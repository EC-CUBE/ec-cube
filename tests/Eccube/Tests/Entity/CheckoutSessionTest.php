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

namespace Eccube\Tests\Entity;

use Eccube\Entity\CheckoutSession;
use Eccube\Entity\Order;
use Eccube\Repository\CheckoutSessionRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * Layer 2 (Doctrine) tests for CheckoutSession + Order の agent カラム.
 *
 * - CheckoutSession の永続化・取得・状態遷移 (incomplete -> ready -> completed / canceled / expired)。
 * - SaveEventSubscriber による create_date/update_date 自動付与。
 * - json カラム (buyer_data 等) のラウンドトリップ。
 * - 通常購入の Order で agent_protocol/agent_id が NULL である回帰確認。
 */
final class CheckoutSessionTest extends EccubeTestCase
{
    private ?CheckoutSessionRepository $checkoutSessionRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checkoutSessionRepository = self::getContainer()->get(CheckoutSessionRepository::class);
    }

    private function createSession(string $sessionId): CheckoutSession
    {
        $session = new CheckoutSession();
        $session
            ->setSessionId($sessionId)
            ->setProtocol(CheckoutSession::PROTOCOL_ACP)
            ->setCurrencyCode('JPY')
            ->setBuyerData(['family_name' => '山田', 'given_name' => '太郎'])
            ->setMetadata(['acp_status' => 'not_ready_for_payment']);
        $this->entityManager->persist($session);
        $this->entityManager->flush();

        return $session;
    }

    public function testPersistAssignsIdentifierAndTimestamps(): void
    {
        $session = $this->createSession('cs_test_persist');

        $this->assertNotNull($session->getId(), '永続化で id が採番される');
        $this->assertInstanceOf(\DateTime::class, $session->getCreateDate(), 'SaveEventSubscriber が create_date を自動付与する');
        $this->assertInstanceOf(\DateTime::class, $session->getUpdateDate(), 'SaveEventSubscriber が update_date を自動付与する');
        $this->assertSame(CheckoutSession::STATUS_INCOMPLETE, $session->getStatus(), '初期 status は incomplete');
    }

    public function testFindOneBySessionId(): void
    {
        $this->createSession('cs_test_lookup');
        $this->entityManager->clear();

        $found = $this->checkoutSessionRepository->findOneBySessionId('cs_test_lookup');

        $this->assertInstanceOf(CheckoutSession::class, $found, 'session_id で取得できる');
        $this->assertSame('cs_test_lookup', $found->getSessionId());
    }

    public function testJsonColumnsRoundTrip(): void
    {
        $session = $this->createSession('cs_test_json');
        $id = $session->getId();
        $this->entityManager->clear();

        /** @var CheckoutSession $reloaded */
        $reloaded = $this->checkoutSessionRepository->find($id);

        $this->assertSame(['family_name' => '山田', 'given_name' => '太郎'], $reloaded->getBuyerData(), 'json buyer_data がラウンドトリップする');
        $this->assertSame(['acp_status' => 'not_ready_for_payment'], $reloaded->getMetadata(), 'json metadata がラウンドトリップする');
        $this->assertNull($reloaded->getFulfillmentData(), '未設定の json カラムは null');
    }

    public function testStatusTransitions(): void
    {
        $session = $this->createSession('cs_test_status');

        foreach ([
            CheckoutSession::STATUS_READY,
            CheckoutSession::STATUS_COMPLETED,
        ] as $next) {
            $session->setStatus($next);
            $this->entityManager->flush();
            $this->assertSame($next, $session->getStatus());
        }

        // canceled / expired も正規化ステータスとして保持できる。
        $session->setStatus(CheckoutSession::STATUS_CANCELED);
        $this->entityManager->flush();
        $this->assertSame('canceled', $session->getStatus(), 'canceled を正規化ステータスとして保持できる');
    }

    public function testIsExpired(): void
    {
        $session = $this->createSession('cs_test_expire');
        $now = new \DateTime('2026-06-11 12:00:00');

        $this->assertFalse($session->isExpired($now), 'expires_at 未設定なら期限切れにならない');

        $session->setExpiresAt(new \DateTime('2026-06-11 11:59:59'));
        $this->assertTrue($session->isExpired($now), 'expires_at を過ぎていれば期限切れ');

        $session->setExpiresAt(new \DateTime('2026-06-11 12:00:01'));
        $this->assertFalse($session->isExpired($now), 'expires_at が未来なら期限切れでない');
    }

    public function testFindExpiredExcludesTerminalStatuses(): void
    {
        $now = new \DateTime('2026-06-11 12:00:00');
        $past = new \DateTime('2026-06-11 11:00:00');

        $active = $this->createSession('cs_expired_active');
        $active->setExpiresAt($past);

        $completed = $this->createSession('cs_expired_completed');
        $completed->setExpiresAt($past)->setStatus(CheckoutSession::STATUS_COMPLETED);
        $this->entityManager->flush();

        $expired = $this->checkoutSessionRepository->findExpired($now);
        $sessionIds = array_map(static fn (CheckoutSession $s): string => $s->getSessionId(), $expired);

        $this->assertContains('cs_expired_active', $sessionIds, '期限切れの未完了セッションは対象');
        $this->assertNotContains('cs_expired_completed', $sessionIds, '完了済セッションはクリーンアップ対象外');
    }

    public function testNormalOrderHasNullAgentColumns(): void
    {
        // Order に追加した agent_protocol/agent_id が、通常購入相当の Order で NULL であることの回帰確認。
        $order = new Order();

        $this->assertNull($order->getAgentProtocol(), '通常購入では agent_protocol は NULL');
        $this->assertNull($order->getAgentId(), '通常購入では agent_id は NULL');
    }

    public function testOrderAgentColumnsAreSettable(): void
    {
        $order = new Order();
        $order->setAgentProtocol(CheckoutSession::PROTOCOL_ACP)->setAgentId('agent-123');

        $this->assertSame('acp', $order->getAgentProtocol());
        $this->assertSame('agent-123', $order->getAgentId());
    }
}
