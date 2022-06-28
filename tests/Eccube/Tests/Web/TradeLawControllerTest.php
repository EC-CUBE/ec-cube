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

namespace Eccube\Tests\Web;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Page;
use Eccube\Entity\TradeLaw;
use Eccube\Repository\TradeLawRepository;

class TradeLawControllerTest extends AbstractWebTestCase
{
    /**
     * @var TradeLawRepository
     */
    private $tradeLawRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tradeLawRepository = $this->entityManager->getRepository(TradeLaw::class);
    }
    public function testRoutingIndex()
    {
        $this->client->request('GET', $this->generateUrl('tradelaw'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * 取引法を無効にすると、特定商取引法ページに取引法テスト文字が表示されないことを確認すること。
     * Check that with no trade law enabled, no trade law test will appear on the tradelaw page.
     * @return void
     */
    public function testNoTradeLawsEnabled() {
        // Disable all trade laws
        $tradeLaws = $this->tradeLawRepository->findAll();
        $id = 0;
        foreach($tradeLaws as $tradeLaw) {
            $tradeLaw->setName(sprintf('Trade名称_%s', $id));
            $tradeLaw->setDescription(sprintf('Trade説明_%s', $id));
            $tradeLaw->setDisplayOrderScreen(false);
            $id++;
        }
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', $this->generateUrl('tradelaw'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        for ($i = 0; $i < $id; $i++) {
            $this->assertStringNotContainsString('Trade名称_'.$i, $crawler->outerHtml());
            $this->assertStringNotContainsString('Trade説明_'.$i, $crawler->outerHtml());
        }
    }

    /**
     * Check that with all trade laws enabled that trade law text will appear on the tradelaw page.
     * すべての取引法を有効にすると、取引法のテキストが特定商取引法ページに表示されることを確認すること。
     * @return void
     */
    public function testTradeLawsEnabled() {
        // Enable all trade laws
        $tradeLaws = $this->tradeLawRepository->findBy([], ['sortNo' => 'ASC']);
        $id = 0;
        foreach($tradeLaws as $tradeLaw) {
            $tradeLaw->setName(sprintf('Trade名称_%s', $id));
            $tradeLaw->setDescription(sprintf('Trade説明_%s', $id));
            $tradeLaw->setDisplayOrderScreen(true);
            $id++;
        }
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', $this->generateUrl('tradelaw'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        for ($i = 0; $i < $id; $i++) {
            $this->assertStringContainsString('Trade名称_'.$i, $crawler->outerHtml());
            $this->assertStringContainsString('Trade説明_'.$i, $crawler->outerHtml());
        }
    }

}
