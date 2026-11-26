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
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerInterface;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerRegistry;
use Eccube\Service\AgentCommerce\Payment\AgentPaymentMethodResolverInterface;
use Eccube\Service\AgentCommerce\Payment\PaymentOutcome;
use Eccube\Service\AgentCommerce\Payment\PaymentOutcomeStatus;
use Eccube\Service\Payment\Method\Cash;
use Psr\Log\LoggerInterface;

/**
 * エージェントチェックアウトの complete を「中断 → 再開」する状態機械として実行するオーケストレータ.
 *
 * 設計の核心 (#6777): 追加認証 (EMV-3DS) / 外部サイトへの buyer ハンドオフ (escalation) は
 * エラーでなく complete が返す**正常な中間状態**であり、complete は冪等な単発呼び出しではなく
 * 状態に応じて複数回呼ばれる状態機械である。本サービスは以下を core に集約する:
 *
 * - 在庫引当の保持/回収 (prepare で物理引当 → REQUIRES_ACTION/PENDING では rollback せず保持、
 *   FAILED/期限切れで rollback)
 * - capture の呼び出し制御 (authorize が AUTHORIZED を返したときだけ capture を呼ぶ。売上確定済を表す
 *   COMPLETED に対しては呼ばない = auto-capture 型 PSP での二重売上を防ぐ)
 * - PSP 参照 (`transaction_id` と metadata) の保持と、再開 complete でのハンドラへの引き渡し
 * - CheckoutSession の正規化ステータス遷移
 * - トランザクション境界 (各 complete 呼び出しを 1 トランザクションで確定。StockReduceProcessor の
 *   悲観ロックに対応するため明示トランザクションで囲み、セッション行をロックして並行 complete を直列化する)
 * - 期限切れの回収 (在庫確保期限を過ぎたセッションは在庫を戻して expired にする)
 *
 * ## 入口ステータスと再開の扱い
 *
 * complete に入れる非終端ステータスは 3 つある (`INCOMPLETE` は住所未確定・見積エラーありのため
 * プロトコル系エラーで拒否する)。**保持した PSP 参照をハンドラへ渡すのは
 * 「在庫を引当てたまま中断した」2 状態からの再開時だけ**である。
 *
 * | 入口                | prepare | 保持済み参照 | 意味                                       |
 * |---------------------|---------|--------------|--------------------------------------------|
 * | `READY`             | 実行    | 渡さない     | 初回、または**失敗後の再試行** (新規与信)  |
 * | `REQUIRES_ACTION`   | skip    | 渡す         | 追加認証 (3DS) からの再開                  |
 * | `IN_PROGRESS`       | skip    | 渡す         | 非同期確定からの再開                       |
 *
 * READY で参照を渡さないのは、与信拒否で ready に戻った場合に**死んだ取引の識別子**を掴ませないため
 * (別カードでの再試行が、拒否済み取引の続行になってしまう)。失敗時に保持する参照は照会・監査用であり、
 * 再試行の入力ではない。
 *
 * ## ハンドラ例外の砦 (範囲)
 *
 * 漏れた例外を FAILED へ写像し、決済失敗が HTTP 500 になって在庫回収もステータス遷移も
 * 行われない事態を防ぐ。**対象は {@link authorize()} と {@link capture()} の 2 呼び出しに限る**。
 * 範囲外は以下のとおり:
 *
 * - `supports()` / `getHandlerId()` — ハンドラ解決時に呼ぶ純粋な述語。投げれば 500 になるため
 *   {@link AgentCheckoutPaymentHandlerInterface} 側で「投げないこと」を契約としている。
 * - `UcpPaymentHandlerInterface::exchangePaymentToken()` — 状態機械の外 (controller) で呼ばれるため、
 *   砦も controller 側にある。
 * - `AgentCheckoutPurchaseFlowAdapter` の prepare/commit/rollback — コア内部の呼び出しであり、
 *   例外はトランザクションごと巻き戻して伝播させる。**capture 成功後に commit が失敗した場合、
 *   保持した PSP 参照も巻き戻る**点は既知の制約 (回復には PSP 側の照会が要る)。
 *
 * プロトコル controller (#6776 ACP / #6574 UCP) は本サービスへ委譲し、戻り値
 * {@link AgentCheckoutCompletionResult} を各プロトコルのレスポンスへ変換する。
 */
