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

namespace Eccube\Tests\Service;

use Eccube\Entity\BaseInfo;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\SiteStructuredDataService;

final class SiteStructuredDataServiceTest extends AbstractServiceTestCase
{
    private ?SiteStructuredDataService $service = null;

    private ?BaseInfo $BaseInfo = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = static::getContainer()->get(SiteStructuredDataService::class);
        $this->BaseInfo = static::getContainer()->get(BaseInfoRepository::class)->get();
    }

    public function testWebSiteBaseStructure(): void
    {
        $data = $this->service->createWebSiteJsonLd($this->BaseInfo);

        $this->assertSame('https://schema.org', $data['@context']);
        $this->assertSame('WebSite', $data['@type']);
        $this->assertSame((string) $this->BaseInfo->getShopName(), $data['name']);
        $this->assertStringStartsWith('http', $data['url']);
        $this->assertSame('SearchAction', $data['potentialAction']['@type']);
        $this->assertArrayHasKey('author', $data);
        $this->assertSame('Organization', $data['author']['@type']);
    }

    public function testOrganizationBaseStructure(): void
    {
        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertSame('https://schema.org', $data['@context']);
        $this->assertSame('Organization', $data['@type']);
        $this->assertStringStartsWith('http', $data['url']);
        $this->assertSame('ImageObject', $data['logo']['@type']);
        $this->assertNotEmpty($data['logo']['contentUrl']);
        $this->assertSame((string) $this->BaseInfo->getShopName(), $data['name']);
    }

    public function testEmptyOptionalPropertiesAreOmitted(): void
    {
        $this->BaseInfo->setShopNameEng(null);
        $this->BaseInfo->setGoodTraded(null);

        $data = $this->service->createWebSiteJsonLd($this->BaseInfo);

        $this->assertArrayNotHasKey('alternateName', $data);
        $this->assertArrayNotHasKey('description', $data);
        $this->assertArrayNotHasKey('alternateName', $data['author']);
    }

    public function testPrefNullOmitsAddressRegionWithoutError(): void
    {
        $this->BaseInfo->setPref(null);

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        // 都道府県未設定でも例外にならず、addressRegion のみ欠落する
        $this->assertSame('Organization', $data['@type']);
        if (isset($data['address'])) {
            $this->assertArrayNotHasKey('addressRegion', $data['address']);
        }
    }

    public function testInvoiceRegistrationNumberIsIncludedWhenPresent(): void
    {
        $this->BaseInfo->setInvoiceRegistrationNumber('T1234567890123');

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertSame('0221:T1234567890123', $data['iso6523Code']);
    }

    public function testEmptyInvoiceRegistrationNumberIsOmitted(): void
    {
        $this->BaseInfo->setInvoiceRegistrationNumber('');

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        // 空文字の場合は "0221:" だけの iso6523Code を出力しない
        $this->assertArrayNotHasKey('iso6523Code', $data);
    }
}
