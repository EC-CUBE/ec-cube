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

namespace Eccube\Tests\Service\AgentCommerce;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\Country;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Shipping;
use Eccube\Service\AgentCommerce\AddressMappingService;
use Eccube\Tests\EccubeTestCase;

/**
 * Layer 2 tests for AddressMappingService.
 *
 * 国コード変換はマスタ mtb_country_iso_code (CountryIsoCodeRepository) 経由になったため、
 * fixtures を読み込んだ DB 上で検証する。ISO 3166-1 numeric (mtb_country.id) -> alpha-2 の
 * 解決、mtb_country.csv の全 id がマスタで解決できること、姓名分離・DTO 整形を確認する。
 */
final class AddressMappingServiceTest extends EccubeTestCase
{
    private ?AddressMappingService $service = null;

    protected function setUp(): void
    {
        parent::setUp();
        // AddressMappingService はまだ consumer が無く private では除去されるため、
        // services_test.yaml で public 化してコンテナから取得する。
        $this->service = self::getContainer()->get(AddressMappingService::class);
    }

    public function testGetAlpha2FromCountryIdKnownIds(): void
    {
        $this->assertSame('JP', $this->service->getAlpha2FromCountryId(392), 'ISO 3166-1 numeric 392 is Japan -> JP');
        $this->assertSame('US', $this->service->getAlpha2FromCountryId(840), 'ISO 3166-1 numeric 840 is United States -> US');
        $this->assertSame('GB', $this->service->getAlpha2FromCountryId(826), 'ISO 3166-1 numeric 826 is United Kingdom -> GB');
        $this->assertSame('CN', $this->service->getAlpha2FromCountryId(156), 'ISO 3166-1 numeric 156 is China -> CN');
        $this->assertSame('KR', $this->service->getAlpha2FromCountryId(410), 'ISO 3166-1 numeric 410 is Republic of Korea -> KR');
        $this->assertSame('RU', $this->service->getAlpha2FromCountryId(643), 'ISO 3166-1 numeric 643 is Russia -> RU');
    }

    public function testGetAlpha2FromCountryIdNullAndUnknown(): void
    {
        $this->assertNull($this->service->getAlpha2FromCountryId(null), 'Null country id must yield null alpha-2');
        $this->assertNull($this->service->getAlpha2FromCountryId(999), 'Unknown numeric id must yield null alpha-2 (no exception)');
    }

    /**
     * mtb_country.csv に存在する全 numeric id が mtb_country_iso_code マスタで
     * 非 null の alpha-2 に解決できること (どの国でも住所変換が破綻しない保証)。
     */
    public function testAllCountryIdsResolveViaMaster(): void
    {
        $ids = $this->loadCountryIdsFromCsv();
        $this->assertNotEmpty($ids, 'mtb_country.csv must contain country rows for this assertion to be meaningful');

        foreach ($ids as $id) {
            $alpha2 = $this->service->getAlpha2FromCountryId($id);
            $this->assertNotNull($alpha2, sprintf('mtb_country id %d must resolve to a non-null alpha-2 via mtb_country_iso_code', $id));
            $this->assertMatchesRegularExpression('/^[A-Z]{2}$/', $alpha2, sprintf('mtb_country id %d must map to a two-letter uppercase alpha-2 code', $id));
        }
    }

    public function testGetRegionFromPref(): void
    {
        $pref = new Pref();
        $pref->setId(13);
        $pref->setName('東京都');

        $this->assertSame('東京都', $this->service->getRegionFromPref($pref), 'Region must be the prefecture name');
        $this->assertNull($this->service->getRegionFromPref(null), 'Null prefecture must yield null region');
    }

    public function testToAddressArrayFromCustomerSplitsNameAndMapsCountry(): void
    {
        $country = new Country();
        $country->setId(392);

        $pref = new Pref();
        $pref->setId(13);
        $pref->setName('東京都');

        $customer = new Customer();
        $customer->setName01('山田');
        $customer->setName02('太郎');
        $customer->setKana01('ヤマダ');
        $customer->setKana02('タロウ');
        $customer->setCompanyName('株式会社テスト');
        $customer->setPostalCode('1000001');
        $customer->setPref($pref);
        $customer->setAddr01('千代田区千代田');
        $customer->setAddr02('1-1');
        $customer->setCountry($country);
        $customer->setPhoneNumber('0312345678');

        $address = $this->service->toAddressArray($customer);

        $this->assertSame('山田', $address['family_name'], 'family_name must come from name01');
        $this->assertSame('太郎', $address['given_name'], 'given_name must come from name02');
        $this->assertSame('ヤマダ', $address['family_name_kana'], 'family_name_kana must come from kana01');
        $this->assertSame('タロウ', $address['given_name_kana'], 'given_name_kana must come from kana02');
        $this->assertSame('株式会社テスト', $address['company'], 'company must come from company_name');
        $this->assertSame('1000001', $address['postal_code'], 'postal_code must be mapped');
        $this->assertSame('東京都', $address['region'], 'region must be the prefecture name');
        $this->assertSame('千代田区千代田', $address['address1'], 'address1 must come from addr01');
        $this->assertSame('1-1', $address['address2'], 'address2 must come from addr02');
        $this->assertSame('JP', $address['country'], 'country must be alpha-2 resolved from Country.id (392 -> JP)');
        $this->assertSame('0312345678', $address['phone'], 'phone must come from phone_number');
    }

    public function testToAddressArrayFromShipping(): void
    {
        $country = new Country();
        $country->setId(840);

        $pref = new Pref();
        $pref->setId(1);
        $pref->setName('北海道');

        $shipping = new Shipping();
        $shipping->setName01('佐藤');
        $shipping->setName02('花子');
        $shipping->setPostalCode('0600000');
        $shipping->setPref($pref);
        $shipping->setAddr01('札幌市中央区');
        $shipping->setCountry($country);
        $shipping->setPhoneNumber('0111234567');

        $address = $this->service->toAddressArray($shipping);

        $this->assertSame('佐藤', $address['family_name'], 'Shipping family_name must come from name01');
        $this->assertSame('花子', $address['given_name'], 'Shipping given_name must come from name02');
        $this->assertSame('北海道', $address['region'], 'Shipping region must be the prefecture name');
        $this->assertSame('US', $address['country'], 'Shipping country must be alpha-2 resolved from Country.id (840 -> US)');
        $this->assertSame('0111234567', $address['phone'], 'Shipping phone must come from phone_number');
    }

    public function testToAddressArrayWithNullCountryAndPref(): void
    {
        $customer = new Customer();
        $customer->setName01('田中');
        $customer->setName02('一郎');

        $address = $this->service->toAddressArray($customer);

        $this->assertSame('田中', $address['family_name'], 'family_name must be mapped even when country/pref are null');
        $this->assertNull($address['country'], 'Null Country must yield null country alpha-2');
        $this->assertNull($address['region'], 'Null Pref must yield null region');
    }

    /**
     * @return int[]
     */
    private function loadCountryIdsFromCsv(): array
    {
        $csvPath = __DIR__.'/../../../../../src/Eccube/Resource/doctrine/import_csv/ja/mtb_country.csv';
        $this->assertFileExists($csvPath, 'mtb_country.csv must exist at the expected resource path');

        $ids = [];
        $handle = fopen($csvPath, 'r');
        fgetcsv($handle, null, ',', '"', ''); // skip header row
        while (($row = fgetcsv($handle, null, ',', '"', '')) !== false) {
            if (!isset($row[0]) || $row[0] === '') {
                continue;
            }
            $ids[] = (int) $row[0];
        }
        fclose($handle);

        return $ids;
    }
}
