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

namespace Eccube\Tests\Service\AgentCommerce;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\Country;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Shipping;
use Eccube\Service\AgentCommerce\AddressMappingService;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 (pure logic) tests for AddressMappingService.
 *
 * Verifies ISO 3166-1 numeric (mtb_country.id) -> alpha-2 mapping, that every
 * id shipped in mtb_country.csv resolves to a non-null alpha-2, name/region
 * splitting, and DTO array shaping. Entities are constructed in-memory (no DB).
 */
class AddressMappingServiceTest extends TestCase
{
    private AddressMappingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AddressMappingService();
    }

    public function testGetAlpha2FromCountryIdKnownIds(): void
    {
        self::assertSame('JP', $this->service->getAlpha2FromCountryId(392), 'ISO 3166-1 numeric 392 is Japan -> JP');
        self::assertSame('US', $this->service->getAlpha2FromCountryId(840), 'ISO 3166-1 numeric 840 is United States -> US');
        self::assertSame('GB', $this->service->getAlpha2FromCountryId(826), 'ISO 3166-1 numeric 826 is United Kingdom -> GB');
        self::assertSame('CN', $this->service->getAlpha2FromCountryId(156), 'ISO 3166-1 numeric 156 is China -> CN');
        self::assertSame('KR', $this->service->getAlpha2FromCountryId(410), 'ISO 3166-1 numeric 410 is Republic of Korea -> KR');
        self::assertSame('RU', $this->service->getAlpha2FromCountryId(643), 'ISO 3166-1 numeric 643 is Russia -> RU');
    }

    public function testGetAlpha2FromCountryIdNullAndUnknown(): void
    {
        self::assertNull($this->service->getAlpha2FromCountryId(null), 'Null country id must yield null alpha-2');
        self::assertNull($this->service->getAlpha2FromCountryId(999999), 'Unknown numeric id must yield null alpha-2 (no exception)');
    }

    /**
     * Every numeric id present in the shipped mtb_country.csv must map to a
     * non-null alpha-2, otherwise an address built from that country breaks.
     */
    public function testAllCsvCountryIdsResolve(): void
    {
        $ids = $this->loadCountryIdsFromCsv();
        self::assertNotEmpty($ids, 'mtb_country.csv must contain country rows for this assertion to be meaningful');

        foreach ($ids as $id) {
            $alpha2 = $this->service->getAlpha2FromCountryId($id);
            self::assertNotNull($alpha2, sprintf('mtb_country.csv id %d must map to a non-null ISO 3166-1 alpha-2', $id));
            self::assertMatchesRegularExpression('/^[A-Z]{2}$/', $alpha2, sprintf('mtb_country.csv id %d must map to a two-letter uppercase alpha-2 code', $id));
        }
    }

    public function testGetRegionFromPref(): void
    {
        $pref = new Pref();
        $pref->setId(13);
        $pref->setName('東京都');

        self::assertSame('東京都', $this->service->getRegionFromPref($pref), 'Region must be the prefecture name');
        self::assertNull($this->service->getRegionFromPref(null), 'Null prefecture must yield null region');
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

        self::assertSame('山田', $address['family_name'], 'family_name must come from name01');
        self::assertSame('太郎', $address['given_name'], 'given_name must come from name02');
        self::assertSame('ヤマダ', $address['family_name_kana'], 'family_name_kana must come from kana01');
        self::assertSame('タロウ', $address['given_name_kana'], 'given_name_kana must come from kana02');
        self::assertSame('株式会社テスト', $address['company'], 'company must come from company_name');
        self::assertSame('1000001', $address['postal_code'], 'postal_code must be mapped');
        self::assertSame('東京都', $address['region'], 'region must be the prefecture name');
        self::assertSame('千代田区千代田', $address['address1'], 'address1 must come from addr01');
        self::assertSame('1-1', $address['address2'], 'address2 must come from addr02');
        self::assertSame('JP', $address['country'], 'country must be alpha-2 derived from Country.id (392 -> JP)');
        self::assertSame('0312345678', $address['phone'], 'phone must come from phone_number');
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

        self::assertSame('佐藤', $address['family_name'], 'Shipping family_name must come from name01');
        self::assertSame('花子', $address['given_name'], 'Shipping given_name must come from name02');
        self::assertSame('北海道', $address['region'], 'Shipping region must be the prefecture name');
        self::assertSame('US', $address['country'], 'Shipping country must be alpha-2 derived from Country.id (840 -> US)');
        self::assertSame('0111234567', $address['phone'], 'Shipping phone must come from phone_number');
    }

    public function testToAddressArrayWithNullCountryAndPref(): void
    {
        $customer = new Customer();
        $customer->setName01('田中');
        $customer->setName02('一郎');

        $address = $this->service->toAddressArray($customer);

        self::assertSame('田中', $address['family_name'], 'family_name must be mapped even when country/pref are null');
        self::assertNull($address['country'], 'Null Country must yield null country alpha-2');
        self::assertNull($address['region'], 'Null Pref must yield null region');
    }

    /**
     * @return int[]
     */
    private function loadCountryIdsFromCsv(): array
    {
        $csvPath = __DIR__.'/../../../../../src/Eccube/Resource/doctrine/import_csv/ja/mtb_country.csv';
        self::assertFileExists($csvPath, 'mtb_country.csv must exist at the expected resource path');

        $ids = [];
        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle, null, ',', '"', ''); // skip header row
        self::assertIsArray($header, 'mtb_country.csv must have a header row');
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
