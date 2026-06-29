<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\AgentCommerce;

use Eccube\Controller\AbstractController;
use Eccube\Entity\CheckoutSession;
use Eccube\Entity\Master\AgentProtocol;
use Eccube\Entity\Master\CheckoutSessionStatus;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CheckoutSessionRepository;
use Eccube\Repository\Master\AgentProtocolRepository;
use Eccube\Repository\Master\CheckoutSessionStatusRepository;
use Eccube\Service\AgentCommerce\Acp\AcpCheckoutSessionMapper;
use Eccube\Service\AgentCommerce\Acp\AcpMessageMapper;
use Eccube\Service\AgentCommerce\AgentCheckoutPurchaseFlowAdapter;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutAddress;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutCompletionService;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutLineItem;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessage;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessageLevel;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutRequest;
use Eccube\Service\AgentCommerce\CheckoutSession\CustomerResolverInterface;
use Eccube\Service\AgentCommerce\Exception\AgentCheckoutErrorCode;
use Eccube\Service\AgentCommerce\Exception\AgentCheckoutException;
use Eccube\Service\AgentCommerce\Exception\IdempotencyConflictException;
use Eccube\Service\AgentCommerce\Idempotency\AgentCheckoutIdempotencyStore;
use Eccube\Service\AgentCommerce\Payment\AgentPaymentMethodResolverInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * ACP (Agentic Commerce Protocol) checkout の REST エンドポイント.
 *
 * 2026-04-17 の Agentic Checkout を 5 エンドポイントで提供する (正準パスは `/checkout_sessions`):
 * - POST   /acp/checkout_sessions            (create)   -> 201
 * - POST   /acp/checkout_sessions/{id}       (update)   -> 200
 * - GET    /acp/checkout_sessions/{id}       (get)      -> 200
 * - POST   /acp/checkout_sessions/{id}/complete         -> 200 (order)
 * - POST   /acp/checkout_sessions/{id}/cancel           -> 200 (canceled) / 405 (確定済)
 *
 * 設計:
 * - セッションレスなエージェント向けに {@link CheckoutSession} で Cart/Order を束ねる。
 * - 見積/確定は通常購入と同一の shopping flow を {@link AgentCheckoutPurchaseFlowAdapter} 経由で再利用。
 * - complete は「中断 → 再開」状態機械 ({@link AgentCheckoutCompletionService})。3DS は
 *   `authentication_required` という正常な中間状態として返る (エラーではない)。
 * - **2 系統エラー**: プロトコル系は HTTP 4xx/5xx + flat Error ({type, code, message, param?})、
 *   ビジネス系は HTTP 200 + messages[]。
 * - インバウンド認証は OAuth2 Bearer (`acp:checkout` scope、{@link AcpAuthenticationSubscriber} が適用)。
 * - **Idempotency-Key は全 POST で必須** (欠落=400 / 異内容=422 / 処理中=409)。
 * - `acp_checkout_enabled` が false のときは NotFoundHttpException (ルートは削除しない既存パターン)。
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6776
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP openapi.agentic_checkout.yaml (2026-04-17)
 */
class AcpCheckoutController extends AbstractController
{
    public function __construct(
        private readonly BaseInfoRepository $baseInfoRepository,
        private readonly CheckoutSessionRepository $checkoutSessionRepository,
        private readonly AgentProtocolRepository $agentProtocolRepository,
        private readonly CheckoutSessionStatusRepository $statusRepository,
        private readonly AgentCheckoutPurchaseFlowAdapter $purchaseFlowAdapter,
        private readonly AcpCheckoutSessionMapper $mapper,
        private readonly AcpMessageMapper $messageMapper,
        private readonly AgentCheckoutIdempotencyStore $idempotencyStore,
        private readonly CustomerResolverInterface $customerResolver,
        private readonly AgentCheckoutCompletionService $completionService,
        private readonly AgentPaymentMethodResolverInterface $paymentMethodResolver,
    ) {
    }

    #[Route(path: '/acp/checkout_sessions', name: 'acp_checkout_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->assertEnabled();

