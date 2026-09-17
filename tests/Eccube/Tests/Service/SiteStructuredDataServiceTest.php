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
use Symfony\Component\Filesystem\Filesystem;

final class SiteStructuredDataServiceTest extends AbstractServiceTestCase
{
    /**
     * 構造化データ専用のロゴの配置パス（`html/user_data` 基準）.
     */
    private const LOGO_FILE = 'assets/img/common/logo.png';

    /**
     * 配置テスト用の 1x1 PNG.
     */
    private const LOGO_PNG_1X1 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+ip1sAAAAASUVORK5CYII=';

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
        $this->assertSame((string) $this->BaseInfo->getShopName(), $data['name']);
    }

    /**
     * logo は専用パスに画像が配置されているときだけ出力する.
     *
     * 参照先は構造化データ専用の `html/user_data/assets/img/common/logo.png` で、既定では存在しない。
     * 画像のサイズ・形式はリクエストごとに検証せずドキュメントで案内するため、有無だけで判断する。
     */
    public function testLogoIsOmittedWhenNotPlaced(): void
    {
        $logoPath = $this->logoPath();
        if (file_exists($logoPath)) {
            $this->markTestSkipped('ロゴが配置済みの環境ではスキップする: '.$logoPath);
        }

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertArrayNotHasKey('logo', $data);
    }

    /**
     * 配置済みの logo は絶対URLで出力する.
     *
     * Packages::getUrl() はルート相対パスを返すため、ホストを前置して絶対URLにする。
     */
    public function testPlacedLogoIsOutputAsAbsoluteUrl(): void
    {
        $created = $this->placeLogo();

        try {
            $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

            $contentUrl = (string) $data['logo']['contentUrl'];
            $url = (string) $data['url'];

            $this->assertSame('ImageObject', $data['logo']['@type']);
            $this->assertMatchesRegularExpression('#^https?://#', $contentUrl);
            $this->assertStringStartsWith(
                parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST),
                $contentUrl
            );
            $this->assertStringContainsString('/html/user_data/'.self::LOGO_FILE, $contentUrl);
        } finally {
            if ($created) {
                unlink($this->logoPath());
            }
        }
    }

    /**
     * テスト用に 1x1 の PNG を配置する（既に配置されている場合はそれを使う）.
     *
     * @return bool このテストで作成した場合のみ true（後片付けで削除する対象）
     */
    private function placeLogo(): bool
    {
        $logoPath = $this->logoPath();
        if (file_exists($logoPath)) {
            return false;
        }

        (new Filesystem())->dumpFile($logoPath, (string) base64_decode(self::LOGO_PNG_1X1, true));

        return true;
    }

    /**
     * ロゴの配置パスを返す.
     */
    private function logoPath(): string
    {
        return static::getContainer()->getParameter('kernel.project_dir').'/html/user_data/'.self::LOGO_FILE;
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

        $this->assertStringNotContainsString('<p>', (string) $data['description']);
        $this->assertStringNotContainsString("\n", (string) $data['description']);
        $this->assertSame(300, mb_strlen((string) $data['description']));
    }

    /**
     * 隣接するブロック要素の境界は空白として保持する.
     *
     * strip_tags() はタグを削除するだけなので、前処理なしでは「商品A商品B」と連結してしまう。
     */
    public function testAdjacentBlockElementsKeepSeparation(): void
    {
        $this->BaseInfo->setMessage('<p>商品A</p><p>商品B</p>商品C<br>商品D');

        $data = $this->service->createOrganizationJsonLd($this->BaseInfo);

        $this->assertSame('商品A 商品B 商品C 商品D', $data['description']);
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
        [$webSite] = $data['@graph'];

        // copyrightYear は WebSite ノードに載る（Organization ではない）
        $this->assertSame('WebSite', $webSite['@type']);
        $this->assertSame(2020, $webSite['copyrightYear']);
    }

    public function testOptionalSchemaFieldsAreOmittedWhenUnset(): void
    {
        $this->BaseInfo->setSameAs(null);
        $this->BaseInfo->setFoundingDate(null);
        $this->BaseInfo->setNumberOfEmployees(null);
        $this->BaseInfo->setSiteImage(null);
        $this->BaseInfo->setCopyrightYear(null);

        $org = $this->service->createOrganizationJsonLd($this->BaseInfo);
        [$webSite] = $this->service->createWebSiteJsonLd($this->BaseInfo)['@graph'];

        $this->assertArrayNotHasKey('sameAs', $org);
        $this->assertArrayNotHasKey('foundingDate', $org);
        $this->assertArrayNotHasKey('numberOfEmployees', $org);
        $this->assertArrayNotHasKey('image', $org);
        $this->assertArrayNotHasKey('copyrightYear', $webSite);
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
