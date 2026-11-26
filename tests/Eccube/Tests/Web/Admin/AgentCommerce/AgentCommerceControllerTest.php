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

namespace Eccube\Tests\Web\Admin\AgentCommerce;

use Eccube\Entity\CheckoutSession;
use Eccube\Entity\Master\AgentProtocol;
use Eccube\Entity\Master\CheckoutSessionStatus;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogCache;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogSearcher;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 管理画面 (コンテンツ管理 > Agent Commerce) の「期限切れセッションを回収」を検証する.
 *
 * cron を設定できない環境向けの代替導線。フロントのリクエストからは回収しない。
 */
final class AgentCommerceControllerTest extends AbstractAdminWebTestCase
{
    public function testIndexShowsExpireButton(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_content_agent_commerce'));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('form[action$="/content/agent_commerce/checkout_session/expire"]'), '回収ボタンを表示する');
    }

    public function testExpireOverdueSessions(): void
    {
        $overdue = $this->createHeldSession('-1 minutes');
        $fresh = $this->createHeldSession('+10 minutes');

        $this->client->request(Request::METHOD_POST, $this->generateUrl('admin_content_agent_commerce_checkout_session_expire'), [
            '_token' => 'dummy',
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_agent_commerce')));

        $this->entityManager->refresh($overdue);
        $this->entityManager->refresh($fresh);
        $this->assertSame(CheckoutSessionStatus::EXPIRED, $overdue->getStatus()?->getId(), '期限切れは expired');
        $this->assertSame(CheckoutSessionStatus::REQUIRES_ACTION, $fresh->getStatus()?->getId(), '期限内は据え置き');
    }

    /**
     * 事前生成した本文は、同じキーの search が返す本文と一致する (次ページ情報を含む).
     */
    public function testWarmupStoresSameBodyAsSearch(): void
    {
        // 既定のページサイズ (10 件) を超える公開商品を用意し、次ページがある状態にする。
        for ($i = 0; $i < UcpCatalogSearcher::DEFAULT_LIMIT + 1; ++$i) {
            $this->createProduct('事前生成テスト商品'.$i, 1);
        }
        $cache = static::getContainer()->get(UcpCatalogCache::class);
        $cache->clear();

        $this->client->request(Request::METHOD_POST, $this->generateUrl('admin_content_agent_commerce_ucp_catalog_cache_warmup'), [
            '_token' => 'dummy',
        ]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_agent_commerce')));

        $warmed = $cache->get($cache->buildKey('search', UcpCatalogCache::searchParams(null, UcpCatalogSearcher::DEFAULT_LIMIT, 0)));
        $this->assertNotNull($warmed, '既定の search と同じキーへ格納する');
        $expected = static::getContainer()->get(UcpCatalogSearcher::class)->searchBody(null, UcpCatalogSearcher::DEFAULT_LIMIT, 0);
        $this->assertSame($expected, $warmed, 'search と同じ本文');
        $this->assertTrue(json_decode($warmed, true)['pagination']['has_next_page'], '次ページ情報を失わない');
    }

    public function testExpireRejectsGet(): void
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_content_agent_commerce_checkout_session_expire'));

        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    private function createHeldSession(string $expiresIn): CheckoutSession
    {
        $session = (new CheckoutSession())
            ->setSessionId('cs_'.bin2hex(random_bytes(8)))
            ->setCurrencyCode('JPY')
            ->setProtocol($this->entityManager->find(AgentProtocol::class, AgentProtocol::UCP))
            ->setExpiresAt((new \DateTime())->modify($expiresIn))
            ->setStatus($this->entityManager->find(CheckoutSessionStatus::class, CheckoutSessionStatus::REQUIRES_ACTION));
        $this->entityManager->persist($session);
        $this->entityManager->flush();

        return $session;
    }
}
