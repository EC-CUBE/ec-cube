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

namespace Eccube\Service;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * サイト共通の構造化データ（JSON-LD / schema.org WebSite・Organization）を組み立てる Service.
 *
 * 店舗設定（BaseInfo）から WebSite / Organization の連想配列を組み立てて返す。
 * 戻り値は EccubeExtension の `json_ld` フィルタ経由で出力し、値に含まれる
 * `" < > &` などを機械的にエスケープする（テンプレート文字列補間による JSON 破壊・XSS を防ぐ）。
 *
 * 値が空の任意プロパティは出力しない（ProductStructuredDataService と同じ方針）。
 */
class SiteStructuredDataService
{
    use StructuredDataDescriptionTrait;

    /**
     * ロゴに用いる画像ファイル（帳票 PDF と共通の店舗ロゴ）.
     *
     * ファビコン（ICO）は Google Images の対応形式に含まれないため使用しない.
     */
    private const LOGO_FILE = 'assets/pdf/logo.png';

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Packages $packages,
        private readonly EccubeConfig $eccubeConfig,
    ) {
    }

    /**
     * サイト共通の JSON-LD 構造を組み立てて返す.
     *
     * WebSite と Organization は入れ子にせず `@graph` で並列のトップレベルノードとして出力し,
     * `WebSite.publisher` から `@id` で Organization を参照する
     * （schema.org の `author` は「作品の著者」であり、サイトを発行している組織は `publisher` が適切.
     * また `@context` を `@graph` の外側に 1 つだけ置くことで重複定義も避ける）.
     *
     * @return array<string, mixed>
     */
    public function createWebSiteJsonLd(BaseInfo $BaseInfo): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                $this->createWebSiteNode($BaseInfo),
                $this->createOrganizationJsonLd($BaseInfo),
            ],
        ];
    }

    /**
     * WebSite ノードを組み立てて返す（`@context` は持たない）.
     *
     * @return array<string, mixed>
     */
    private function createWebSiteNode(BaseInfo $BaseInfo): array
    {
        $siteUrl = $this->generateAbsoluteUrl('homepage');

        $data = [
            '@type' => 'WebSite',
            '@id' => $siteUrl.'#website',
            'name' => (string) $BaseInfo->getShopName(),
        ];
        $this->addIfNotEmpty($data, 'alternateName', $BaseInfo->getShopNameEng());
        $data['url'] = $siteUrl;
        $this->addIfNotEmpty($data, 'description', $this->normalizeDescription($BaseInfo->getGoodTraded()));
        $data['potentialAction'] = [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => $this->generateAbsoluteUrl('product_list').'?name={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ];
        $data['publisher'] = ['@id' => $siteUrl.'#organization'];

        return $data;
    }

    /**
     * Organization の JSON-LD 構造を組み立てて返す（`@context` は持たない）.
     *
     * @return array<string, mixed>
     */
    public function createOrganizationJsonLd(BaseInfo $BaseInfo): array
    {
        $siteUrl = $this->generateAbsoluteUrl('homepage');

        $data = [
            '@type' => 'Organization',
            '@id' => $siteUrl.'#organization',
            'url' => $siteUrl,
            'logo' => [
                '@type' => 'ImageObject',
                'contentUrl' => $this->buildLogoUrl(),
            ],
            'name' => (string) $BaseInfo->getShopName(),
        ];
        $this->addIfNotEmpty($data, 'alternateName', $BaseInfo->getShopNameEng());
        $this->addIfNotEmpty($data, 'legalName', $BaseInfo->getCompanyName());
        $this->addIfNotEmpty($data, 'description', $this->normalizeDescription($BaseInfo->getMessage()));
        // email01 は送信元(From)かつ全送信メールの BCC 先で、送信専用や店舗内部の運用アドレスが
        // 入る前提の項目なので公開しない。公開して良い連絡先は email02（問い合わせ専用）。
        $this->addIfNotEmpty($data, 'email', $BaseInfo->getEmail02());

        // phone_number は PhoneNumberType の TruncateHyphenListener と Assert\Type('digit') により
        // ハイフンなしの数字列（例 0312345678）で保存される。国番号 +81 を前置すると国内トランク
        // プレフィックスの 0 が残った不正な値（+81-0312345678）になるため、保存値をそのまま出力する。
        $phoneNumber = $BaseInfo->getPhoneNumber();
        if ($phoneNumber !== null && $phoneNumber !== '') {
            $data['telephone'] = $phoneNumber;
        }

        $address = $this->buildAddress($BaseInfo);
        if ($address !== null) {
            $data['address'] = $address;
        }

        $contactPoint = $this->buildContactPoint($BaseInfo, $phoneNumber);
        if ($contactPoint !== null) {
            $data['contactPoint'] = $contactPoint;
        }

        $invoiceRegistrationNumber = $BaseInfo->getInvoiceRegistrationNumber();
        if ($invoiceRegistrationNumber !== null && $invoiceRegistrationNumber !== '') {
            $data['iso6523Code'] = '0221:'.$invoiceRegistrationNumber;
        }

        return $data;
    }

    /**
     * PostalAddress 構造を組み立てる（住所要素が1つも無ければ null）.
     *
     * @return array<string, mixed>|null
     */
    private function buildAddress(BaseInfo $BaseInfo): ?array
    {
        $address = ['@type' => 'PostalAddress'];
        $this->addIfNotEmpty($address, 'streetAddress', $BaseInfo->getAddr02());
        $this->addIfNotEmpty($address, 'addressLocality', $BaseInfo->getAddr01());
        $this->addIfNotEmpty($address, 'addressRegion', $BaseInfo->getPref()?->getName());
        $this->addIfNotEmpty($address, 'postalCode', $BaseInfo->getPostalCode());

        // @type 以外に住所要素が無ければ出力しない
        if (count($address) === 1) {
            return null;
        }

        $address['addressCountry'] = 'JP';

        return $address;
    }

    /**
     * ContactPoint 構造を組み立てる（連絡手段が1つも無ければ null）.
     *
     * @return array<string, mixed>|null
     */
    private function buildContactPoint(BaseInfo $BaseInfo, ?string $phoneNumber): ?array
    {
        // 参照先が email02（問い合わせ専用メールアドレス）と contact（お問い合わせフォーム）なので
        // 用途は問い合わせ窓口で確定している
        $contactPoint = [
            '@type' => 'ContactPoint',
            'contactType' => 'customer support',
        ];
        if ($phoneNumber !== null && $phoneNumber !== '') {
            $contactPoint['telephone'] = $phoneNumber;
        }
        $this->addIfNotEmpty($contactPoint, 'email', $BaseInfo->getEmail02());

        // telephone / email が無ければ url だけの ContactPoint は出力しない
        if (!isset($contactPoint['telephone']) && !isset($contactPoint['email'])) {
            return null;
        }

        $contactPoint['url'] = $this->generateAbsoluteUrl('contact');

        return $contactPoint;
    }

    /**
     * 店舗ロゴの絶対URLを生成する.
     *
     * 参照先は帳票 PDF と同じ店舗ロゴで、優先順も `OrderPdfService` に揃える.
     * user_data に配置されていればそれを使い、無ければ既定ロゴ（管理画面テンプレート同梱）へフォールバックする.
     */
    private function buildLogoUrl(): string
    {
        $userDataLogo = $this->eccubeConfig->get('eccube_html_dir').'/user_data/'.self::LOGO_FILE;

        if (file_exists($userDataLogo)) {
            return $this->generateAbsoluteAssetUrl(self::LOGO_FILE, 'user_data');
        }

        return $this->generateAbsoluteAssetUrl(self::LOGO_FILE, 'admin');
    }

    /**
     * ルート名から絶対URLを生成する.
     */
    private function generateAbsoluteUrl(string $route): string
    {
        return $this->urlGenerator->generate($route, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * アセットの絶対URLを生成する.
     *
     * Packages::getUrl() は既定の設定ではルート相対パス（/html/user_data/...）を返すため,
     * 構造化データではスキーム込みホストを前置して絶対URLにする
     * （ProductStructuredDataService の画像URLと同じ方針）.
     * asset パッケージに base_urls（CDN 等）が設定されている場合は既に絶対URLなのでそのまま返す.
     * ただしプロトコル相対URL（`//cdn.example.com/...`）はスキームを持たず、JSON-LD 上は
     * `@base` の無い相対 IRI として解決先が曖昧になるため、スキームを補って絶対URLにする.
     */
    private function generateAbsoluteAssetUrl(string $path, string $package): string
    {
        $url = $this->packages->getUrl($path, $package);

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        if (str_starts_with($url, '//')) {
            return $this->urlGenerator->getContext()->getScheme().':'.$url;
        }

        return $this->getSchemeAndHttpHost().$url;
    }

    /**
     * ルーティングコンテキストからスキーム込みホスト（例: https://example.com）を返す.
     */
    private function getSchemeAndHttpHost(): string
    {
        $context = $this->urlGenerator->getContext();
        $scheme = $context->getScheme();
        $host = $context->getHost();
        $port = $scheme === 'https' ? $context->getHttpsPort() : $context->getHttpPort();

        if (($scheme === 'http' && $port !== 80) || ($scheme === 'https' && $port !== 443)) {
            $host .= ':'.$port;
        }

        return $scheme.'://'.$host;
    }

    /**
     * 値が null / 空文字でない場合のみ連想配列へ追加する.
     *
     * @param array<string, mixed> $data
     */
    private function addIfNotEmpty(array &$data, string $key, ?string $value): void
    {
        if ($value !== null && $value !== '') {
            $data[$key] = $value;
        }
    }
}
