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
use Eccube\Entity\Order;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CheckoutSessionRepository;
use Eccube\Repository\Master\AgentProtocolRepository;
use Eccube\Repository\Master\CheckoutSessionStatusRepository;
use Eccube\Service\AgentCommerce\AgentCheckoutPurchaseFlowAdapter;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutAddress;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutCompletionResult;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutCompletionService;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutLineItem;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutRequest;
use Eccube\Service\AgentCommerce\CheckoutSession\CustomerResolverInterface;
use Eccube\Service\AgentCommerce\Exception\AgentCheckoutErrorCode;
use Eccube\Service\AgentCommerce\Exception\AgentCheckoutException;
use Eccube\Service\AgentCommerce\Exception\IdempotencyConflictException;
use Eccube\Service\AgentCommerce\Idempotency\AgentCheckoutIdempotencyStore;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerRegistry;
use Eccube\Service\AgentCommerce\Ucp\UcpCheckoutSessionMapper;
use Eccube\Service\AgentCommerce\Ucp\UcpMessageMapper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * UCP (Universal Commerce Protocol) checkout の REST エンドポイント.
 *
 * v2026-04-08 の checkout capability を 5 エンドポイントで提供する:
 * - POST   /ucp/checkout-sessions            (create)        -> 201
 * - GET    /ucp/checkout-sessions/{id}       (get)           -> 200
 * - PUT    /ucp/checkout-sessions/{id}       (update)        -> 200 (全置換)
 * - POST   /ucp/checkout-sessions/{id}/complete             -> 200 (order)
 * - POST   /ucp/checkout-sessions/{id}/cancel               -> 200 (canceled)
 *
 * 設計:
 * - セッションレスなエージェント向けに {@link CheckoutSession} で Cart/Order を束ねる。
 * - 見積/確定は通常購入と同一の shopping flow を {@link AgentCheckoutPurchaseFlowAdapter} 経由で再利用。
 * - **2 系統エラー**: プロトコル系は HTTP 4xx/5xx + Error、ビジネス系は HTTP 200 + messages[]。
 * - インバウンド認証は RFC 9421 署名 (UcpSignatureSubscriber が適用、api4 非依存)。
 * - `ucp_checkout_enabled` が false のときは NotFoundHttpException (ルートは削除しない既存パターン)。
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6574
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP checkout-rest.md
 */
class UcpCheckoutController extends AbstractController
{
    public function __construct(
        private readonly BaseInfoRepository $baseInfoRepository,
        private readonly CheckoutSessionRepository $checkoutSessionRepository,
        private readonly AgentProtocolRepository $agentProtocolRepository,
        private readonly CheckoutSessionStatusRepository $statusRepository,
        private readonly AgentCheckoutPurchaseFlowAdapter $purchaseFlowAdapter,
        private readonly UcpCheckoutSessionMapper $mapper,
        private readonly UcpMessageMapper $messageMapper,
        private readonly AgentCheckoutIdempotencyStore $idempotencyStore,
        private readonly AgentCheckoutPaymentHandlerRegistry $paymentHandlerRegistry,
        private readonly CustomerResolverInterface $customerResolver,
        private readonly AgentCheckoutCompletionService $completionService,
    ) {
    }

    #[Route(path: '/ucp/checkout-sessions', name: 'ucp_checkout_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->assertEnabled();

