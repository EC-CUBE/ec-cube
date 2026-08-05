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

    /**
     * WebSite と Organization は @graph で並列のトップレベルノードとして出力する.
     */
    public function testWebSiteBaseStructure(): void
    {
        $data = $this->service->createWebSiteJsonLd($this->BaseInfo);

        $this->assertSame('https://schema.org', $data['@context']);
        $this->assertCount(2, $data['@graph']);

        [$webSite, $organization] = $data['@graph'];

        $this->assertSame('WebSite', $webSite['@type']);
        $this->assertSame((string) $this->BaseInfo->getShopName(), $webSite['name']);
        $this->assertStringStartsWith('http', $webSite['url']);
        $this->assertSame('SearchAction', $webSite['potentialAction']['@type']);
        $this->assertSame('Organization', $organization['@type']);

        // @context はトップレベルに 1 つだけ持ち、各ノードは持たない
        $this->assertArrayNotHasKey('@context', $webSite);
        $this->assertArrayNotHasKey('@context', $organization);
    }

    /**
     * WebSite.publisher は Organization を @id で参照する（author に内包しない）.
     */
    public function testWebSitePublisherReferencesOrganizationById(): void
    {
        $data = $this->service->createWebSiteJsonLd($this->BaseInfo);
        [$webSite, $organization] = $data['@graph'];

        $this->assertArrayNotHasKey('author', $webSite);
        $this->assertSame($organization['@id'], $webSite['publisher']['@id']);
        $this->assertNotSame($organization['@id'], $webSite['@id']);
    }

    public function testOrganizationBaseStructure(): void
    {
        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertSame('Organization', $data['@type']);
        $this->assertStringStartsWith('http', $data['url']);
        $this->assertSame('ImageObject', $data['logo']['@type']);
        $this->assertNotEmpty($data['logo']['contentUrl']);
        $this->assertSame((string) $this->BaseInfo->getShopName(), $data['name']);
    }

    /**
     * logo.contentUrl は絶対URLで出力する（ProductStructuredDataService の画像URLと同じ方針）.
     *
     * Packages::getUrl() はルート相対パス（/html/user_data/...）を返すため、
     * スキーム込みホストが前置されていることを検証する。
     */
    public function testLogoContentUrlIsAbsolute(): void
    {
        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);
        $contentUrl = (string) $data['logo']['contentUrl'];
        $url = (string) $data['url'];

        $this->assertMatchesRegularExpression('#^https?://#', $contentUrl);

        // url と同じスキーム・ホストで出力される
        $this->assertStringStartsWith(
            parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST),
            $contentUrl
        );

        // asset パッケージが解決したパスは保持される。参照先は帳票 PDF と共通の店舗ロゴで、
        // user_data に配置されていればそれを使い、無ければ管理画面テンプレート同梱の既定ロゴになる
        $this->assertMatchesRegularExpression(
            '#(/html/user_data/assets/pdf/logo\.png|/html/template/admin/assets/pdf/logo\.png)#',
            $contentUrl
        );
    }

    /**
     * telephone は保存値をそのまま出力する（国番号 +81 を前置しない）.
     *
     * phone_number は PhoneNumberType の TruncateHyphenListener と Assert\Type('digit') により
     * ハイフンなしの数字列で保存されるため、+81 を前置すると国内トランクプレフィックスの 0 が残る。
     */
    public function testTelephoneIsOutputAsStored(): void
    {
        $this->BaseInfo->setPhoneNumber('0312345678');

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertSame('0312345678', $data['telephone']);
        $this->assertSame('0312345678', $data['contactPoint']['telephone']);
        $this->assertSame('customer support', $data['contactPoint']['contactType']);
    }

    /**
     * description は HTML 除去・空白正規化・300 文字丸めを通す（ProductStructuredDataService と同じ扱い）.
     */
    public function testDescriptionIsNormalized(): void
    {
        $this->BaseInfo->setMessage("<p>店舗からの\nメッセージ</p>".str_repeat('あ', 400));

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertStringNotContainsString('<p>', $data['description']);
        $this->assertStringNotContainsString("\n", $data['description']);
        $this->assertSame(300, mb_strlen($data['description']));
    }

    /**
     * email は email01（送信元・BCC 先）ではなく email02（問い合わせ専用）を出力する.
     */
    public function testEmailUsesContactAddressNotSenderAddress(): void
    {
        $this->BaseInfo->setEmail01('sender@example.com');
        $this->BaseInfo->setEmail02('contact@example.com');

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertSame('contact@example.com', $data['email']);
    }

    public function testEmptyOptionalPropertiesAreOmitted(): void
    {
        $this->BaseInfo->setShopNameEng(null);
        $this->BaseInfo->setGoodTraded(null);

        $data = $this->service->createWebSiteJsonLd($this->BaseInfo);
        [$webSite, $organization] = $data['@graph'];

        $this->assertArrayNotHasKey('alternateName', $webSite);
        $this->assertArrayNotHasKey('description', $webSite);
        $this->assertArrayNotHasKey('alternateName', $organization);
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