class AgentCheckoutCompletionService
{
    /** 与信失敗の回数を数える metadata のキー. */
    public const PAYMENT_FAILURES_METADATA_KEY = 'payment_failures';

    /**
     * @param int $maxPaymentAttempts 1 セッションで許す与信失敗の回数。超えたセッションは canceled にする
     *                                (同一セッションで支払データを差し替えながら与信を繰り返す試行を止める)
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AgentCheckoutPurchaseFlowAdapter $purchaseFlowAdapter,
        private readonly AgentCheckoutPaymentHandlerRegistry $paymentHandlerRegistry,
        private readonly CheckoutSessionStatusRepository $statusRepository,
        private readonly CustomerResolverInterface $customerResolver,
        private readonly LoggerInterface $logger,
        private readonly int $escalationExpireMinutes,
        private readonly AgentCheckoutSessionLocker $locker,
        private readonly AgentCheckoutSessionTerminator $terminator,
        private readonly AgentPaymentMethodResolverInterface $paymentMethodResolver,
        private readonly int $maxPaymentAttempts = 5,
    ) {
    }

    /**
     * セッションを確定する (状態機械の単一エントリ。初回 complete・再開 complete の双方を処理).
     *
     * セッション行をロックしてからステータスを判定する。同じセッションへの並行 complete は
     * 先行のコミットを待ち、確定済みなら冪等に既存 Order を返す (在庫引当・与信を二重に実行しない)。
     *
     * @param array<string, mixed> $paymentData 決済の中立表現 (再開時は authentication_result 等を含む)
     * @param string|null          $handlerId   エージェントが選んだ決済ハンドラ (指定時はロック後の Order に支払方法を割り当てる)
     *
     * @throws AgentCheckoutException 確定不能な状態 (終端・期限切れ・未確定・Order 未生成・決済ハンドラ不一致等)
     */
    public function complete(CheckoutSession $session, array $paymentData, ?string $handlerId = null): AgentCheckoutCompletionResult
    {
        $result = $this->locker->transactional($session, function (CheckoutSession $session) use ($paymentData, $handlerId): ?AgentCheckoutCompletionResult {
            if ($this->terminator->isOverdue($session)) {
                // 期限切れの回収 (在庫の解放と expired への遷移) はコミットさせ、その後でエラーを返す。
                $this->terminator->expire($session);

                return null;
            }

            return $this->completeLocked($session, $paymentData, $handlerId);
        });

        if ($result === null) {
            throw new AgentCheckoutException(AgentCheckoutErrorCode::INVALID_SESSION_STATE, 'The checkout session has expired.');
        }

        return $result;
    }

    /**
     * ロック取得後の complete 本体.
     *
     * @param array<string, mixed> $paymentData
     */
    private function completeLocked(CheckoutSession $session, array $paymentData, ?string $handlerId): AgentCheckoutCompletionResult
    {
        $statusId = $session->getStatus()?->getId();

        // 冪等性: 既に完了済なら副作用を再実行せず既存 Order を返す。
        if ($statusId === CheckoutSessionStatus::COMPLETED) {
            $completedOrder = $session->getOrder();
            if ($completedOrder !== null) {
                // 並行リクエストが先に確定させた場合、手元の Order は確定前 (受注番号なし) の状態のまま。
                $this->entityManager->refresh($completedOrder);
            }

            return new AgentCheckoutCompletionResult($this->requireStatus(CheckoutSessionStatus::COMPLETED), $completedOrder);
        }

        // 終端ステータスは確定不能 (プロトコル系エラー)。
        if (in_array($statusId, [CheckoutSessionStatus::CANCELED, CheckoutSessionStatus::EXPIRED], true)) {
            throw new AgentCheckoutException(AgentCheckoutErrorCode::INVALID_SESSION_STATE, 'The checkout session is in a terminal state and cannot be completed.');
        }

        // 確定できるのは ready と、在庫を引き当てたまま中断した 2 状態からの再開だけ。
        // incomplete (住所未確定・見積エラーあり) のまま確定させない。
        $isResume = in_array($statusId, AgentCheckoutSessionTerminator::HELD_STATUSES, true);
        if (!$isResume && $statusId !== CheckoutSessionStatus::READY) {
            throw new AgentCheckoutException(AgentCheckoutErrorCode::INVALID_SESSION_STATE, 'The checkout session is not ready for completion. Resolve the errors in messages[] with an update first.');
        }

        $order = $session->getOrder();
        if ($order === null) {
            throw new AgentCheckoutException(AgentCheckoutErrorCode::INVALID_SESSION_STATE, 'The checkout session has no order to complete.');
        }

        // エージェントが選んだ決済ハンドラに対応する支払方法を、ロック後の Order に割り当てる。
        // ロック前に割り当てると、並行する update が差し替えた Order では既定の支払方法のまま確定してしまう。
        if ($handlerId !== null && $this->paymentMethodResolver->resolve($order, $handlerId) === null) {
            throw new AgentCheckoutException(AgentCheckoutErrorCode::PAYMENT_HANDLER_NOT_FOUND, sprintf('No enabled payment method supports the requested payment handler "%s".', $handlerId));
        }

        $member = $this->customerResolver->resolve($session);

        return $this->runStateMachine($session, $order, $member, $paymentData, $isResume);
    }

