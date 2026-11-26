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

use Eccube\Entity\CheckoutSession;
use Eccube\Entity\Master\CheckoutSessionStatus;
use Eccube\Repository\CheckoutSessionRepository;
use Eccube\Repository\Master\CheckoutSessionStatusRepository;
use Eccube\Service\AgentCommerce\AgentCheckoutPurchaseFlowAdapter;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerRegistry;
use Eccube\Service\AgentCommerce\Payment\CancellablePaymentHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * CheckoutSession を終端 (canceled / expired) へ遷移させ、中断中に引き当てた在庫を回収する.
 *
 * complete が REQUIRES_ACTION / IN_PROGRESS を返したセッションは、在庫を引き当てたまま再開を待つ。
 * ステータスを書き換えるだけでは在庫が戻らないため、終端へ遷移させる経路 (cancel・期限切れ) は
 * すべて本クラスを通す。
 *
 * 各メソッドは {@link AgentCheckoutSessionLocker::transactional()} の内側で呼ぶこと
 * (PurchaseFlow の rollback は在庫行の悲観ロックを取るため、トランザクションが要る)。
 */
class AgentCheckoutSessionTerminator
{
    /** 終端ステータス. */
    public const TERMINAL_STATUSES = [
        CheckoutSessionStatus::COMPLETED,
        CheckoutSessionStatus::CANCELED,
        CheckoutSessionStatus::EXPIRED,
    ];

    /** 在庫を引き当てたまま中断しているステータス. */
    public const HELD_STATUSES = [
        CheckoutSessionStatus::REQUIRES_ACTION,
        CheckoutSessionStatus::IN_PROGRESS,
    ];

    public function __construct(
        private readonly AgentCheckoutPurchaseFlowAdapter $purchaseFlowAdapter,
        private readonly AgentCheckoutPaymentHandlerRegistry $paymentHandlerRegistry,
        private readonly CheckoutSessionStatusRepository $statusRepository,
        private readonly CheckoutSessionRepository $checkoutSessionRepository,
        private readonly CustomerResolverInterface $customerResolver,
        private readonly AgentCheckoutSessionLocker $locker,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function isTerminal(CheckoutSession $session): bool
    {
        return in_array($session->getStatus()?->getId(), self::TERMINAL_STATUSES, true);
    }

    /**
     * 期限を過ぎた非終端のセッションか.
     */
    public function isOverdue(CheckoutSession $session, ?\DateTime $now = null): bool
    {
        return !$this->isTerminal($session) && $session->isExpired($now ?? new \DateTime());
    }

    /**
     * 取消 (canceled) へ遷移させる. 中断中なら在庫を回収し、PSP の取引を取り消す.
     */
    public function cancel(CheckoutSession $session): void
    {
        $this->release($session);
        $session->setStatus($this->requireStatus(CheckoutSessionStatus::CANCELED));
    }

    /**
     * 期限切れ (expired) へ遷移させる. 中断中なら在庫を回収し、PSP の取引を取り消す.
     */
    public function expire(CheckoutSession $session): void
    {
        $this->release($session);
        $session->setStatus($this->requireStatus(CheckoutSessionStatus::EXPIRED));
    }

    /**
     * 期限を過ぎたセッションを最大 $limit 件まで expired へ遷移させ、遷移させた件数を返す.
     *
     * セッションごとに行ロックを取り、ロック後に期限と状態を再判定する (並行する complete が
     * 先に確定させていれば何もしない)。cron で動かすコマンドと、CLI を使えない環境向けの
     * 管理画面から呼ぶ。
     *
     * フロント (エージェント) のリクエストからは呼ばないこと。リクエストのたびに期限切れの検索と
     * 在庫行のロックが走り、アクセスの多いサイトでは負荷になる上、外部から回収処理を誘発できてしまう
     * (EC-CUBE 2 系の cancelPendingOrder の教訓。EC-CUBE/ec-cube2#303)。
     */
    public function expireOverdue(\DateTime $now, int $limit): int
    {
        $count = 0;
        foreach ($this->checkoutSessionRepository->findExpired($now, $limit) as $session) {
            $expired = $this->locker->transactional($session, function (CheckoutSession $locked) use ($now): bool {
                if (!$this->isOverdue($locked, $now)) {
                    return false;
                }
                $this->expire($locked);

                return true;
            });
            if ($expired) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * 中断中 (在庫引当済み) のセッションの在庫を回収し、取消に対応するハンドラなら PSP の取引も取り消す.
     */
    private function release(CheckoutSession $session): void
    {
        if (!in_array($session->getStatus()?->getId(), self::HELD_STATUSES, true)) {
            return;
        }

        $order = $session->getOrder();
        if ($order === null) {
            return;
        }

        $this->purchaseFlowAdapter->rollback($order, $this->customerResolver->resolve($session));

        // 取引を取り消すのは、中断したときのハンドラ (保持した PSP 参照はそのハンドラの取引)。
        // 記録が無い場合 (記録を始める前に中断したセッション) は、支払方法から選ぶ。
        $heldHandlerId = $session->getMetadata()[AgentCheckoutCompletionService::PAYMENT_HANDLER_METADATA_KEY] ?? null;
        $handler = is_string($heldHandlerId) && $heldHandlerId !== ''
            ? $this->paymentHandlerRegistry->resolveByHandlerId($heldHandlerId)
            : $this->paymentHandlerRegistry->resolveForOrder($order);
        if (!$handler instanceof CancellablePaymentHandlerInterface) {
            return;
        }

        try {
            $handler->cancel($order, AgentCheckoutCompletionService::heldPaymentReference($session));
        } catch (\Throwable $e) {
            // 在庫の回収とステータス遷移は取消の成否に関わらず行う。取引は PSP 側の照会で解放する。
            $this->logger->error('The agent payment handler threw during cancel.', ['exception' => $e, 'order_no' => $order->getOrderNo()]);
        }
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
