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

namespace Eccube\Tests\Repository;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Repository\CustomerAddressRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\Master\SexRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * CustomerRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class CustomerRepositoryGetQueryBuilderBySearchDataTest extends EccubeTestCase
{
    /**
     * @var array
     */
    protected $Results;

    /**
     * @var array
     */
    protected $searchData;

    /**
     * @var Customer
     */
    protected $Customer;

    /**
     * @var Customer
     */
    protected $Customer1;

    /**
     * @var Customer
     */
    protected $Customer2;

    /**
     * @var Customer
     */
    protected $Customer3;

    /**
     * @var CustomerRepository
     */
    protected $customerRepo;

    /**
     * @var CustomerAddressRepository
     */
    protected $customerAddressRepo;

    /**
     * @var PrefRepository
     */
    protected $masterPrefRepo;

    /**
     * @var SexRepository
     */
    protected $masterSexRepo;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->customerRepo = $this->entityManager->getRepository(\Eccube\Entity\Customer::class);
        $this->customerAddressRepo = $this->entityManager->getRepository(\Eccube\Entity\CustomerAddress::class);
        $this->masterPrefRepo = $this->entityManager->getRepository(\Eccube\Entity\Master\Pref::class);
        $this->masterSexRepo = $this->entityManager->getRepository(\Eccube\Entity\Master\Sex::class);
        $this->deleteAllRows([
            'dtb_order_item',
            'dtb_shipping',
            'dtb_order',
        ]);

