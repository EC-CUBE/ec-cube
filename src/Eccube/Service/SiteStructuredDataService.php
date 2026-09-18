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
     * ロゴに用いる画像ファイル（`html/user_data` 基準の構造化データ専用の置き場）.
     *
     * 既定では存在しないため、店舗が管理画面のファイル管理から配置したときだけ `logo` を出力する.
     * 幅・高さともに 112px 以上で、Google Images の対応形式（PNG 等）の画像を置く前提
     * （要件はコード側で検証せずドキュメントで案内する）.
     *
     * ファビコン（ICO）は Google Images の対応形式に含まれないため使用しない.
     * 帳票 PDF のロゴ（`assets/pdf/logo.png`）とも共有しない. こちらはインストール時に
     * 管理画面同梱の 301x38 が配置されるため既定で必ず存在し、共有すると差し替えていない店舗でも
     * EC-CUBE 既定のロゴが出力されてしまう. また `OrderPdfService` は幅 40mm 固定・高さは
     * アスペクト比で描画するため、上記の要件を満たす画像を置くと帳票のレイアウトが崩れる.
     */
    private const LOGO_FILE = 'assets/img/common/logo.png';

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
            'name' => (string) $BaseInfo->getShopName(),
        ];

        $logoUrl = $this->buildLogoUrl();
        if ($logoUrl !== null) {
            $data['logo'] = [
                '@type' => 'ImageObject',
                'contentUrl' => $logoUrl,
            ];
        }

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
     * 店舗ロゴの絶対URLを生成する（ファイルが配置されていなければ null）.
     *
     * `logo` は任意プロパティなので、未配置の店舗では省略する.
     * 画像のサイズ・形式はリクエストごとに検証せず、ドキュメントで案内する.
     */
    private function buildLogoUrl(): ?string
    {
        $path = $this->eccubeConfig->get('eccube_html_dir').'/user_data/'.self::LOGO_FILE;

        if (!file_exists($path)) {
            return null;
        }

        return $this->generateAbsoluteAssetUrl(self::LOGO_FILE, 'user_data');
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
