<?php

namespace Eccube\Tests\Repository;

use Eccube\Application;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Tests\EccubeTestCase;

/**
 * CustomerRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class CustomerRepositoryGetQueryBuilderBySearchDataTest extends EccubeTestCase
{
    protected $Results;
    protected $searchData;

    public function setUp()
    {
        parent::setUp();
        $this->removeCustomer();
        $this->Customer = $this->createCustomer('customer@example.com');
        $this->Customer1 = $this->createCustomer('customer1@example.com');
        $this->Customer2 = $this->createCustomer('customer2@example.com');
        $this->Customer3 = $this->createCustomer('customer3@example.com');
    }

    public function removeCustomer()
    {
        $CustomerAddresses = $this->app['eccube.repository.customer_address']->findAll();
        foreach ($CustomerAddresses as $CustomerAddress) {
            $this->app['orm.em']->remove($CustomerAddress);
        }
        $this->app['orm.em']->flush();
        $Customers = $this->app['eccube.repository.customer']->findAll();
        foreach ($Customers as $Customer) {
            $this->app['orm.em']->remove($Customer);
        }
        $this->app['orm.em']->flush();
    }

    public function scenario()
    {
        $this->Results = $this->app['eccube.repository.customer']->getQueryBuilderBySearchData($this->searchData)
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
        $this->searchData = array(
            'multi' => $this->expected
        );

        $this->scenario();

        $this->assertEquals(1, count($this->Results));
        $this->actual = $this->Results[0]->getId();
        $this->verify();
    }

    public function testMultiWithIdNotFound()
    {
        $this->searchData = array(
            'multi' => 99999
        );

        $this->scenario();

        $this->expected = 0;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testMultiWithEmail()
    {
        $this->searchData = array(
            'multi' => 'customer@example.com'
        );

        $this->scenario();

        $this->assertEquals(1, count($this->Results));

        $this->expected = 'customer@example.com';
        $this->actual = $this->Results[0]->getEmail();
        $this->verify();
    }

    public function testMultiWithEmail2()
    {
        $this->searchData = array(
            'multi' => 'customer'
        );

        $this->scenario();

        $this->expected = 4;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testMultiWithName()
    {
        $this->Customer->setName01('姓');
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'multi' => '姓'
        );

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
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'multi' => '姓 名'
        );

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
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'multi' => '姓　名'
        );

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
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'multi' => 'メイ'
        );

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
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'multi' => 'セイ メイ'
        );

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
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'multi' => 'セイ　メイ'
        );

        $this->scenario();

        $this->assertEquals(1, count($this->Results));

        $this->expected = 'セイ';
        $this->actual = $this->Results[0]->getKana01();
        $this->verify();
        $this->expected = 'メイ';
        $this->actual = $this->Results[0]->getKana02();
        $this->verify();
    }

    /* https://github.com/EC-CUBE/ec-cube/issues/945
     * kana01, kana02 のいずれかが NULL だと検索にヒットしない
    public function testMultiWithKana01()
    {
        $this->Customer->setKana01('セイ')
            ->setKana02(null);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'multi' => 'セイ'
        );

        $this->scenario();

        $this->assertEquals(1, count($this->Results));

        $this->expected = 'セイ';
        $this->actual = $this->Results[0]->getKana02();
        $this->verify();
    }
    */

    public function testPref()
    {
        $pref_id = 26;
        $Pref = $this->app['eccube.repository.master.pref']->find($pref_id);
        $this->Customer->setPref($Pref);
        $Pref2 = $this->app['eccube.repository.master.pref']->find(1);
        $this->Customer1->setPref($Pref2);
        $this->Customer2->setPref($Pref2);
        $this->Customer3->setPref($Pref2);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'pref' => $Pref
        );

        $this->scenario();

        $this->assertEquals(1, count($this->Results));

        $this->expected = $pref_id;
        $this->actual = $this->Results[0]->getPref()->getId();
        $this->verify();
    }

    public function testSex()
    {
        $Male = $this->app['eccube.repository.master.sex']->find(1);
        $Female = $this->app['eccube.repository.master.sex']->find(2);
        $this->Customer->setSex($Male);
        $this->Customer1->setSex($Female);
        $this->Customer2->setSex(null);
        $this->Customer3->setSex(null);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'sex' => array($Male, $Female)
        );

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
    }

    public function testBirthMonth()
    {
        $this->Customer->setBirth(new \DateTime('2016-09-29'));
        $this->Customer1->setBirth(new \DateTime('2010-09-01'));
        $this->Customer2->setBirth(new \DateTime('2016-01-01'));
        $this->Customer3->setBirth(null);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'birth_month' => 9
        );

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
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'birth_start' => new \DateTime('2006-09-01')
        );

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
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'birth_start' => new \DateTime('2006-09-02')
        );

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
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'birth_end' => new \DateTime('2006-09-01')
        );

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
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'birth_end' => new \DateTime('2006-08-31')
        );

        $this->scenario();

        $this->expected = 0;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testTel()
    {
        $this->Customer
            ->setTel01('090')
            ->setTel02('999')
            ->setTel03('000');
        $this->Customer1
            ->setTel01('090')
            ->setTel02('111')
            ->setTel03('000');
        $this->Customer2
            ->setTel01('090')
            ->setTel02('222')
            ->setTel03('000');
        $this->Customer3
            ->setTel01('090')
            ->setTel02('333')
            ->setTel03('000');
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'tel' => '999'
        );

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBuyTotalStart()
    {
        $this->Customer->setBuyTotal(1);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'buy_total_start' => '1'
        );

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    /* https://github.com/EC-CUBE/ec-cube/issues/945
     * 0 が無視されてしまう
    public function testBuyTotalStartWithZero()
    {
        $this->Customer->setBuyTotal(0);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'buy_total_start' => '0'
        );

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }
    */

    public function testBuyTotalEnd()
    {
        $this->Customer->setBuyTotal(1);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'buy_total_end' => '1',
        );

        $this->scenario();

        $this->expected = 4;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBuyTimesStart()
    {
        $this->Customer->setBuyTimes(1);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'buy_times_start' => '1'
        );

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    /* https://github.com/EC-CUBE/ec-cube/issues/945
     * 0 が無視されてしまう
    public function testBuyTimesStartWithZero()
    {
        $this->Customer->setBuyTimes(0);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'buy_times_start' => '0'
        );

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }
    */

    public function testBuyTimesEnd()
    {
        $this->Customer->setBuyTimes(1);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'buy_times_end' => '1',
        );

        $this->scenario();

        $this->expected = 4;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCreateDateStart()
    {
        $this->searchData = array(
            'create_date_start' => new \DateTime('- 1 days')
        );

        $this->scenario();

        $this->expected = 4;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCreateDateEnd()
    {
        $this->searchData = array(
            'create_date_end' => new \DateTime('+ 1 days')
        );

        $this->scenario();
        $this->expected = 4;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testUpdateDateStart()
    {
        $this->searchData = array(
            'update_date_start' => new \DateTime('- 1 days')
        );

        $this->scenario();

        $this->expected = 4;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testUpdateDateEnd()
    {
        $this->searchData = array(
            'update_date_end' => new \DateTime('+ 1 days')
        );

        $this->scenario();
        $this->expected = 4;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testLastBuyStart()
    {
        $this->Customer->setLastBuyDate(new \DateTime());
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'last_buy_start' => new \DateTime('- 1 days')
        );

        $this->scenario();
        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testLastBuyEnd()
    {
        $this->Customer->setLastBuyDate(new \DateTime());
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'last_buy_end' => new \DateTime('+ 1 days')
        );

        $this->scenario();
        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testStatus()
    {
        $Active = $this->app['orm.em']->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::ACTIVE);
        $NonActive = $this->app['orm.em']->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::NONACTIVE);
        $this->Customer->setStatus($Active);
        $this->Customer1->setStatus($NonActive);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'customer_status' => array($Active, $NonActive)
        );

        $this->scenario();
        $this->expected = 4;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testStatusWithNonActive()
    {
        $NonActive = $this->app['orm.em']->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::NONACTIVE);
        $this->Customer->setStatus($NonActive);
        $this->Customer1->setStatus($NonActive);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'customer_status' => array($NonActive)
        );

        $this->scenario();
        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBuyProductCode()
    {
        $this->searchData = array(
            'buy_product_code' => '商品'
        );

        $this->scenario();
        // TODO OrderRepository のテストで正常パターンを作成する
        $this->expected = 0;
        $this->actual = count($this->Results);
        $this->verify();
    }
}