        $this->removeCustomer();
        $this->Customer = $this->createCustomer('customer@example.com');
        $this->Customer1 = $this->createCustomer('customer1@example.com');
        $this->Customer2 = $this->createCustomer('customer2@example.com');
        $this->Customer3 = $this->createCustomer('customer3@example.com');
    }

    public function removeCustomer()
    {
        $CustomerAddresses = $this->customerAddressRepo->findAll();
        foreach ($CustomerAddresses as $CustomerAddress) {
            $this->entityManager->remove($CustomerAddress);
        }
        $this->entityManager->flush();
        $Customers = $this->customerRepo->findAll();
        foreach ($Customers as $Customer) {
            $this->entityManager->remove($Customer);
        }
        $this->entityManager->flush();
    }

    public function scenario()
    {
        $this->Results = $this->customerRepo->getQueryBuilderBySearchData($this->searchData)
            ->getQuery()
            ->getResult();
    }

    public function testMultiWithId()
    {
        // 検索時, IDの重複を防ぐため事前に5個生成しておく
        for ($i = 0; $i < 10; $i++) {
            $this->createCustomer('user-'.$i.'@example.com');
        }
        $Customer = $this->createCustomer('customer@example.jp');
        $this->expected = $Customer->getId();
        $this->searchData = [
            'multi' => $this->expected,
        ];

        $this->scenario();

        $this->assertEquals(1, count($this->Results));
        $this->actual = $this->Results[0]->getId();
        $this->verify();
    }

    public function testMultiWithIdNotFound()
    {
        $this->searchData = [
            'multi' => 99999,
        ];

        $this->scenario();

        $this->expected = 0;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testMultiWithEmail()
    {
        $this->searchData = [
            'multi' => 'customer@example.com',
        ];

        $this->scenario();

        $this->assertEquals(1, count($this->Results));

        $this->expected = 'customer@example.com';
        $this->actual = $this->Results[0]->getEmail();
        $this->verify();
    }

    public function testMultiWithEmail2()
    {
        $this->searchData = [
            'multi' => 'customer',
        ];

        $this->scenario();

        $this->expected = 4;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testMultiWithName()
    {
        $this->Customer->setName01('姓');
        $this->entityManager->flush();

        $this->searchData = [
            'multi' => '姓',
        ];

        $this->scenario();

        $this->assertEquals(1, count($this->Results));

        $this->expected = '姓';
        $this->actual = $this->Results[0]->getName01();
        $this->verify();
    }

    public function testMultiWithNameHasSpaceEn()
    {
        $this->Customer->setName01('姓');
        $this->Customer->setName02('名');
        $this->entityManager->flush();

        $this->searchData = [
            'multi' => '姓 名',
        ];

        $this->scenario();

        $this->assertEquals(1, count($this->Results));

        $this->expected = '姓';
        $this->actual = $this->Results[0]->getName01();
        $this->verify();
        $this->expected = '名';
        $this->actual = $this->Results[0]->getName02();
        $this->verify();
    }

    public function testMultiWithNameHasSpaceJa()
    {
        $this->Customer->setName01('姓');
        $this->Customer->setName02('名');
        $this->entityManager->flush();

        $this->searchData = [
            'multi' => '姓　名',
        ];

        $this->scenario();

        $this->assertEquals(1, count($this->Results));

        $this->expected = '姓';
        $this->actual = $this->Results[0]->getName01();
        $this->verify();
        $this->expected = '名';
        $this->actual = $this->Results[0]->getName02();
        $this->verify();
    }

    public function testMultiWithKana()
    {
        $this->Customer->setKana01('セイ')
            ->setKana02('メイ');
        $this->entityManager->flush();

        $this->searchData = [
            'multi' => 'メイ',
        ];

        $this->scenario();

        $this->assertEquals(1, count($this->Results));

        $this->expected = 'メイ';
        $this->actual = $this->Results[0]->getKana02();
        $this->verify();
    }

    public function testMultiWithKanaHasWhiteSpaceEn()
    {
        $this->Customer->setKana01('セイ')
            ->setKana02('メイ');
        $this->entityManager->flush();

        $this->searchData = [
            'multi' => 'セイ メイ',
        ];

        $this->scenario();

        $this->assertEquals(1, count($this->Results));

        $this->expected = 'セイ';
        $this->actual = $this->Results[0]->getKana01();
        $this->verify();
        $this->expected = 'メイ';
        $this->actual = $this->Results[0]->getKana02();
        $this->verify();
    }

    public function testMultiWithKanaHasWhiteSpaceJa()
    {
        $this->Customer->setKana01('セイ')
            ->setKana02('メイ');
        $this->entityManager->flush();

        $this->searchData = [
            'multi' => 'セイ　メイ',
        ];

        $this->scenario();

        $this->assertEquals(1, count($this->Results));

        $this->expected = 'セイ';
        $this->actual = $this->Results[0]->getKana01();
        $this->verify();
        $this->expected = 'メイ';
        $this->actual = $this->Results[0]->getKana02();
        $this->verify();
    }

    public function testPref()
    {
        $pref_id = 26;
        $Pref = $this->masterPrefRepo->find($pref_id);
        $this->Customer->setPref($Pref);
        $Pref2 = $this->masterPrefRepo->find(1);
        $this->Customer1->setPref($Pref2);
        $this->Customer2->setPref($Pref2);
        $this->Customer3->setPref($Pref2);
        $this->entityManager->flush();

        $this->searchData = [
            'pref' => $Pref,
        ];

        $this->scenario();

        $this->assertEquals(1, count($this->Results));

        $this->expected = $pref_id;
        $this->actual = $this->Results[0]->getPref()->getId();
        $this->verify();
    }

    public function testSex()
    {
        $Male = $this->masterSexRepo->find(1);
        $Female = $this->masterSexRepo->find(2);
        $this->Customer->setSex($Male);
        $this->Customer1->setSex($Female);
        $this->Customer2->setSex(null);
        $this->Customer3->setSex(null);
        $this->entityManager->flush();

        $this->searchData = [
            'sex' => [$Male, $Female],
        ];

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBirthMonth()
    {
        $this->Customer->setBirth(new \DateTime('2016-09-29'));
        $this->Customer1->setBirth(new \DateTime('2010-09-01'));
        $this->Customer2->setBirth(new \DateTime('2016-01-01'));
        $this->Customer3->setBirth(null);
        $this->entityManager->flush();

        $this->searchData = [
            'birth_month' => 9,
        ];

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBirthStart()
    {
        $birth = '2006-09-01';
        $this->Customer->setBirth(new \DateTime($birth));
        $this->Customer1->setBirth(null);
        $this->Customer2->setBirth(null);
        $this->Customer3->setBirth(null);
        $this->entityManager->flush();

        $this->searchData = [
            'birth_start' => new \DateTime('2006-09-01'),
        ];

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBirthStartWithOut()
    {
        $birth = '2006-09-01';
        $this->Customer->setBirth(new \DateTime($birth));
        $this->Customer1->setBirth(null);
        $this->Customer2->setBirth(null);
        $this->Customer3->setBirth(null);
        $this->entityManager->flush();

        $this->searchData = [
            'birth_start' => new \DateTime('2006-09-02'),
        ];

        $this->scenario();

        $this->expected = 0;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBirthEnd()
    {
        $birth = '2006-09-01';
        $this->Customer->setBirth(new \DateTime($birth));
        $this->Customer1->setBirth(null);
        $this->Customer2->setBirth(null);
        $this->Customer3->setBirth(null);
        $this->entityManager->flush();

        $this->searchData = [
            'birth_end' => new \DateTime('2006-09-01'),
        ];

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBirthEndWithOut()
    {
        $birth = '2006-09-01';
        $this->Customer->setBirth(new \DateTime($birth));
        $this->Customer1->setBirth(null);
        $this->Customer2->setBirth(null);
        $this->Customer3->setBirth(null);
        $this->entityManager->flush();

        $this->searchData = [
            'birth_end' => new \DateTime('2006-08-31'),
        ];

        $this->scenario();

        $this->expected = 0;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testPhoneNumber()
    {
        $this->Customer
            ->setPhoneNumber('0123456789');
        $this->Customer1
            ->setPhoneNumber('0123456789');
        $this->Customer2
            ->setPhoneNumber('0123456789');
        $this->Customer3
            ->setPhoneNumber('9876543210');
        $this->entityManager->flush();

        $this->searchData = [
            'phone_number' => '0123456789',
        ];

        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBuyTotalStart()
    {
        $this->Customer->setBuyTotal(1);
        $this->entityManager->flush();

        $this->searchData = [
            'buy_total_start' => '1',
        ];

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBuyTotalStartWithZero()
    {
        $this->Customer->setBuyTotal(0);
        $this->Customer1->setBuyTotal(1);
        $this->Customer2->setBuyTotal(1);
        $this->Customer3->setBuyTotal(1);
        $this->entityManager->flush();

        $this->searchData = [
            'buy_total_start' => '0',
            'buy_total_end' => '0',
        ];

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBuyTotalEnd()
    {
        $this->Customer->setBuyTotal(1);
        $this->entityManager->flush();

        $this->searchData = [
            'buy_total_end' => '1',
        ];

        $this->scenario();

        $this->expected = 4;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBuyTimesStart()
    {
        $this->Customer->setBuyTimes(1);
        $this->entityManager->flush();

        $this->searchData = [
            'buy_times_start' => '1',
        ];

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBuyTimesStartWithZero()
    {
        $this->Customer->setBuyTimes(0);
        $this->Customer1->setBuyTimes(1);
        $this->Customer2->setBuyTimes(1);
        $this->Customer3->setBuyTimes(1);
        $this->entityManager->flush();

        $this->searchData = [
            'buy_times_start' => '0',
            'buy_times_end' => '0',
        ];

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBuyTimesEnd()
    {
        $this->Customer->setBuyTimes(1);
        $this->entityManager->flush();

        $this->searchData = [
            'buy_times_end' => '1',
        ];

        $this->scenario();

        $this->expected = 4;
        $this->actual = count($this->Results);
        $this->verify();
    }

    /**
     * @dataProvider dataFormDateProvider
     *
     * @param string $formName
     * @param string $time
     * @param int $expected
     */
    public function testDate(string $formName, string $time, int $expected)
    {
        $this->Customer->setLastBuyDate(new \DateTime());
        $this->entityManager->flush();

        $this->searchData = [
            $formName => new \DateTime($time),
        ];

        $this->scenario();

        $this->expected = $expected;
        $this->actual = count($this->Results);
        $this->verify();
    }

    /**
     * Data provider date form test.
     *
     * time:
     * - today: 今日の00:00:00
     * - tomorrow: 明日の00:00:00
     * - yesterday: 昨日の00:00:00
     *
     * @return array
     */
    public function dataFormDateProvider()
    {
        return [
            ['create_date_start', 'today', 4],
            ['create_date_start', 'tomorrow', 0],
            ['update_date_start', 'today', 4],
            ['update_date_start', 'tomorrow', 0],
            ['last_buy_start', 'today', 1],
            ['last_buy_start', 'tomorrow', 0],
            ['create_date_end', 'today', 4],
            ['create_date_end', 'yesterday', 0],
            ['update_date_end', 'today', 4],
            ['update_date_end', 'yesterday', 0],
            ['last_buy_end', 'today', 1],
            ['last_buy_end', 'yesterday', 0],
        ];
    }

    /**
     * @dataProvider dataFormDateTimeProvider
     *
     * @param string $formName
     * @param string $time
     * @param int $expected
     */
    public function testDateTime(string $formName, string $time, int $expected)
    {
        $this->Customer->setLastBuyDate(new \DateTime());
        $this->entityManager->flush();

        $this->searchData = [
            $formName => new \DateTime($time),
        ];

        $this->scenario();

        $this->expected = $expected;
        $this->actual = count($this->Results);
        $this->verify();
    }

    /**
     * Data provider datetime form test.
     *
     * @return array
     */
    public function dataFormDateTimeProvider()
    {
        return [
            ['create_datetime_start', '- 1 hour', 4],
            ['create_datetime_start', '+ 1 hour', 0],
            ['update_datetime_start', '- 1 hour', 4],
            ['update_datetime_start', '+ 1 hour', 0],
            ['last_buy_datetime_start', '- 1 hour', 1],
            ['last_buy_datetime_start', '+ 1 hour', 0],
            ['create_datetime_end', '+ 1 hour', 4],
            ['create_datetime_end', '- 1 hour', 0],
            ['update_datetime_end', '+ 1 hour', 4],
            ['update_datetime_end', '- 1 hour', 0],
            ['last_buy_datetime_end', '+ 1 hour', 1],
            ['last_buy_datetime_end', '- 1 hour', 0],
        ];
    }

    public function testStatus()
    {
        $Active = $this->entityManager->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::ACTIVE);
        $NonActive = $this->entityManager->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::NONACTIVE);
        $this->Customer->setStatus($Active);
        $this->Customer1->setStatus($NonActive);
        $this->entityManager->flush();

        $this->searchData = [
            'customer_status' => [$Active, $NonActive],
        ];

        $this->scenario();
        $this->expected = 4;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testStatusWithNonActive()
    {
        $NonActive = $this->entityManager->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::NONACTIVE);
        $this->Customer->setStatus($NonActive);
        $this->Customer1->setStatus($NonActive);
        $this->entityManager->flush();

        $this->searchData = [
            'customer_status' => [$NonActive],
        ];

        $this->scenario();
        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBuyProductCode()
    {
        $this->searchData = [
            'buy_product_name' => '商品',
        ];

        $this->scenario();
        // TODO OrderRepository のテストで正常パターンを作成する
        $this->expected = 0;
        $this->actual = count($this->Results);
        $this->verify();
    }
}