        return $this->withIdempotency($request, function () use ($request): array {
            try {
                $payload = $this->decodeBody($request);
                $checkoutRequest = $this->mapper->toCheckoutRequest($payload, $this->resolveAgentId($request));

                $session = (new CheckoutSession())
                    ->setSessionId($this->generateSessionId())
                    ->setProtocol($this->agentProtocolRepository->find(AgentProtocol::ACP))
                    ->setStatus($this->findStatus(CheckoutSessionStatus::INCOMPLETE))
                    ->setCurrencyCode($checkoutRequest->currencyCode)
                    ->setAgentId($checkoutRequest->agentId)
                    ->setMetadata(['line_items' => $this->lineItemsMetadata($checkoutRequest)]);

                return ['status' => 201, 'body' => $this->buildAndPersist($session, $checkoutRequest)];
            } catch (AgentCheckoutException $e) {
                return $this->protocolError(400, $e->getErrorCode()->value, $e->getMessage());
            }
        });
    }

    #[Route(path: '/acp/checkout_sessions/{sessionId}', name: 'acp_checkout_update', methods: ['POST'])]
    public function update(Request $request, string $sessionId): JsonResponse
    {
        $this->assertEnabled();
        $session = $this->findSession($request, $sessionId);

        return $this->withIdempotency($request, function () use ($request, $session): array {
            try {
                $payload = $this->decodeBody($request);
                $checkoutRequest = $this->mapper->toCheckoutRequest($payload, $this->resolveAgentId($request));

                $session->setMetadata(array_merge($session->getMetadata() ?? [], ['line_items' => $this->lineItemsMetadata($checkoutRequest)]));

                return ['status' => 200, 'body' => $this->buildAndPersist($session, $checkoutRequest)];
            } catch (AgentCheckoutException $e) {
                return $this->protocolError(400, $e->getErrorCode()->value, $e->getMessage());
            }
        });
    }

    #[Route(path: '/acp/checkout_sessions/{sessionId}', name: 'acp_checkout_get', methods: ['GET'])]
    public function get(Request $request, string $sessionId): JsonResponse
    {
        $this->assertEnabled();
        $session = $this->findSession($request, $sessionId);

        $order = $session->getOrder();
        if ($order !== null) {
            return new JsonResponse($this->mapper->buildResponseFromOrder($session, $order, []), Response::HTTP_OK);
        }

        return new JsonResponse($this->mapper->buildProvisionalResponse($session, $this->provisionalRequestFromSession($session), []), Response::HTTP_OK);
    }

    #[Route(path: '/acp/checkout_sessions/{sessionId}/complete', name: 'acp_checkout_complete', methods: ['POST'])]
    public function complete(Request $request, string $sessionId): JsonResponse
    {
        $this->assertEnabled();
        $session = $this->findSession($request, $sessionId);

        return $this->withIdempotency($request, function () use ($request, $session): array {
            $order = $session->getOrder();
            if ($order === null) {
                return $this->protocolError(400, 'invalid_session_state', 'The checkout session is not ready for completion.');
            }

            try {
                // decodeBody は不正 JSON で AgentCheckoutException を投げるため try 内に置き、
                // create/update と同じく 400 プロトコルエラーへ変換する (500 にしない)。
                $payload = $this->decodeBody($request);
                $paymentData = is_array($payload['payment_data'] ?? null) ? $payload['payment_data'] : [];

                // 3DS: authentication_required 状態で authentication_result 無しの再開 complete は 400 requires_3ds。
                if ($this->isAuthenticationPending($session) && !isset($paymentData['authentication_result'])) {
                    return $this->protocolError(400, 'requires_3ds', 'This checkout session requires issuer authentication. The request must include "authentication_result".', '$.authentication_result');
                }

                // エージェントが選んだ決済ハンドラ (payment_data.handler_id) に対応する Payment を割り当て、
                // 状態機械の resolveForOrder が同一ハンドラへ解決することを保証する (sort_no 非依存)。
                $handlerId = is_string($paymentData['handler_id'] ?? null) ? $paymentData['handler_id'] : null;
                if ($handlerId !== null && $this->paymentMethodResolver->resolve($order, $handlerId) === null) {
                    return $this->protocolError(400, 'payment_handler_not_found', sprintf('No enabled payment method supports the requested payment handler "%s".', $handlerId), '$.payment_data.handler_id');
                }

                // complete は状態機械 (#6777)。3DS/escalation はエラーでなく requires_action 等の中間状態として返る。
                $result = $this->completionService->complete($session, $paymentData);
            } catch (AgentCheckoutException $e) {
                return $this->protocolError(400, $e->getErrorCode()->value, $e->getMessage());
            }

            $acpMessages = $this->messageMapper->toAcpMessages($result->messages);

            return ['status' => 200, 'body' => $this->mapper->buildResponseFromOrder($session, $order, $acpMessages, $result->actionData)];
        });
    }

    #[Route(path: '/acp/checkout_sessions/{sessionId}/cancel', name: 'acp_checkout_cancel', methods: ['POST'])]
    public function cancel(Request $request, string $sessionId): JsonResponse
    {
        $this->assertEnabled();
        $session = $this->findSession($request, $sessionId);

        return $this->withIdempotency($request, function () use ($session): array {
            $statusId = $session->getStatus()?->getId();
            if (in_array($statusId, [CheckoutSessionStatus::COMPLETED, CheckoutSessionStatus::CANCELED], true)) {
                // 既に完了/取消済のセッションは取消不可 (ACP: 405 Not Allowed)。
                return $this->protocolError(405, 'not_allowed', 'A completed or canceled checkout session cannot be canceled.');
            }

            $session->setStatus($this->findStatus(CheckoutSessionStatus::CANCELED));
            $this->entityManager->flush();

            $order = $session->getOrder();
            $body = $order !== null
                ? $this->mapper->buildResponseFromOrder($session, $order, [])
                : $this->mapper->buildProvisionalResponse($session, $this->provisionalRequestFromSession($session), []);

            return ['status' => 200, 'body' => $body];
        });
    }

    /**
     * 中立リクエストからセッションを確定 (見積) し、レスポンスボディを返す.
     *
     * 住所未確定なら暫定見積 + not_ready_for_payment、住所ありなら shopping flow で再計算する。
     *
     * @return array<string, mixed>
     */
    private function buildAndPersist(CheckoutSession $session, AgentCheckoutRequest $checkoutRequest): array
    {
        if ($checkoutRequest->buyer === null) {
            $session->setStatus($this->findStatus(CheckoutSessionStatus::INCOMPLETE));
            $this->entityManager->persist($session);
            $this->entityManager->flush();

            $messages = $this->messageMapper->toAcpMessages([
                new AgentCheckoutMessage(
                    AgentCheckoutMessageLevel::ERROR,
                    'Shipping address is required to calculate shipping and complete checkout.',
                ),
            ]);

            return $this->mapper->buildProvisionalResponse($session, $checkoutRequest, $messages);
        }

        $result = $this->purchaseFlowAdapter->buildOrder($checkoutRequest, $this->customerResolver->resolve($session));
        $order = $result->order;

        $session
            ->setOrder($order)
            ->setBuyerData($this->buyerData($checkoutRequest->buyer))
            ->setStatus($this->findStatus($result->hasError() ? CheckoutSessionStatus::INCOMPLETE : CheckoutSessionStatus::READY));
        $this->entityManager->persist($session);
        $this->entityManager->flush();

        return $this->mapper->buildResponseFromOrder($session, $order, $this->messageMapper->toAcpMessages($result->messages));
    }

    /**
     * Idempotency-Key を考慮してハンドラを実行し JsonResponse を返す.
     *
     * ACP は全 POST で Idempotency-Key が必須 (欠落=400)。同一キーの異内容=422、処理中=409。
     *
     * @param callable(): array{status: int, body: array<string, mixed>} $handler
     */
    private function withIdempotency(Request $request, callable $handler): JsonResponse
    {
        $key = $request->headers->get('Idempotency-Key');
        if ($key === null || $key === '') {
            return new JsonResponse(
                ['type' => 'invalid_request', 'code' => 'idempotency_key_required', 'message' => 'Idempotency-Key header is required on all POST requests.'],
                Response::HTTP_BAD_REQUEST,
            );
        }

        // 認証済みクライアント (AcpAuthenticationSubscriber が設定) を主体として名前空間化し、越境リプレイを防ぐ。
        $subject = $request->attributes->get('acp_client_id');
        $subject = is_string($subject) ? $subject : null;
        $requestHash = $this->idempotencyStore->hashRequest([
            'path' => $request->getPathInfo(),
            'method' => $request->getMethod(),
            'body' => $request->getContent(),
            'agent' => $subject,
        ]);

        try {
            $result = $this->idempotencyStore->execute($key, $requestHash, $handler, $subject);
        } catch (IdempotencyConflictException $e) {
            if ($e->isInFlight()) {
                return new JsonResponse(
                    ['type' => 'invalid_request', 'code' => 'idempotency_in_flight', 'message' => 'A request with this Idempotency-Key is currently being processed.'],
                    Response::HTTP_CONFLICT,
                );
            }

            return new JsonResponse(
                ['type' => 'invalid_request', 'code' => 'idempotency_conflict', 'message' => 'Idempotency-Key has already been used with a different request body.'],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $response = new JsonResponse($result['body'], $result['status']);
        if ($result['replayed']) {
            // リプレイされたレスポンスである旨を広告する (SHOULD)。
            $response->headers->set('Idempotent-Replayed', 'true');
        }

        return $response;
    }

    /**
     * プロトコル系エラー (flat Error) を組み立てる.
     *
     * @return array{status: int, body: array<string, mixed>}
     */
    private function protocolError(int $status, string $code, string $message, ?string $param = null): array
    {
        $body = ['type' => 'invalid_request', 'code' => $code, 'message' => $message];
        if ($param !== null) {
            $body['param'] = $param;
        }

        return ['status' => $status, 'body' => $body];
    }

    private function assertEnabled(): void
    {
        if (!$this->baseInfoRepository->get()->isAcpCheckoutEnabled()) {
            throw new NotFoundHttpException('ACP checkout is not enabled.');
        }
    }

    private function findSession(Request $request, string $sessionId): CheckoutSession
    {
        $session = $this->checkoutSessionRepository->findOneBySessionId($sessionId);
        if ($session === null
            || $session->getProtocol()?->getId() !== AgentProtocol::ACP
            || $session->getAgentId() !== $this->resolveAgentId($request)
        ) {
            // 他プロトコル/他マーチャント/他エージェントのセッションへの越境アクセスを遮断する
            // (主体不一致は存在を秘匿して 404)。session_id は乱数で列挙不可だが、漏洩時の越境も防ぐ。
            throw new NotFoundHttpException(sprintf('Checkout session "%s" was not found.', $sessionId));
        }

        return $session;
    }

    private function findStatus(int $id): CheckoutSessionStatus
    {
        /** @var CheckoutSessionStatus $status */
        $status = $this->statusRepository->find($id);

        return $status;
    }

    /**
     * セッションが追加認証 (3DS / authentication_required) の再開待ちか.
     *
     * REQUIRES_ACTION かつ metadata の payment_action が 3DS を示す場合に true。
     */
    private function isAuthenticationPending(CheckoutSession $session): bool
    {
        if ($session->getStatus()?->getId() !== CheckoutSessionStatus::REQUIRES_ACTION) {
            return false;
        }

        $action = $session->getMetadata()['payment_action'] ?? [];
        if (!is_array($action)) {
            return false;
        }

        return ($action['intervention'] ?? null) === '3ds'
            || ($action['type'] ?? null) === '3ds'
            || ($action['authentication_required'] ?? false) === true
            || isset($action['authentication_metadata']);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeBody(Request $request): array
    {
        $content = $request->getContent();
        if ($content === '') {
            return [];
        }

        try {
            $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new AgentCheckoutException(AgentCheckoutErrorCode::EMPTY_LINE_ITEMS, 'Malformed JSON request body.', $e);
        }

        if (!is_array($decoded)) {
            return [];
        }

        $normalized = [];
        foreach ($decoded as $key => $value) {
            $normalized[(string) $key] = $value;
        }

        return $normalized;
    }

    private function generateSessionId(): string
    {
        return 'acp_cs_'.bin2hex(random_bytes(16));
    }

    private function resolveAgentId(Request $request): ?string
    {
        $clientId = $request->attributes->get('acp_client_id');

        return is_string($clientId) ? $clientId : null;
    }

    /**
     * @return list<array{pc: int, qty: int}>
     */
    private function lineItemsMetadata(AgentCheckoutRequest $checkoutRequest): array
    {
        return array_map(
            static fn (AgentCheckoutLineItem $item): array => ['pc' => $item->productClassId, 'qty' => $item->quantity],
            $checkoutRequest->lineItems
        );
    }

    /**
     * GET/cancel で Order 未生成のセッションを暫定表示するため、metadata から明細を復元する.
     */
    private function provisionalRequestFromSession(CheckoutSession $session): AgentCheckoutRequest
    {
        $lineItems = [];
        $stored = $session->getMetadata()['line_items'] ?? [];
        if (is_array($stored)) {
            foreach ($stored as $entry) {
                if (is_array($entry) && isset($entry['pc'], $entry['qty'])) {
                    $lineItems[] = new AgentCheckoutLineItem((int) $entry['pc'], (int) $entry['qty']);
                }
            }
        }

        return new AgentCheckoutRequest($lineItems, null, $session->getCurrencyCode(), AgentProtocol::ACP);
    }

    /**
     * 中立住所 DTO を buyer_data (echo 用) へ写す.
     *
     * @return array<string, mixed>
     */
    private function buyerData(AgentCheckoutAddress $address): array
    {
        return array_filter([
            'family_name' => $address->name01,
            'given_name' => $address->name02,
            'email' => $address->email,
            'phone' => $address->phoneNumber,
        ], static fn ($value): bool => $value !== null);
    }
}
