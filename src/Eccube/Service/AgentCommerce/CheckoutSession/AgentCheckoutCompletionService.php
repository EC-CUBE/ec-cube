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

namespace Eccube\Service\AgentCommerce\CheckoutSession;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\CheckoutSession;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\CheckoutSessionStatus;
use Eccube\Entity\Order;
use Eccube\Repository\Master\CheckoutSessionStatusRepository;
use Eccube\Service\AgentCommerce\AgentCheckoutPurchaseFlowAdapter;
use Eccube\Service\AgentCommerce\Exception\AgentCheckoutErrorCode;
use Eccube\Service\AgentCommerce\Exception\AgentCheckoutException;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerRegistry;
use Eccube\Service\AgentCommerce\Payment\PaymentOutcome;
use Eccube\Service\AgentCommerce\Payment\PaymentOutcomeStatus;

/**
 * エージェントチェックアウトの complete を「中断 → 再開」する状態機械として実行するオーケストレータ.
 *
 * 設計の核心 (#6777): 追加認証 (EMV-3DS) / 外部サイトへの buyer ハンドオフ (escalation) は
 * エラーでなく complete が返す**正常な中間状態**であり、complete は冪等な単発呼び出しではなく
 * 状態に応じて複数回呼ばれる状態機械である。本サービスは以下を core に集約する:
 *
 * - 在庫引当の保持/回収 (prepare で物理引当 → REQUIRES_ACTION/PENDING では rollback せず保持、
 *   FAILED/期限切れで rollback)
 * - CheckoutSession の正規化ステータス遷移
 * - トランザクション境界 (各 complete 呼び出しを 1 トランザクションで確定。StockReduceProcessor の
 *   悲観ロックに対応するため明示トランザクションで囲む)
 *
 * プロトコル controller (#6776 ACP / #6574 UCP) は本サービスへ委譲し、戻り値
 * {@link AgentCheckoutCompletionResult} を各プロトコルのレスポンスへ変換する。
 */
