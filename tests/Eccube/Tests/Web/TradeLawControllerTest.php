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

use Eccube\Entity\Page;
use Eccube\Entity\TradeLaw;
use Eccube\Repository\TradeLawRepository;
use Symfony\Component\HttpFoundation\Request;

class TradeLawControllerTest extends AbstractWebTestCase
{
    private ?TradeLawRepository $tradeLawRepository = null;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->tradeLawRepository = $this->entityManager->getRepository(TradeLaw::class);
    }

    public function testRoutingIndex()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('help_tradelaw'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Ensure that the line with both the name/description registered appears on the specific transaction law page.
     * 名称/説明の両方が登録されている行が、特定商取引法ページに表示されることを確認する。
     */
    public function testTradeLawsNotEmpty(): void
    {
        $tradeLaws = $this->tradeLawRepository->findBy([], ['sortNo' => 'ASC']);
        $id = 0;
        foreach ($tradeLaws as $tradeLaw) {
            $tradeLaw->setName(sprintf('Trade名称_%s', $id));
            $tradeLaw->setDescription(sprintf('Trade説明_%s', $id));
            $id++;
        }
        $this->entityManager->flush();

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('help_tradelaw'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        for ($i = 0; $i < $id; $i++) {
            $this->assertStringContainsString('Trade名称_'.$i, $crawler->outerHtml());
            $this->assertStringContainsString('Trade説明_'.$i, $crawler->outerHtml());
        }
    }

    /**
     * Ensure that lines that do not have both a name/description registered do not appear on the specific transaction law page.
     * 名称/説明の両方が登録されていない行は、特定商取引法ページに表示されないことを確認する。
     */
    public function testTradeLawsEmpty(): void
    {
        $tradeLaws = $this->tradeLawRepository->findBy([], ['sortNo' => 'ASC']);
        $id = 0;
        foreach ($tradeLaws as $tradeLaw) {
            $tradeLaw->setName(sprintf('Trade名称_%s', $id));
            $tradeLaw->setDescription('');
            $id++;
        }
        $this->entityManager->flush();

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('help_tradelaw'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        for ($i = 0; $i < $id; $i++) {
            $this->assertStringNotContainsString('Trade名称_'.$i, $crawler->outerHtml());
        }
    }
}
