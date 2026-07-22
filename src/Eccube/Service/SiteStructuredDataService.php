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
    /**
     * ロゴに用いるファビコン画像ファイル（user_data 配下）.
     */
    private const LOGO_FILE = 'assets/img/common/favicon.ico';

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Packages $packages,
    ) {
    }

    /**
     * サイト共通の JSON-LD 構造（WebSite。author に Organization を内包）を組み立てて返す.
     *
     * @return array<string, mixed>
     */
    public function createWebSiteJsonLd(BaseInfo $BaseInfo): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => (string) $BaseInfo->getShopName(),
        ];
        $this->addIfNotEmpty($data, 'alternateName', $BaseInfo->getShopNameEng());
        $data['url'] = $this->generateAbsoluteUrl('homepage');
        $this->addIfNotEmpty($data, 'description', $BaseInfo->getGoodTraded());
        $data['potentialAction'] = [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => $this->generateAbsoluteUrl('product_list').'?name={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ];

        $copyrightYear = $BaseInfo->getCopyrightYear();
        if ($copyrightYear !== null) {
            $data['copyrightYear'] = $copyrightYear;
        }

        $data['author'] = $this->createOrganizationJsonLd($BaseInfo);

        return $data;
    }

    /**
     * Organization の JSON-LD 構造を組み立てて返す.
     *
     * @return array<string, mixed>
     */
    public function createOrganizationJsonLd(BaseInfo $BaseInfo): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'url' => $this->generateAbsoluteUrl('homepage'),
            'logo' => [
                '@type' => 'ImageObject',
                'contentUrl' => $this->packages->getUrl(self::LOGO_FILE, 'user_data'),
            ],
            'name' => (string) $BaseInfo->getShopName(),
        ];
        $this->addIfNotEmpty($data, 'alternateName', $BaseInfo->getShopNameEng());
        $this->addIfNotEmpty($data, 'legalName', $BaseInfo->getCompanyName());
        $this->addIfNotEmpty($data, 'description', $BaseInfo->getMessage());
        $this->addIfNotEmpty($data, 'image', $BaseInfo->getSiteImage());
        $this->addIfNotEmpty($data, 'email', $BaseInfo->getEmail01());

        $phoneNumber = $BaseInfo->getPhoneNumber();
        if ($phoneNumber !== null && $phoneNumber !== '') {
            $data['telephone'] = '+81-'.$phoneNumber;
        }

        $address = $this->buildAddress($BaseInfo);
        if ($address !== null) {
            $data['address'] = $address;
        }

        $contactPoint = $this->buildContactPoint($BaseInfo, $phoneNumber);
        if ($contactPoint !== null) {
            $data['contactPoint'] = $contactPoint;
        }

        $foundingDate = $BaseInfo->getFoundingDate();
        if ($foundingDate !== null) {
            $data['foundingDate'] = $foundingDate->format('Y-m-d');
        }

        $numberOfEmployees = $BaseInfo->getNumberOfEmployees();
        if ($numberOfEmployees !== null) {
            $data['numberOfEmployees'] = [
                '@type' => 'QuantitativeValue',
                'value' => $numberOfEmployees,
            ];
        }

        $invoiceRegistrationNumber = $BaseInfo->getInvoiceRegistrationNumber();
        if ($invoiceRegistrationNumber !== null && $invoiceRegistrationNumber !== '') {
            $data['iso6523Code'] = '0221:'.$invoiceRegistrationNumber;
        }

        $sameAs = $this->buildSameAs($BaseInfo->getSameAs());
        if ($sameAs !== []) {
            $data['sameAs'] = $sameAs;
        }

        return $data;
    }

    /**
     * 改行区切りの SNS 等公式 URL 文字列を、空要素を除いた URL のリストに変換する.
     *
     * @return list<string>
     */
    private function buildSameAs(?string $sameAs): array
    {
        if ($sameAs === null || $sameAs === '') {
            return [];
        }

        $urls = preg_split('/\R/u', $sameAs) ?: [];
        $urls = array_map(trim(...), $urls);
        $urls = array_filter($urls, static fn (string $url): bool => $url !== '');

        return array_values($urls);
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
        $contactPoint = ['@type' => 'ContactPoint'];
        if ($phoneNumber !== null && $phoneNumber !== '') {
            $contactPoint['telephone'] = '+81-'.$phoneNumber;
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
     * ルート名から絶対URLを生成する.
     */
    private function generateAbsoluteUrl(string $route): string
    {
        return $this->urlGenerator->generate($route, [], UrlGeneratorInterface::ABSOLUTE_URL);
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