class AgentCheckoutCompletionService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AgentCheckoutPurchaseFlowAdapter $purchaseFlowAdapter,
        private readonly AgentCheckoutPaymentHandlerRegistry $paymentHandlerRegistry,
        private readonly CheckoutSessionStatusRepository $statusRepository,
        private readonly CustomerResolverInterface $customerResolver,
        private readonly int $escalationExpireMinutes,
    ) {
    }

    /**
     * セッションを確定する (状態機械の単一エントリ。初回 complete・再開 complete の双方を処理).
     *
     * @param array<string, mixed> $paymentData 決済の中立表現 (再開時は authentication_result 等を含む)
     *
     * @throws AgentCheckoutException 確定不能な状態 (終端・Order 未生成等)
     */
    public function complete(CheckoutSession $session, array $paymentData): AgentCheckoutCompletionResult
    {
        $statusId = $session->getStatus()?->getId();

        // 冪等性: 既に完了済なら副作用を再実行せず既存 Order を返す。
        if ($statusId === CheckoutSessionStatus::COMPLETED) {
            return new AgentCheckoutCompletionResult($this->requireStatus(CheckoutSessionStatus::COMPLETED), $session->getOrder());
        }

        // 終端ステータスは確定不能 (プロトコル系エラー)。
        if (in_array($statusId, [CheckoutSessionStatus::CANCELED, CheckoutSessionStatus::EXPIRED], true)) {
            throw new AgentCheckoutException(AgentCheckoutErrorCode::INVALID_SESSION_STATE, 'The checkout session is in a terminal state and cannot be completed.');
        }

        $order = $session->getOrder();
        if ($order === null) {
            throw new AgentCheckoutException(AgentCheckoutErrorCode::INVALID_SESSION_STATE, 'The checkout session has no order to complete.');
        }

        $member = $this->customerResolver->resolve($session);
        $isResume = in_array($statusId, [CheckoutSessionStatus::REQUIRES_ACTION, CheckoutSessionStatus::IN_PROGRESS], true);

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();
        try {
            $result = $this->runStateMachine($session, $order, $member, $paymentData, $isResume);
            $this->entityManager->flush();
            $connection->commit();

            return $result;
        } catch (\Throwable $e) {
            $connection->rollBack();

            throw $e;
        }
    }

    /**
     * 状態機械の本体 (トランザクション内で実行される).
     *
     * @param array<string, mixed> $paymentData
     */
    private function runStateMachine(CheckoutSession $session, Order $order, ?Customer $member, array $paymentData, bool $isResume): AgentCheckoutCompletionResult
    {
        // 初回 complete: 在庫引当・受注番号採番 (PROCESSING のまま)。
        // 再開 complete: 引当は初回で済んでいるため再 prepare しない。
        if (!$isResume) {
            $prepareResult = $this->purchaseFlowAdapter->prepare($order, $member);
            if ($prepareResult->hasError()) {
                // ビジネス系エラー (在庫不足等): 確定せず messages[] を返す (status 据置)。
                return new AgentCheckoutCompletionResult($this->requireCurrentStatus($session), null, $prepareResult->messages);
            }
        }

        $handler = $this->paymentHandlerRegistry->resolveForOrder($order);

        // 決済ハンドラ未登録 (代引・無償等) は与信不要のためそのまま確定する。
        if ($handler === null) {
            return $this->commitOrder($session, $order, $member);
        }

        $outcome = $handler->authorize($order, $paymentData);

        switch ($outcome->status) {
            case PaymentOutcomeStatus::COMPLETED:
                $capture = $handler->capture($order, $paymentData);
                if ($capture->status !== PaymentOutcomeStatus::COMPLETED) {
                    return $this->failOrder($session, $order, $member, $capture);
                }
                $this->mergePaymentData($session, $capture->metadata);

                return $this->commitOrder($session, $order, $member);

            case PaymentOutcomeStatus::REQUIRES_ACTION:
                return $this->holdForAction($session, $outcome, CheckoutSessionStatus::REQUIRES_ACTION);

            case PaymentOutcomeStatus::PENDING:
                return $this->holdForAction($session, $outcome, CheckoutSessionStatus::IN_PROGRESS);

            case PaymentOutcomeStatus::FAILED:
            default:
                return $this->failOrder($session, $order, $member, $outcome);
        }
    }

    /**
     * 注文を確定し completed へ遷移する.
     */
    private function commitOrder(CheckoutSession $session, Order $order, ?Customer $member): AgentCheckoutCompletionResult
    {
        $this->purchaseFlowAdapter->commit($order, $member);
        $session
            ->setOrder($order)
            ->setStatus($this->requireStatus(CheckoutSessionStatus::COMPLETED));

        return new AgentCheckoutCompletionResult($session->getStatus() ?? $this->requireStatus(CheckoutSessionStatus::COMPLETED), $order);
    }

    /**
     * 追加対応待ち (REQUIRES_ACTION) / 非同期確定待ち (IN_PROGRESS) として在庫を引当のまま保持する.
     *
     * 在庫は rollback せず、payment_data と actionData を永続化してリクエスト跨ぎで再開可能にする。
     */
    private function holdForAction(CheckoutSession $session, PaymentOutcome $outcome, int $statusId): AgentCheckoutCompletionResult
    {
        $this->mergePaymentData($session, $outcome->metadata);
        if ($outcome->actionData !== []) {
            $metadata = $session->getMetadata() ?? [];
            $metadata['payment_action'] = $outcome->actionData;
            $session->setMetadata($metadata);
        }
        $this->extendExpiry($session);
        $session->setStatus($this->requireStatus($statusId));

        return new AgentCheckoutCompletionResult($session->getStatus() ?? $this->requireStatus($statusId), null, [], $outcome->actionData);
    }

    /**
     * 決済失敗時: 引当を rollback し、再試行可否でステータスを分岐する.
     *
     * retryable (card declined 等) は ready に戻して再 complete を許し、unrecoverable は canceled とする。
     */
    private function failOrder(CheckoutSession $session, Order $order, ?Customer $member, PaymentOutcome $outcome): AgentCheckoutCompletionResult
    {
        $this->purchaseFlowAdapter->rollback($order, $member);

        $statusId = $outcome->retryable ? CheckoutSessionStatus::READY : CheckoutSessionStatus::CANCELED;
        $session->setStatus($this->requireStatus($statusId));

        $message = new AgentCheckoutMessage(
            AgentCheckoutMessageLevel::ERROR,
            $outcome->errorMessage !== null && $outcome->errorMessage !== '' ? $outcome->errorMessage : (string) $outcome->errorCode,
        );

        return new AgentCheckoutCompletionResult($session->getStatus() ?? $this->requireStatus($statusId), null, [$message]);
    }

    /**
     * payment_data に PSP 参照等をマージする (token はハンドラ側でマスキング済).
     *
     * @param array<string, mixed> $metadata
     */
    private function mergePaymentData(CheckoutSession $session, array $metadata): void
    {
        if ($metadata === []) {
            return;
        }
        $session->setPaymentData(array_merge($session->getPaymentData() ?? [], $metadata));
    }

    /**
     * 在庫を確保したまま再開を待つ期限を再設定する (eccube.yaml の escalation 期限).
     */
    private function extendExpiry(CheckoutSession $session): void
    {
        $session->setExpiresAt((new \DateTime())->modify(sprintf('+%d minutes', $this->escalationExpireMinutes)));
    }

    private function requireCurrentStatus(CheckoutSession $session): CheckoutSessionStatus
    {
        $status = $session->getStatus();
        if ($status === null) {
            throw new AgentCheckoutException(AgentCheckoutErrorCode::INVALID_SESSION_STATE, 'The checkout session has no status.');
        }

        return $status;
    }

    private function requireStatus(int $id): CheckoutSessionStatus
    {
        $status = $this->statusRepository->find($id);
        if ($status === null) {
            throw new \RuntimeException(sprintf('CheckoutSessionStatus #%d is not found. Run the agent-commerce master data migration.', $id));
        }

        return $status;
    }
}
