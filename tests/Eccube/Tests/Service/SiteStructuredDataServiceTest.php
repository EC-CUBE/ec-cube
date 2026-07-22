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
use Eccube\Entity\OpeningHours;
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

    public function testSameAsIsSplitIntoListAndTrimmed(): void
    {
        $this->BaseInfo->setSameAs("https://example.com/a\n  https://example.com/b  \n\nhttps://example.com/c");

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        // 改行区切り→trim→空行除去でURLのリストになる
        $this->assertSame([
            'https://example.com/a',
            'https://example.com/b',
            'https://example.com/c',
        ], $data['sameAs']);
    }

    public function testEmptySameAsIsOmitted(): void
    {
        $this->BaseInfo->setSameAs("  \n  ");

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertArrayNotHasKey('sameAs', $data);
    }

    public function testFoundingDateIsFormatted(): void
    {
        $this->BaseInfo->setFoundingDate(new \DateTime('2000-04-01'));

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertSame('2000-04-01', $data['foundingDate']);
    }

    public function testNumberOfEmployeesIsQuantitativeValue(): void
    {
        $this->BaseInfo->setNumberOfEmployees(42);

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertSame('QuantitativeValue', $data['numberOfEmployees']['@type']);
        $this->assertSame(42, $data['numberOfEmployees']['value']);
    }

    public function testSiteImageIsOutputAsImage(): void
    {
        $this->BaseInfo->setSiteImage('https://example.com/site.png');

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertSame('https://example.com/site.png', $data['image']);
    }

    public function testCopyrightYearIsOutputOnWebSite(): void
    {
        $this->BaseInfo->setCopyrightYear(2020);

        $data = $this->service->createWebSiteJsonLd($this->BaseInfo);

        $this->assertSame(2020, $data['copyrightYear']);
    }

    public function testOptionalSchemaFieldsAreOmittedWhenUnset(): void
    {
        $this->BaseInfo->setSameAs(null);
        $this->BaseInfo->setFoundingDate(null);
        $this->BaseInfo->setNumberOfEmployees(null);
        $this->BaseInfo->setSiteImage(null);
        $this->BaseInfo->setCopyrightYear(null);

        $org = $this->service->createOrganizationJsonLd($this->BaseInfo);
        $web = $this->service->createWebSiteJsonLd($this->BaseInfo);

        $this->assertArrayNotHasKey('sameAs', $org);
        $this->assertArrayNotHasKey('foundingDate', $org);
        $this->assertArrayNotHasKey('numberOfEmployees', $org);
        $this->assertArrayNotHasKey('image', $org);
        $this->assertArrayNotHasKey('copyrightYear', $web);
    }

    public function testOpeningHoursSpecification(): void
    {
        $this->BaseInfo->getOpeningHours()->clear();
        $OpeningHours = new OpeningHours();
        $OpeningHours->setDayOfWeek(['Monday', 'Tuesday']);
        $OpeningHours->setOpens(new \DateTime('09:00'));
        $OpeningHours->setCloses(new \DateTime('18:00'));
        $this->BaseInfo->addOpeningHour($OpeningHours);

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertArrayHasKey('openingHoursSpecification', $data);
        $spec = $data['openingHoursSpecification'][0];
        $this->assertSame('OpeningHoursSpecification', $spec['@type']);
        $this->assertSame(['Monday', 'Tuesday'], $spec['dayOfWeek']);
        $this->assertSame('09:00', $spec['opens']);
        $this->assertSame('18:00', $spec['closes']);
    }

    public function testEmptyOpeningHoursIsOmitted(): void
    {
        $this->BaseInfo->getOpeningHours()->clear();

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertArrayNotHasKey('openingHoursSpecification', $data);
    }
}
