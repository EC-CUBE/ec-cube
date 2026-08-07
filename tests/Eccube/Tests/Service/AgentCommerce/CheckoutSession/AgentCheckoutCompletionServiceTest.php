<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Service\AgentCommerce\CheckoutSession;

use Eccube\Entity\CheckoutSession;
use Eccube\Entity\Master\AgentProtocol;
use Eccube\Entity\Master\CheckoutSessionStatus;
use Eccube\Entity\Order;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductStock;
use Eccube\Repository\CheckoutSessionRepository;
use Eccube\Repository\Master\CheckoutSessionStatusRepository;
use Eccube\Service\AgentCommerce\AgentCheckoutPurchaseFlowAdapter;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutAddress;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutCompletionService;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutLineItem;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutRequest;
use Eccube\Service\AgentCommerce\CheckoutSession\GuestCustomerResolver;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerInterface;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerRegistry;
use Eccube\Service\AgentCommerce\Payment\PaymentOutcome;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\NullLogger;

/**
 * Layer 3 (complete 状態機械) tests for AgentCheckoutCompletionService.
 *
 * 「中断 → 再開」の状態機械を、決済ハンドラスタブの {@link PaymentOutcome} を切り替えて検証する:
 * frictionless / challenge→再開 / declined→rollback / 冪等性 / 非同期(PENDING)→再開 /
 * 期限切れ回収。在庫引当の保持・回収と CheckoutSession の正規化ステータス遷移を確認する。
 */
