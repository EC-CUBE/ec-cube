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

namespace Eccube\Service\AgentCommerce;

use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Master\Country;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Shipping;
use Eccube\Repository\Master\CountryIsoCodeRepository;
use Eccube\Repository\Master\PrefRepository;

/**
 * EC-CUBE の住所系エンティティ (Customer / CustomerAddress / Shipping) を
 * ACP / UCP の住所 DTO へ写すためのマッピングサービス。
 *
 * - 国コードは Country.id (ISO 3166-1 numeric) を alpha-2 へ変換する。変換表は
 *   マスタ mtb_country_iso_code (id=ISO numeric, name=alpha-2) で管理し、
 *   CountryIsoCodeRepository 経由で解決する (コードにハードコードしない)。
 * - region は Pref 名 (例: 東京都) をそのまま返す。
 */
class AddressMappingService
{
    public function __construct(
        private readonly CountryIsoCodeRepository $countryIsoCodeRepository,
        private readonly PrefRepository $prefRepository,
    ) {
    }

    /**
     * ISO 3166-1 numeric (mtb_country.id) を alpha-2 へ変換する。
     * mtb_country_iso_code マスタに無い id / null は null を返す。
     */
    public function getAlpha2FromCountryId(?int $numericCountryId): ?string
    {
        if ($numericCountryId === null) {
            return null;
        }

        return $this->countryIsoCodeRepository->find($numericCountryId)?->getName();
    }

    /**
     * Pref から region 文字列 (都道府県名) を返す。null は null を返す。
     */
    public function getRegionFromPref(?Pref $pref): ?string
    {
        if ($pref === null) {
            return null;
        }

        return $pref->getName();
    }

    /**
     * region 文字列 (都道府県名) から Pref を逆引きする。
     *
     * UCP の postal address `region` は EC-CUBE では Pref 名 (例: 東京都) に対応する。
     * ISO 3166-2:JP コード (JP-13 等) での指定は標準では解決せず、app/Customize での
     * 拡張余地とする (本サービスを decoration して解決ロジックを差し替え可能)。
     */
    public function getPrefFromRegion(?string $region): ?Pref
    {
        if ($region === null || $region === '') {
            return null;
        }

        return $this->prefRepository->findOneBy(['name' => $region]);
    }

    /**
     * 住所系エンティティを ACP / UCP の住所 DTO 相当の配列へ写す。
     *
     * @return array{
     *     family_name: ?string,
     *     given_name: ?string,
     *     family_name_kana: ?string,
     *     given_name_kana: ?string,
     *     company: ?string,
     *     postal_code: ?string,
     *     region: ?string,
     *     address1: ?string,
     *     address2: ?string,
     *     country: ?string,
     *     phone: ?string
     * }
     */
    public function toAddressArray(Customer|CustomerAddress|Shipping $source): array
    {
        $country = $this->extractCountry($source);
        $countryId = $country !== null ? $country->getId() : null;

        return [
            'family_name' => $this->callIfExists($source, 'getName01'),
            'given_name' => $this->callIfExists($source, 'getName02'),
            'family_name_kana' => $this->callIfExists($source, 'getKana01'),
            'given_name_kana' => $this->callIfExists($source, 'getKana02'),
            'company' => $this->callIfExists($source, 'getCompanyName'),
            'postal_code' => $this->callIfExists($source, 'getPostalCode'),
            'region' => $this->getRegionFromPref($this->extractPref($source)),
            'address1' => $this->callIfExists($source, 'getAddr01'),
            'address2' => $this->callIfExists($source, 'getAddr02'),
            'country' => $this->getAlpha2FromCountryId($countryId),
            'phone' => $this->extractPhoneNumber($source),
        ];
    }

    private function extractCountry(Customer|CustomerAddress|Shipping $source): ?Country
    {
        // Customer / CustomerAddress / Shipping いずれも getCountry() を持つ。
        return $source->getCountry();
    }

    private function extractPref(Customer|CustomerAddress|Shipping $source): ?Pref
    {
        // Customer / CustomerAddress / Shipping いずれも getPref() を持つ。
        return $source->getPref();
    }

    /**
     * 電話番号を取得する。
     * Customer / CustomerAddress / Shipping いずれも getPhoneNumber() を持つ。
     */
    private function extractPhoneNumber(Customer|CustomerAddress|Shipping $source): ?string
    {
        return $source->getPhoneNumber();
    }

    /**
     * 指定 getter が存在すれば呼び出して文字列(or null)を返す。存在しなければ null。
     */
    private function callIfExists(Customer|CustomerAddress|Shipping $source, string $method): ?string
    {
        if (!method_exists($source, $method)) {
            return null;
        }

        try {
            $value = $source->$method();
        } catch (\TypeError) {
            // 一部エンティティ (Shipping の getName01/getKana01 等) は getter の戻り型が
            // 非 null の string だが、値が未設定だと TypeError になる。null 扱いにする。
            return null;
        }

        return $value === null ? null : (string) $value;
    }
}