        return $this->withIdempotency($request, function () use ($request): array {
            try {
                $payload = $this->decodeBody($request);
                $checkoutRequest = $this->mapper->toCheckoutRequest($payload, $this->resolveAgentId($request));

                $session = (new CheckoutSession())
                    ->setSessionId($this->generateSessionId())
                    ->setProtocol($this->agentProtocolRepository->find(AgentProtocol::UCP))
                    ->setStatus($this->findStatus(CheckoutSessionStatus::INCOMPLETE))
                    ->setCurrencyCode($checkoutRequest->currencyCode)
                    ->setMetadata(['line_items' => $this->lineItemsMetadata($checkoutRequest)]);

                return ['status' => 201, 'body' => $this->buildAndPersist($session, $checkoutRequest)];
            } catch (AgentCheckoutException $e) {
                return $this->protocolError(422, $e->getErrorCode()->value, $e->getMessage());
            }
        });
    }

    #[Route(path: '/ucp/checkout-sessions/{sessionId}', name: 'ucp_checkout_get', methods: ['GET'])]
    public function get(string $sessionId): JsonResponse
    {
        $this->assertEnabled();
        $session = $this->findSession($sessionId);

        $order = $session->getOrder();
        if ($order !== null) {
            return new JsonResponse($this->mapper->buildResponseFromOrder($session, $order, []), Response::HTTP_OK);
        }

        return new JsonResponse($this->mapper->buildProvisionalResponse($session, $this->provisionalRequestFromSession($session), []), Response::HTTP_OK);
    }

    #[Route(path: '/ucp/checkout-sessions/{sessionId}', name: 'ucp_checkout_update', methods: ['PUT'])]
    public function update(Request $request, string $sessionId): JsonResponse
    {
        $this->assertEnabled();
        $session = $this->findSession($sessionId);

        return $this->withIdempotency($request, function () use ($request, $session): array {
            try {
                $payload = $this->decodeBody($request);
                $checkoutRequest = $this->mapper->toCheckoutRequest($payload, $this->resolveAgentId($request));

                // 全置換セマンティクス: 明細スナップショットを差し替える。
                $session->setMetadata(array_merge($session->getMetadata() ?? [], ['line_items' => $this->lineItemsMetadata($checkoutRequest)]));

                return ['status' => 200, 'body' => $this->buildAndPersist($session, $checkoutRequest)];
            } catch (AgentCheckoutException $e) {
                return $this->protocolError(422, $e->getErrorCode()->value, $e->getMessage());
            }
        });
    }

    #[Route(path: '/ucp/checkout-sessions/{sessionId}/complete', name: 'ucp_checkout_complete', methods: ['POST'])]
    public function complete(Request $request, string $sessionId): JsonResponse
    {
        $this->assertEnabled();
        $session = $this->findSession($sessionId);

        return $this->withIdempotency($request, function () use ($request, $session): array {
            $order = $session->getOrder();
            if ($order === null) {
                // Order が未構築のセッションは確定不能 (プロトコル系エラー 4xx)。
                return $this->protocolError(422, 'invalid_session_state', 'The checkout session is not ready for completion.');
            }

            try {
                $payload = $this->decodeBody($request);
                // UCP は payment.instruments[].handler_id で決済ハンドラを解決し、クレデンシャルを
                // ゲートウェイトークンへ交換する。交換後の中立データを状態機械へ渡す。
                $paymentData = $this->resolvePaymentData($payload);
                // complete は「中断→再開」状態機械 (#6777)。追加認証 (3DS/escalation) は
                // エラーでなく requires_action 等の中間状態として返る。在庫引当の保持/回収・
                // トランザクション境界・与信→売上→確定の順序は CompletionService が担う。
                $result = $this->completionService->complete($session, $paymentData);
            } catch (AgentCheckoutException $e) {
                return $this->protocolError(422, $e->getErrorCode()->value, $e->getMessage());
            }

            $ucpMessages = $this->messageMapper->toUcpMessages($result->messages);
            $continueUrl = $this->continueUrlFor($session, $result);

            return ['status' => 200, 'body' => $this->mapper->buildResponseFromOrder($session, $order, $ucpMessages, $continueUrl)];
        });
    }

    #[Route(path: '/ucp/checkout-sessions/{sessionId}/cancel', name: 'ucp_checkout_cancel', methods: ['POST'])]
    public function cancel(Request $request, string $sessionId): JsonResponse
    {
        $this->assertEnabled();
        $session = $this->findSession($sessionId);

        return $this->withIdempotency($request, function () use ($session): array {
            if ($session->getStatus()?->getId() === CheckoutSessionStatus::COMPLETED) {
                return $this->protocolError(422, 'invalid_session_state', 'A completed checkout session cannot be canceled.');
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
     * 住所未確定なら暫定見積 + incomplete、住所ありなら shopping flow で再計算し ready/incomplete。
     *
     * @return array<string, mixed>
     */
    private function buildAndPersist(CheckoutSession $session, AgentCheckoutRequest $checkoutRequest): array
    {
        if ($checkoutRequest->buyer === null) {
            $session->setStatus($this->findStatus(CheckoutSessionStatus::INCOMPLETE));
            $this->entityManager->persist($session);
            $this->entityManager->flush();

            $messages = [[
                'type' => 'error',
                'severity' => 'recoverable',
                'content' => 'Shipping address is required to calculate shipping and complete checkout.',
                'content_type' => 'plain',
            ]];

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

        return $this->mapper->buildResponseFromOrder($session, $order, $this->messageMapper->toUcpMessages($result->messages));
    }

    /**
     * payment.instruments[].handler_id で UCP 決済ハンドラを解決し、クレデンシャルをゲートウェイ
     * トークンへ交換した中立データを返す.
     *
     * 与信/売上 (authorize/capture) は状態機械 (CompletionService) が実行するため、ここでは交換のみ行う。
     * ハンドラ未登録 (代引・無償等) の場合は空配列を返し、状態機械が与信不要として確定する。
     *
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function resolvePaymentData(array $payload): array
    {
        $instrument = $payload['payment']['instruments'][0] ?? null;
        if (!is_array($instrument)) {
            return [];
        }

        $handlerId = is_string($instrument['handler_id'] ?? null) ? $instrument['handler_id'] : null;
        if ($handlerId === null) {
            return [];
        }

        $handler = $this->paymentHandlerRegistry->resolveUcpByHandlerId($handlerId);
        if ($handler === null) {
            return [];
        }

        $credential = is_array($instrument['credential'] ?? null) ? $instrument['credential'] : [];

        return $handler->exchangePaymentToken($credential);
    }

    /**
     * requires_action (escalation) のとき buyer ハンドオフ用の continue_url を解決する.
     *
     * UCP では `requires_escalation` 時に continue_url が MUST。決済ハンドラが actionData で
     * 提供すればそれを優先し、無ければセッション取得 URL を絶対 HTTPS で生成する
     * (`continue_url` は絶対 HTTPS URL でなければならない)。それ以外の状態では null。
     */
    private function continueUrlFor(CheckoutSession $session, AgentCheckoutCompletionResult $result): ?string
    {
        if ($session->getStatus()?->getId() !== CheckoutSessionStatus::REQUIRES_ACTION) {
            return null;
        }

        $fromHandler = $result->actionData['continue_url'] ?? null;
        if (is_string($fromHandler) && str_starts_with($fromHandler, 'https://')) {
            return $fromHandler;
        }

        $url = $this->generateUrl('ucp_checkout_get', ['sessionId' => $session->getSessionId()], UrlGeneratorInterface::ABSOLUTE_URL);

        // continue_url は絶対 HTTPS URL でなければならない (RequestContext が http のときも https に強制)。
        return str_starts_with($url, 'http://') ? substr_replace($url, 'https://', 0, 7) : $url;
    }

    /**
     * Idempotency-Key を考慮してハンドラを実行し JsonResponse を返す.
     *
     * @param callable(): array{status: int, body: array<string, mixed>} $handler
     */
    private function withIdempotency(Request $request, callable $handler): JsonResponse
    {
        $key = $request->headers->get('Idempotency-Key');
        // 認証済みエージェント (UcpSignatureSubscriber が検証時に設定) を主体として名前空間化し、
        // 別エージェントによる越境リプレイを防ぐ。
        $subject = $request->attributes->get('ucp_agent_profile');
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
            return new JsonResponse(['code' => 'idempotency_conflict', 'content' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return new JsonResponse($result['body'], $result['status']);
    }

    /**
     * @return array{status: int, body: array<string, mixed>}
     */
    private function protocolError(int $status, string $code, string $content): array
    {
        return ['status' => $status, 'body' => ['code' => $code, 'content' => $content]];
    }

    private function assertEnabled(): void
    {
        if (!$this->baseInfoRepository->get()->isUcpCheckoutEnabled()) {
            throw new NotFoundHttpException('UCP checkout is not enabled.');
        }
    }

    private function findSession(string $sessionId): CheckoutSession
    {
        $session = $this->checkoutSessionRepository->findOneBySessionId($sessionId);
        if ($session === null || $session->getProtocol()?->getId() !== AgentProtocol::UCP) {
            // 他プロトコル/他マーチャントのセッションへの越境アクセスを遮断する。
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

        // JSON オブジェクトのキーを文字列に正規化する (トップレベルが配列のとき int キーになり得る)。
        $normalized = [];
        foreach ($decoded as $key => $value) {
            $normalized[(string) $key] = $value;
        }

        return $normalized;
    }

    private function generateSessionId(): string
    {
        return 'ucp_cs_'.bin2hex(random_bytes(16));
    }

    private function resolveAgentId(Request $request): ?string
    {
        // 署名検証済みのエージェント profile URL があれば agent_id として用いる。
        $verified = $request->attributes->get('ucp_agent_profile');

        return is_string($verified) ? $verified : $request->headers->get('UCP-Agent');
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

        return new AgentCheckoutRequest($lineItems, null, $session->getCurrencyCode(), AgentProtocol::UCP);
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