final class AgentCheckoutCompletionServiceTest extends EccubeTestCase
{
    private ?AgentCheckoutPurchaseFlowAdapter $adapter = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adapter = self::getContainer()->get(AgentCheckoutPurchaseFlowAdapter::class);
    }

    public function testFrictionlessAuthorizeThenCapturePlacesOrder(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $stock = $ProductClass->getProductStock();
        $session = $this->createReadySession($ProductClass, 2);

        $handler = $this->stubHandler([PaymentOutcome::authorized('auth_1')], PaymentOutcome::completed('cap_1'));
        $result = $this->service($handler)->complete($session, []);

        $this->assertSame(CheckoutSessionStatus::COMPLETED, $result->status->getId(), 'frictionless で completed へ遷移する');
        $this->assertInstanceOf(Order::class, $result->order, 'completed 時は Order が返る');
        $this->assertSame(CheckoutSessionStatus::COMPLETED, $session->getStatus()?->getId());

        $this->entityManager->refresh($stock);
        $this->assertInstanceOf(ProductStock::class, $stock);
        $this->assertSame(98, (int) $stock->getStock(), '在庫が 2 引き当てられて確定する');
    }

    public function testChallengeHoldsStockThenResumeCompletes(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $stock = $ProductClass->getProductStock();
        $session = $this->createReadySession($ProductClass, 2);

        // authorize: 1 回目 REQUIRES_ACTION (3DS challenge) → 2 回目 AUTHORIZED (再開)。
        $handler = $this->stubHandler(
            [PaymentOutcome::requiresAction(['continue_url' => 'https://example.com/3ds/abc']), PaymentOutcome::authorized('auth_2')],
            PaymentOutcome::completed('cap_1'),
        );
        $service = $this->service($handler);

        // 初回 complete: 追加認証待ち。在庫は引き当てたまま保持し、Order は未確定。
        $first = $service->complete($session, []);
        $this->assertSame(CheckoutSessionStatus::REQUIRES_ACTION, $first->status->getId(), '3DS challenge は requires_action (エラーでない)');
        $this->assertNotInstanceOf(Order::class, $first->order, 'requires_action では Order は未確定');
        $this->assertSame(['continue_url' => 'https://example.com/3ds/abc'], $first->actionData, 'actionData に continue_url 原資が載る');
        $this->assertInstanceOf(\DateTime::class, $session->getExpiresAt(), 'requires_action で在庫確保期限 (expires_at) が設定される');
        $this->assertGreaterThan(new \DateTime(), $session->getExpiresAt(), 'expires_at は将来 (在庫確保期限)');

        $this->entityManager->refresh($stock);
        $this->assertInstanceOf(ProductStock::class, $stock);
        $this->assertSame(98, (int) $stock->getStock(), 'requires_action 中も在庫は引当のまま保持される (rollback しない)');

        // 再開 complete: authentication_result を受けて確定。再 prepare せず authorize→capture→commit。
        $second = $service->complete($session, ['authentication_result' => ['outcome' => 'authenticated']]);
        $this->assertSame(CheckoutSessionStatus::COMPLETED, $second->status->getId(), '再開で completed へ遷移');
        $this->assertInstanceOf(Order::class, $second->order);

        $this->entityManager->refresh($stock);
        $this->assertSame(98, (int) $stock->getStock(), '再開確定後も在庫は二重に減らない');
    }

    public function testAuthorizedOutcomeHandsTransactionReferenceToCapture(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $session = $this->createReadySession($ProductClass, 1);

        $handler = $this->stubHandler(
            [PaymentOutcome::authorized('pi_auth_1', ['gateway' => 'stub'])],
            PaymentOutcome::completed('pi_auth_1', ['captured' => true]),
        );
        $this->service($handler)->complete($session, ['token' => 'tok_visa']);

        $this->assertCount(1, $handler->capturedAuthorizations, 'AUTHORIZED は capture を 1 度だけ呼ぶ');
        $this->assertSame('pi_auth_1', $handler->capturedAuthorizations[0]->transactionId, 'capture は authorize が返した取引識別子を受け取る (トークン再償還を不要にする)');
        $this->assertSame(['gateway' => 'stub'], $handler->capturedAuthorizations[0]->metadata, 'capture は authorize の metadata も受け取る');

        $paymentData = $session->getPaymentData() ?? [];
        $this->assertSame('pi_auth_1', $paymentData['transaction_id'] ?? null, '取引識別子が payment_data へ永続化される');
        $this->assertTrue($paymentData['captured'] ?? false, 'capture の metadata も payment_data へマージされる');
    }

    public function testAutoCaptureGatewayIsNotCapturedTwice(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $stock = $ProductClass->getProductStock();
        $session = $this->createReadySession($ProductClass, 1);

        // auto-capture 型 PSP: authorize が売上確定まで済ませて COMPLETED を返す。
        $handler = $this->stubHandler([PaymentOutcome::completed('ch_auto_1', ['gateway' => 'auto'])]);
        $result = $this->service($handler)->complete($session, []);

        $this->assertSame(CheckoutSessionStatus::COMPLETED, $result->status->getId(), 'auto-capture でも注文は確定する');
        $this->assertSame(0, $handler->captureCount, 'COMPLETED に対して capture を呼ばない (二重売上を防ぐ)');

        $paymentData = $session->getPaymentData() ?? [];
        $this->assertSame('ch_auto_1', $paymentData['transaction_id'] ?? null, 'capture を経由しなくても PSP 参照は payment_data へ残る');

        $this->entityManager->refresh($stock);
        $this->assertInstanceOf(ProductStock::class, $stock);
        $this->assertSame(99, (int) $stock->getStock(), '在庫は 1 だけ引き当てられる');
    }

    public function testCaptureFailureRollsBackStockAndKeepsAuthorizationReference(): void
    {
        $ProductClass = $this->createPurchasableProductClass('10');
        $stock = $ProductClass->getProductStock();
        $session = $this->createReadySession($ProductClass, 2);

        $handler = $this->stubHandler(
            [PaymentOutcome::authorized('pi_auth_2', ['gateway' => 'stub'])],
            PaymentOutcome::failed('capture_failed', 'Capture was rejected.', true, 'pi_auth_2'),
        );
        $result = $this->service($handler)->complete($session, []);

        $this->assertSame(CheckoutSessionStatus::READY, $result->status->getId(), 'capture 失敗 (retryable) は ready に戻す');
        $this->assertNotInstanceOf(Order::class, $result->order, 'capture 失敗時は注文を確定しない');

        $paymentData = $session->getPaymentData() ?? [];
        $this->assertSame('pi_auth_2', $paymentData['transaction_id'] ?? null, '与信済みの取引識別子は capture 失敗後も残る (与信取消の照会に必要)');

        $this->entityManager->refresh($stock);
        $this->assertInstanceOf(ProductStock::class, $stock);
        $this->assertSame(10, (int) $stock->getStock(), 'capture 失敗で引当をロールバックする');
    }

    /**
     * 不変条件: 保持済みの PSP 参照をハンドラへ渡すのは「在庫を引当てたまま中断した」状態
     * (REQUIRES_ACTION / IN_PROGRESS) からの再開時のみ。ready からの再試行では渡さない.
     *
     * ready は在庫を回収済みで、与信拒否でここへ落ちた場合は保持している識別子が死んだ取引を指す。
     * 渡してしまうと「別カードでの再試行」が拒否済み取引の続行になる。
     */
    #[DataProvider(methodName: 'reentryScenarios')]
    public function testStoredPaymentReferenceReachesHandlerOnlyWhenResumingFromHeldStatus(PaymentOutcome $interrupted, bool $expectsReference): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $session = $this->createReadySession($ProductClass, 1);
        $handler = $this->stubHandler(
            [$interrupted, PaymentOutcome::authorized('pi_second')],
            PaymentOutcome::completed('pi_second'),
        );
        $service = $this->service($handler);

        $service->complete($session, ['token' => 'tok_first']);
        $this->assertSame([], $handler->receivedPaymentReferences[0], '初回 complete には保持済み参照が無い');

        // 中断・失敗のいずれでも参照は payment_data に残る (照会・監査のため)。
        $this->assertSame('pi_first', ($session->getPaymentData() ?? [])['transaction_id'] ?? null, 'PSP 参照は payment_data に保持される');

        $service->complete($session, ['token' => 'tok_second']);
        $handedBack = $handler->receivedPaymentReferences[1]['transaction_id'] ?? null;

        if ($expectsReference) {
            $this->assertSame('pi_first', $handedBack, '中断からの再開では保持済み参照がハンドラへ渡る');
        } else {
            $this->assertNull($handedBack, 'ready からの再試行では保持済み参照を渡さない (新規与信としてやり直す)');
        }
    }

    /**
     * 再入シナリオ: [中断/失敗を起こす authorize の戻り値, 再入時に参照が渡るか].
     *
     * complete に再入できる非終端ステータスのうち、初回入口の INCOMPLETE を除く 3 経路を網羅する。
     *
     * @return array<string, array{PaymentOutcome, bool}>
     */
    public static function reentryScenarios(): array
    {
        return [
            'requires_action からの再開' => [PaymentOutcome::requiresAction(['continue_url' => 'https://example.com/3ds'], [], 'pi_first'), true],
            'in_progress からの再開' => [PaymentOutcome::pending([], 'pi_first'), true],
            '与信拒否で ready へ戻った後の再試行' => [PaymentOutcome::failed('card_declined', 'The card was declined.', true, 'pi_first'), false],
        ];
    }

    /**
     * capture 失敗 (retryable) で ready へ戻った後の再試行も、新規 authorize から始まる.
     *
     * {@link AgentCheckoutPaymentHandlerInterface::capture()} が「与信が残り再 authorize できない
     * 場合は retryable=false を返せ」と定めている根拠となる挙動。
     */
    public function testReadyRetryAfterCaptureFailureStartsFreshAuthorize(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $session = $this->createReadySession($ProductClass, 1);

        $handler = $this->stubHandler(
            [PaymentOutcome::authorized('pi_auth_retry')],
            PaymentOutcome::failed('capture_failed', 'Capture was rejected.', true, 'pi_auth_retry'),
        );
        $service = $this->service($handler);

        $first = $service->complete($session, ['token' => 'tok_one_shot']);
        $this->assertSame(CheckoutSessionStatus::READY, $first->status->getId());

        $service->complete($session, ['token' => 'tok_one_shot']);

        $this->assertSame(2, $handler->authorizeCount, 'ready からの再試行は capture の再実行ではなく新規 authorize になる');
        $this->assertSame([], $handler->receivedPaymentReferences[1], '再試行では与信済みの取引識別子を渡さない');
    }

    /**
     * capture が契約 (COMPLETED か FAILED) に反した status を返しても、内容のある ERROR を返す.
     *
     * PENDING は errorCode/errorMessage を持たないため、素通しすると messages[] に
     * 「要素はあるが中身が空」のエラーが載る。
     */
    public function testCaptureContractViolationIsReportedWithNonEmptyMessage(): void
    {
        $ProductClass = $this->createPurchasableProductClass('10');
        $stock = $ProductClass->getProductStock();
        $session = $this->createReadySession($ProductClass, 2);

        $handler = $this->stubHandler(
            [PaymentOutcome::authorized('pi_auth_3')],
            PaymentOutcome::pending([], 'pi_auth_3'),
        );
        $result = $this->service($handler)->complete($session, []);

        $this->assertSame(CheckoutSessionStatus::READY, $result->status->getId(), 'capture の契約違反は失敗として扱う');
        $this->assertCount(1, $result->messages);
        $this->assertNotSame('', $result->messages[0]->message, 'エージェントへ空文字のエラーメッセージを返さない');

        $this->entityManager->refresh($stock);
        $this->assertInstanceOf(ProductStock::class, $stock);
        $this->assertSame(10, (int) $stock->getStock(), '契約違反でも在庫は回収する (fail-closed)');
    }

    /**
     * errorMessage も errorCode も空のまま失敗しても、メッセージ本文は空にならない.
     */
    public function testFailureMessageIsNeverEmpty(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $session = $this->createReadySession($ProductClass, 1);

        $handler = $this->stubHandler([PaymentOutcome::failed('', '', true)]);
        $result = $this->service($handler)->complete($session, []);

        $this->assertCount(1, $result->messages);
        $this->assertNotSame('', $result->messages[0]->message, 'errorCode / errorMessage が空でも本文を落とさない');
    }

    public function testResumeHandsStoredPaymentReferenceToHandler(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $session = $this->createReadySession($ProductClass, 1);

        $handler = $this->stubHandler(
            [
                PaymentOutcome::requiresAction(['continue_url' => 'https://example.com/3ds'], ['gateway' => 'stub'], 'pi_hold_1'),
                PaymentOutcome::authorized('pi_hold_1'),
            ],
            PaymentOutcome::completed('pi_hold_1'),
        );
        $service = $this->service($handler);

        $service->complete($session, ['token' => 'tok_one_shot']);
        $this->assertSame([], $handler->receivedPaymentReferences[0], '初回 complete では保持済み参照が無い');

        // 再開: エージェントの入力に取引識別子が無くても、中断時に保持した PSP 参照がハンドラへ届く
        // (ワンショットトークンを再償還せず同じ取引を続行できる)。
        $service->complete($session, ['authentication_result' => ['outcome' => 'authenticated']]);
        $resumeReference = $handler->receivedPaymentReferences[1];
        $this->assertSame('pi_hold_1', $resumeReference['transaction_id'] ?? null, '再開時は中断前の取引識別子がハンドラへ渡る');
        $this->assertSame('stub', $resumeReference['gateway'] ?? null, '中断時の metadata も引き渡される');
    }

    public function testHandlerExceptionOnAuthorizeIsMappedToFailureNotFatal(): void
    {
        $ProductClass = $this->createPurchasableProductClass('10');
        $stock = $ProductClass->getProductStock();
        $session = $this->createReadySession($ProductClass, 2);

        // ハンドラは failed を返す契約だが、漏れた例外で状態遷移ごと巻き戻さないことを保証する。
        $handler = $this->stubHandler([new \RuntimeException('psp connection reset')]);
        $result = $this->service($handler)->complete($session, []);

        $this->assertSame(CheckoutSessionStatus::READY, $result->status->getId(), 'ハンドラの例外は 500 でなくビジネス系の失敗として扱う');
        $this->assertNotEmpty($result->messages, 'エージェントには messages[] で返す');

        $this->entityManager->refresh($stock);
        $this->assertInstanceOf(ProductStock::class, $stock);
        $this->assertSame(10, (int) $stock->getStock(), '例外時も引当をロールバックする');
    }

    public function testHandlerExceptionOnCaptureKeepsAuthorizationReference(): void
    {
        $ProductClass = $this->createPurchasableProductClass('10');
        $session = $this->createReadySession($ProductClass, 1);

        $handler = $this->stubHandler(
            [PaymentOutcome::authorized('pi_auth_3', ['gateway' => 'stub'])],
            new \RuntimeException('psp timeout'),
        );
        $result = $this->service($handler)->complete($session, []);

        $this->assertSame(CheckoutSessionStatus::READY, $result->status->getId(), 'capture の例外も retryable な失敗として扱う');

        $paymentData = $session->getPaymentData() ?? [];
        $this->assertSame('pi_auth_3', $paymentData['transaction_id'] ?? null, '例外で巻き戻さず、与信済み取引の識別子を残す (取消・照会に必要)');
    }

    public function testDeclinedRetryableRollsBackStockAndReturnsToReady(): void
    {
        $ProductClass = $this->createPurchasableProductClass('10');
        $stock = $ProductClass->getProductStock();
        $session = $this->createReadySession($ProductClass, 3);

        $handler = $this->stubHandler([PaymentOutcome::failed('payment_declined', 'Card declined.', true)]);
        $result = $this->service($handler)->complete($session, []);

        $this->assertSame(CheckoutSessionStatus::READY, $result->status->getId(), 'retryable な決済失敗は ready に戻す (再 complete 可)');
        $this->assertNotInstanceOf(Order::class, $result->order, '失敗時は Order を確定しない');
        $this->assertNotEmpty($result->messages, '決済失敗はビジネス系メッセージで返る');

        $this->entityManager->refresh($stock);
        $this->assertInstanceOf(ProductStock::class, $stock);
        $this->assertSame(10, (int) $stock->getStock(), '決済失敗で引当をロールバックし在庫が戻る');
    }

    public function testDeclinedNonRetryableCancelsSession(): void
    {
        $ProductClass = $this->createPurchasableProductClass('10');
        $session = $this->createReadySession($ProductClass, 1);

        $handler = $this->stubHandler([PaymentOutcome::failed('fraud_blocked', 'Blocked.', false)]);
        $result = $this->service($handler)->complete($session, []);

        $this->assertSame(CheckoutSessionStatus::CANCELED, $result->status->getId(), 'unrecoverable な決済失敗は canceled');
    }

    public function testCompletedSessionIsIdempotentOnReplay(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $stock = $ProductClass->getProductStock();
        $session = $this->createReadySession($ProductClass, 2);

        $handler = $this->stubHandler([PaymentOutcome::authorized('auth_1')], PaymentOutcome::completed('cap_1'));
        $service = $this->service($handler);

        $first = $service->complete($session, []);
        $this->assertSame(CheckoutSessionStatus::COMPLETED, $first->status->getId());
        $this->entityManager->refresh($stock);
        $this->assertInstanceOf(ProductStock::class, $stock);
        $this->assertSame(98, (int) $stock->getStock());
        $orderId = $first->order?->getId();

        // 完了済セッションの再 complete は副作用 (採番・在庫引当・与信) を再実行しない (ACP MUST)。
        $replay = $service->complete($session, []);
        $this->assertSame(CheckoutSessionStatus::COMPLETED, $replay->status->getId(), '再 complete も completed のまま');
        $this->assertSame($orderId, $replay->order?->getId(), '同一 Order を返す');
        $this->assertSame(1, $handler->authorizeCount, 'リプレイで authorize は再実行されない');
        $this->assertSame(1, $handler->captureCount, 'リプレイで capture は再実行されない');

        $this->entityManager->refresh($stock);
        $this->assertSame(98, (int) $stock->getStock(), 'リプレイで在庫が二重に減らない');
    }

    public function testPendingHoldsAsInProgressThenResumeCompletes(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $stock = $ProductClass->getProductStock();
        $session = $this->createReadySession($ProductClass, 1);

        // authorize: 1 回目 PENDING (非同期) → 2 回目 AUTHORIZED (IPN/Webhook 受信後の再開)。
        $handler = $this->stubHandler(
            [PaymentOutcome::pending(['psp_ref' => 'pi_123']), PaymentOutcome::authorized('auth_2')],
            PaymentOutcome::completed('cap_1'),
        );
        $service = $this->service($handler);

        $first = $service->complete($session, []);
        $this->assertSame(CheckoutSessionStatus::IN_PROGRESS, $first->status->getId(), 'PENDING は in_progress で保持');
        $this->entityManager->refresh($stock);
        $this->assertInstanceOf(ProductStock::class, $stock);
        $this->assertSame(99, (int) $stock->getStock(), 'in_progress 中も在庫は保持');

        $second = $service->complete($session, []);
        $this->assertSame(CheckoutSessionStatus::COMPLETED, $second->status->getId(), 'IPN/Webhook 相当の再開で completed');
    }

    public function testExpiredRequiresActionSessionIsReclaimable(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $session = $this->createReadySession($ProductClass, 1);

        $handler = $this->stubHandler([PaymentOutcome::requiresAction(['continue_url' => 'https://example.com/3ds'])]);
        $this->service($handler)->complete($session, []);
        $this->assertSame(CheckoutSessionStatus::REQUIRES_ACTION, $session->getStatus()?->getId());

        // 在庫確保期限を過去に倒して、期限切れ回収 (findExpired) の対象になることを確認する。
        $session->setExpiresAt((new \DateTime())->modify('-1 minutes'));
        $this->entityManager->flush();

        /** @var CheckoutSessionRepository $repository */
        $repository = $this->entityManager->getRepository(CheckoutSession::class);
        $expired = $repository->findExpired(new \DateTime());
        $ids = array_map(static fn (CheckoutSession $s): ?int => $s->getId(), $expired);
        $this->assertContains($session->getId(), $ids, 'requires_action は非終端のため期限切れ回収の対象に含まれる');
    }

    /**
     * Order を構築済みの ready なセッションを作る.
     */
    private function createReadySession(ProductClass $ProductClass, int $quantity): CheckoutSession
    {
        $request = new AgentCheckoutRequest(
            lineItems: [new AgentCheckoutLineItem((int) $ProductClass->getId(), $quantity)],
            buyer: $this->guestAddress(),
            protocolId: AgentProtocol::ACP,
        );
        $order = $this->adapter->buildOrder($request)->order;

        $session = new CheckoutSession();
        $session
            ->setSessionId('cs_'.bin2hex(random_bytes(8)))
            ->setOrder($order)
            ->setCurrencyCode('JPY')
            ->setStatus($this->statusMaster(CheckoutSessionStatus::READY));
        $this->entityManager->persist($session);
        $this->entityManager->flush();

        return $session;
    }

    private function statusMaster(int $id): CheckoutSessionStatus
    {
        $status = $this->entityManager->getRepository(CheckoutSessionStatus::class)->find($id);
        $this->assertInstanceOf(CheckoutSessionStatus::class, $status);

        return $status;
    }

    private function service(AgentCheckoutPaymentHandlerInterface ...$handlers): AgentCheckoutCompletionService
    {
        /** @var CheckoutSessionStatusRepository $statusRepository */
        $statusRepository = $this->entityManager->getRepository(CheckoutSessionStatus::class);

        return new AgentCheckoutCompletionService(
            $this->entityManager,
            self::getContainer()->get(AgentCheckoutPurchaseFlowAdapter::class),
            new AgentCheckoutPaymentHandlerRegistry($handlers),
            $statusRepository,
            self::getContainer()->get(GuestCustomerResolver::class),
            new NullLogger(),
            15,
        );
    }

    /**
     * 設定可能な決済ハンドラスタブ.
     *
     * @param array<int, PaymentOutcome|\Throwable> $authorizeOutcomes authorize の戻り値 (呼び出し順に消費・末尾を反復)。
     *                                                                 Throwable を混ぜると authorize がそれを投げる
     */
    private function stubHandler(array $authorizeOutcomes, PaymentOutcome|\Throwable|null $captureOutcome = null): AgentCheckoutPaymentHandlerInterface
    {
        return new class($authorizeOutcomes, $captureOutcome) implements AgentCheckoutPaymentHandlerInterface {
            public int $authorizeCount = 0;

            public int $captureCount = 0;

            /** @var array<int, PaymentOutcome> capture() が受け取った与信結果 */
            public array $capturedAuthorizations = [];

            /** @var array<int, array<string, mixed>> authorize() が受け取った PSP 参照 */
            public array $receivedPaymentReferences = [];

            /**
             * @param array<int, PaymentOutcome|\Throwable> $authorizeOutcomes
             */
            public function __construct(
                private array $authorizeOutcomes,
                private readonly PaymentOutcome|\Throwable|null $captureOutcome,
            ) {
            }

            public function authorize(Order $order, array $paymentData, array $paymentReference = []): PaymentOutcome
            {
                $outcome = $this->authorizeOutcomes[$this->authorizeCount] ?? $this->authorizeOutcomes[array_key_last($this->authorizeOutcomes)];
                ++$this->authorizeCount;
                $this->receivedPaymentReferences[] = $paymentReference;

                if ($outcome instanceof \Throwable) {
                    throw $outcome;
                }

                return $outcome;
            }

            public function capture(Order $order, array $paymentData, PaymentOutcome $authorization): PaymentOutcome
            {
                ++$this->captureCount;
                $this->capturedAuthorizations[] = $authorization;

                if ($this->captureOutcome instanceof \Throwable) {
                    throw $this->captureOutcome;
                }

                return $this->captureOutcome ?? PaymentOutcome::completed('cap_'.$this->captureCount);
            }

            public function supports(Order $order): bool
            {
                return true;
            }

            public function getHandlerId(): string
            {
                return 'test_handler';
            }
        };
    }

    private function guestAddress(): AgentCheckoutAddress
    {
        return new AgentCheckoutAddress(
            name01: '山田',
            name02: '太郎',
            kana01: 'ヤマダ',
            kana02: 'タロウ',
            postalCode: '5300001',
            prefId: 27,
            addr01: '大阪市北区',
            addr02: '梅田1-1-1',
            email: 'agent-completion@example.com',
            phoneNumber: '0612345678',
        );
    }

    private function createPurchasableProductClass(string $stock = '100'): ProductClass
    {
        $Product = $this->createProduct('エージェント complete テスト商品', 1);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()[0];
        $ProductClass->setStock($stock);
        $ProductClass->setStockUnlimited(false);
        // createProduct は ProductStock を faker の乱数で生成するため、引当検証を決定的にするよう
        // ProductClass.stock と ProductStock.stock の双方を明示的に揃える (引当は ProductStock を減らす)。
        $ProductClass->getProductStock()->setStock($stock);
        $this->entityManager->flush();

        return $ProductClass;
    }
}