    /**
     * 状態機械の本体 (トランザクション内で実行される).
     *
     * @param array<string, mixed> $paymentData
     */
    private function runStateMachine(CheckoutSession $session, Order $order, ?Customer $member, array $paymentData, bool $isResume): AgentCheckoutCompletionResult
    {
        $handler = $this->paymentHandlerRegistry->resolveForOrder($order);

        // 決済ハンドラが無い支払方法で確定してよいのは、決済処理を伴わないもの (本体の Cash) と 0 円の受注だけ。
        // Web 専用の決済プラグイン (クレジットカード等) もハンドラを持たないため、無条件に確定すると
        // プラグインの決済処理を通らないまま新規受付になる。在庫引当の前に判定し、副作用を残さない。
        if ($handler === null && !$this->isSettledWithoutHandler($order)) {
            $this->logger->warning('The payment method of the agent order has no agent payment handler.', [
                'order_id' => $order->getId(),
                'method_class' => $order->getPayment()?->getMethodClass(),
            ]);

            return new AgentCheckoutCompletionResult($this->requireCurrentStatus($session), null, [
                new AgentCheckoutMessage(AgentCheckoutMessageLevel::ERROR, 'The payment method assigned to this checkout cannot be processed by an agent. Specify a supported payment handler.'),
            ]);
        }

        // 初回 complete: 在庫引当・受注番号採番 (PROCESSING のまま)。
        // 再開 complete: 引当は初回で済んでいるため再 prepare しない。
        if (!$isResume) {
            $prepareResult = $this->purchaseFlowAdapter->prepare($order, $member);
            if ($prepareResult->hasError()) {
                // ビジネス系エラー (在庫不足等): 確定せず messages[] を返す (status 据置)。
                return new AgentCheckoutCompletionResult($this->requireCurrentStatus($session), null, $prepareResult->messages);
            }
        }

        // 代引・銀行振込等 (本体の Cash) と 0 円の受注は与信不要のためそのまま確定する。
        if ($handler === null) {
            // prepare は見積を再計算する (送料・手数料の変更等)。0 円だった受注が有償になっていれば、
            // 決済処理を通さずに確定しないよう引当を戻して止める。
            if (!$this->isSettledWithoutHandler($order)) {
                $this->purchaseFlowAdapter->rollback($order, $member);

                return new AgentCheckoutCompletionResult($this->requireCurrentStatus($session), null, [
                    new AgentCheckoutMessage(AgentCheckoutMessageLevel::ERROR, 'The payment method assigned to this checkout cannot be processed by an agent. Specify a supported payment handler.'),
                ]);
            }

            return $this->commitOrder($session, $order, $member);
        }

        // 再開 complete (REQUIRES_ACTION / IN_PROGRESS) でのみ、中断時に保持した PSP 参照を渡す。
        // エージェントの入力ではなくサーバ側の記録なので、ワンショットトークンを再償還せず同じ取引を
        // 続行できる。ready からの再試行では渡さない (クラス docblock の表を参照)。
        $outcome = $this->authorize($handler, $order, $paymentData, $isResume ? ($session->getPaymentData() ?? []) : []);

        switch ($outcome->status) {
            case PaymentOutcomeStatus::AUTHORIZED:
                // 与信のみ成立。capture 前に PSP 参照を payment_data へ残し、capture が失敗しても
                // どの取引が与信済みかを追跡できるようにする。
                $this->mergePaymentData($session, $this->paymentReference($outcome));
                $capture = $this->capture($handler, $order, $paymentData, $outcome);
                if ($capture->status !== PaymentOutcomeStatus::COMPLETED) {
                    return $this->failOrder($session, $order, $member, $this->asCaptureFailure($capture, $order));
                }
                $this->mergePaymentData($session, $this->paymentReference($capture));

                return $this->commitOrder($session, $order, $member);

            case PaymentOutcomeStatus::COMPLETED:
                // 与信と売上が 1 度で完結する auto-capture 型 PSP。capture を呼ぶと二重発行になるため呼ばない。
                $this->mergePaymentData($session, $this->paymentReference($outcome));

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
     * ハンドラの与信を実行する. 例外は FAILED へ写像し、状態機械の外へ出さない.
     *
     * ハンドラは PSP 通信の失敗も {@link PaymentOutcome::failed()} で返す契約だが、漏れた例外を
     * そのまま伝播させるとトランザクションごと巻き戻り、決済失敗が HTTP 500 になって
     * 在庫の回収 (rollback) もセッションの状態遷移も行われない。ここが最後の砦。
     *
     * @param array<string, mixed> $paymentData
     * @param array<string, mixed> $paymentReference
     */
    private function authorize(AgentCheckoutPaymentHandlerInterface $handler, Order $order, array $paymentData, array $paymentReference): PaymentOutcome
    {
        try {
            return $handler->authorize($order, $paymentData, $paymentReference);
        } catch (\Throwable $e) {
            $this->logger->error('The agent payment handler threw during authorize.', ['exception' => $e, 'order_no' => $order->getOrderNo()]);

            // 与信が成立したかどうかを判定できないため、参照は保持できない。retryable として ready へ戻す。
            return PaymentOutcome::failed('payment_handler_error', 'The payment could not be processed.', true);
        }
    }

    /**
     * ハンドラの売上確定を実行する. 例外は FAILED へ写像し、与信の取引識別子を保持する.
     *
     * @param array<string, mixed> $paymentData
     */
    private function capture(AgentCheckoutPaymentHandlerInterface $handler, Order $order, array $paymentData, PaymentOutcome $authorization): PaymentOutcome
    {
        try {
            return $handler->capture($order, $paymentData, $authorization);
        } catch (\Throwable $e) {
            $this->logger->error('The agent payment handler threw during capture.', ['exception' => $e, 'order_no' => $order->getOrderNo(), 'transaction_id' => $authorization->transactionId]);

            // 与信は PSP 側に残る。取消・再 capture の照会ができるよう取引識別子を引き継ぐ。
            return PaymentOutcome::failed('payment_handler_error', 'The payment could not be captured.', true, $authorization->transactionId, $authorization->metadata);
        }
    }

    /**
     * capture の戻り値を「失敗として扱える形」へ正規化する.
     *
     * capture の契約は COMPLETED か FAILED のみ ({@link AgentCheckoutPaymentHandlerInterface::capture()})。
     * PENDING 等をそのまま {@link failOrder()} へ渡すと errorCode / errorMessage が無く、
     * **中身が空の ERROR メッセージ**がエージェントへ返る。契約違反はログに残し、明示的な失敗へ写像する。
     */
    private function asCaptureFailure(PaymentOutcome $capture, Order $order): PaymentOutcome
    {
        if ($capture->status === PaymentOutcomeStatus::FAILED) {
            return $capture;
        }

        $this->logger->error('The agent payment handler returned an invalid status from capture.', [
            'order_no' => $order->getOrderNo(),
            'status' => $capture->status->value,
            'transaction_id' => $capture->transactionId,
        ]);

        return PaymentOutcome::failed(
            'capture_unexpected_status',
            'The payment could not be captured.',
            true,
            $capture->transactionId,
            $capture->metadata,
        );
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
        $this->mergePaymentData($session, $this->paymentReference($outcome));
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
     * ready からの再 complete は新規の authorize から始まる (capture 単独の再実行入口は無い)。
     */
    private function failOrder(CheckoutSession $session, Order $order, ?Customer $member, PaymentOutcome $outcome): AgentCheckoutCompletionResult
    {
        $this->purchaseFlowAdapter->rollback($order, $member);
        // 失敗した取引の PSP 参照も残す。**照会・監査のための記録**であり、再試行の入力ではない
        // (ready からの再試行でハンドラへ渡すと、死んだ取引の続行になる)。
        $this->mergePaymentData($session, $this->paymentReference($outcome));

        $failures = $this->countPaymentFailure($session);
        $exhausted = $failures >= $this->maxPaymentAttempts;
        if ($exhausted) {
            $this->logger->warning('The agent checkout session reached the maximum number of payment attempts.', [
                'session_id' => $session->getSessionId(),
                'failures' => $failures,
            ]);
        }

        $statusId = $outcome->retryable && !$exhausted ? CheckoutSessionStatus::READY : CheckoutSessionStatus::CANCELED;
        $session->setStatus($this->requireStatus($statusId));
        // 在庫は回収済みのため、中断時に設定した在庫確保期限は外す (ready のまま期限切れ回収の対象にしない)。
        $session->setExpiresAt();

        $messages = [new AgentCheckoutMessage(AgentCheckoutMessageLevel::ERROR, $this->errorMessageFor($outcome))];
        if ($exhausted && $outcome->retryable) {
            $messages[] = new AgentCheckoutMessage(AgentCheckoutMessageLevel::ERROR, 'The maximum number of payment attempts for this checkout session has been reached. Start a new checkout session.');
        }

        return new AgentCheckoutCompletionResult($session->getStatus() ?? $this->requireStatus($statusId), null, $messages);
    }

    /**
     * 与信失敗の回数を metadata に記録し、記録後の回数を返す.
     */
    private function countPaymentFailure(CheckoutSession $session): int
    {
        $metadata = $session->getMetadata() ?? [];
        $failures = (int) ($metadata[self::PAYMENT_FAILURES_METADATA_KEY] ?? 0) + 1;
        $metadata[self::PAYMENT_FAILURES_METADATA_KEY] = $failures;
        $session->setMetadata($metadata);

        return $failures;
    }

    /**
     * 決済ハンドラ無しで確定してよい受注か (本体の Cash の支払方法、または支払総額 0 円).
     *
     * Cash を継承したプラグインの支払方法は対象にしない。継承先が決済処理を持たない保証が無いため
     * (fail-closed)。エージェント経路で扱う場合は、その支払方法に対応する決済ハンドラを登録する。
     */
    private function isSettledWithoutHandler(Order $order): bool
    {
        if ($order->getPayment()?->getMethodClass() === Cash::class) {
            return true;
        }

        return bccomp($order->getPaymentTotal(), '0', 2) === 0;
    }

    /**
     * エージェントへ返す ERROR メッセージ本文を決める.
     *
     * errorMessage → errorCode の順に採用し、双方が空でも**空文字を返さない**。
     * 空文字だと messages[] に要素はあるのに内容が無い応答になり、エージェントは理由を判別できない。
     */
    private function errorMessageFor(PaymentOutcome $outcome): string
    {
        foreach ([$outcome->errorMessage, $outcome->errorCode] as $candidate) {
            if ($candidate !== null && $candidate !== '') {
                return $candidate;
            }
        }

        return 'The payment could not be processed.';
    }

    /**
     * {@link PaymentOutcome} から payment_data へ残す PSP 参照を組み立てる.
     *
     * metadata に加えて transactionId を `transaction_id` として保持する。中断 (REQUIRES_ACTION /
     * PENDING) からの再開・失敗時の照会は、この参照を辿って行う。
     *
     * @return array<string, mixed>
     */
    private function paymentReference(PaymentOutcome $outcome): array
    {
        $reference = $outcome->metadata;
        if ($outcome->transactionId !== null && $outcome->transactionId !== '') {
            $reference['transaction_id'] = $outcome->transactionId;
        }

        return $reference;
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
