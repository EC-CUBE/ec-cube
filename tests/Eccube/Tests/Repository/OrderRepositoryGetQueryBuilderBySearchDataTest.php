<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Entity\OrderDetail;
use Eccube\Entity\Shipping;
use Eccube\Entity\ShipmentItem;

/**
 * OrderRepository::getQueryBuilderBySearchDataTest test cases.
 *
 * @author Kentaro Ohkouchi
 */
class OrderRepositoryGetQueryBuilderBySearchDataTest extends EccubeTestCase
{
    protected $Customer;
    protected $Order;
    protected $Results;
    protected $searchData;

    public function setUp() {
        parent::setUp();
        $this->createProduct();
        $this->Customer = $this->createCustomer();
        $this->Customer->setName01('立方体長');
        $this->Customer2 = $this->createCustomer();
        $this->Customer2->setName01('立方隊員');
        $this->app['orm.em']->persist($this->Customer);
        $this->app['orm.em']->persist($this->Customer2);
        $this->app['orm.em']->flush();

        $this->Order = $this->createOrder($this->Customer);
        $this->Order1 = $this->createOrder($this->Customer);
        $this->Order2 = $this->createOrder($this->Customer2);
    }

    public function scenario()
    {
        $this->Results = $this->app['eccube.repository.order']->getQueryBuilderBySearchData($this->searchData)
            ->getQuery()
            ->getResult();
    }

    public function testOrderIdStart()
    {
        $this->searchData = array(
            'order_id_start' => $this->Order->getId()
        );
        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();

    }

    public function testOrderIdEnd()
    {
        $this->searchData = array(
            'order_id_end' => $this->Order->getId()
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testStatus()
    {
        $NewStatus = $this->app['eccube.repository.order_status']->find($this->app['config']['order_new']);
        $this->Order1->setOrderStatus($NewStatus);
        $this->Order2->setOrderStatus($NewStatus);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'status' => $this->app['config']['order_new']
        );
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testName()
    {
        $this->searchData = array(
            'name' => $this->Customer->getName01()
        );
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testKana()
    {
        $this->Order1
            ->setKana01('セイ')
            ->setKana02('メイ'); // XXX いずれかが NULL だと無視されてしまう
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'kana' => 'メ'
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testEmail()
    {
        $this->searchData = array(
            'email' => $this->Customer->getEmail()
        );
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testTel01()
    {
        $this->Order1->setTel01('999');
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'tel01' => '999'
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testTel02()
    {
        $this->Order1->setTel02('999');
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'tel02' => '999'
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testTel03()
    {
        $this->Order1->setTel03('999');
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'tel03' => '999'
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBirthStart()
    {
        $this->Customer->setBirth(new \DateTime('2006-09-01'));
        $this->Customer2->setBirth(null);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'birth_start' => new \DateTime('2006-09-01')
        );
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBirthEnd()
    {
        $this->Customer->setBirth(new \DateTime('2006-09-01'));
        $this->Customer2->setBirth(null);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'birth_end' => new \DateTime('2006-09-01')
        );
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testSex()
    {
        $Male = $this->app['eccube.repository.master.sex']->find(1);
        $Female = $this->app['eccube.repository.master.sex']->find(2);
        $this->Customer->setSex($Male);
        $this->Customer2->setSex(null);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'sex' => array($Male, $Female)
        );
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testOrderDateStart()
    {
        // FIXME create_date を検索している
        $this->searchData = array(
            'order_date_start' => new \DateTime('- 1 days')
        );

        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testOrderDateEnd()
    {
        // FIXME create_date を検索している
        $this->searchData = array(
            'order_date_end' => new \DateTime('+ 1 days')
        );

        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testUpdateDateStart()
    {
        $this->searchData = array(
            'update_date_start' => new \DateTime('- 1 days')
        );

        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testUpdateDateEnd()
    {
        $this->searchData = array(
            'update_date_end' => new \DateTime('+ 1 days')
        );

        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testPaymentTotalStart()
    {
        $this->Order->setPaymentTotal(99);
        $this->Order1->setPaymentTotal(100);
        $this->Order2->setPaymentTotal(101);
        $this->app['orm.em']->flush();

        // XXX 0 が無視されてしまう
        $this->searchData = array(
            'payment_total_start' => 100
        );

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testPaymentTotalEnd()
    {
        $this->Order->setPaymentTotal(99);
        $this->Order1->setPaymentTotal(100);
        $this->Order2->setPaymentTotal(101);
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'payment_total_end' => 100
        );

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBuyProductName()
    {
        foreach ($this->Order1->getOrderDetails() as $OrderDetail) {
            $OrderDetail->setProductName('アイス');
        }
        foreach ($this->Order2->getOrderDetails() as $OrderDetail) {
            $OrderDetail->setProductName('アイス');
        }
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'buy_product_name' => 'アイス'
        );

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }
}
