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

        $handler = $this->stubHandler([PaymentOutcome::completed('auth_1')], PaymentOutcome::completed('cap_1'));
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

        // authorize: 1 回目 REQUIRES_ACTION (3DS challenge) → 2 回目 COMPLETED (再開)。
        $handler = $this->stubHandler(
            [PaymentOutcome::requiresAction(['continue_url' => 'https://example.com/3ds/abc']), PaymentOutcome::completed('auth_2')],
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

        $handler = $this->stubHandler([PaymentOutcome::completed('auth_1')], PaymentOutcome::completed('cap_1'));
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

        // authorize: 1 回目 PENDING (非同期) → 2 回目 COMPLETED (IPN/Webhook 受信後の再開)。
        $handler = $this->stubHandler(
            [PaymentOutcome::pending(['psp_ref' => 'pi_123']), PaymentOutcome::completed('auth_2')],
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
            15,
        );
    }

    /**
     * 設定可能な決済ハンドラスタブ.
     *
     * @param array<int, PaymentOutcome> $authorizeOutcomes authorize の戻り値 (呼び出し順に消費・末尾を反復)
     */
    private function stubHandler(array $authorizeOutcomes, ?PaymentOutcome $captureOutcome = null): AgentCheckoutPaymentHandlerInterface
    {
        return new class($authorizeOutcomes, $captureOutcome) implements AgentCheckoutPaymentHandlerInterface {
            public int $authorizeCount = 0;

            public int $captureCount = 0;

            /**
             * @param array<int, PaymentOutcome> $authorizeOutcomes
             */
            public function __construct(
                private array $authorizeOutcomes,
                private readonly ?PaymentOutcome $captureOutcome,
            ) {
            }

            public function authorize(Order $order, array $paymentData): PaymentOutcome
            {
                $outcome = $this->authorizeOutcomes[$this->authorizeCount] ?? $this->authorizeOutcomes[array_key_last($this->authorizeOutcomes)];
                ++$this->authorizeCount;

                return $outcome;
            }

            public function capture(Order $order, array $paymentData): PaymentOutcome
            {
                ++$this->captureCount;

                return $this->captureOutcome ?? PaymentOutcome::completed('cap_'.$this->captureCount);
            }

            public function supports(Order $order): bool
            {
                return true;
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
