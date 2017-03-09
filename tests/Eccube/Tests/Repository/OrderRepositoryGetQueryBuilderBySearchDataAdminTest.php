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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * OrderRepository::getQueryBuilderBySearchDataForAdminTest test cases.
 *
 * @author Kentaro Ohkouchi
 */
class OrderRepositoryGetQueryBuilderBySearchDataAdminTest extends EccubeTestCase
{
    protected $Customer;
    protected $Order;
    protected $Results;
    protected $searchData;

    public function setUp() {
        parent::setUp();
        $this->createProduct();
        $this->Customer = $this->createCustomer();
        $this->app['orm.em']->persist($this->Customer);
        $this->app['orm.em']->flush();

        $this->Order = $this->createOrder($this->Customer);
        $this->Order1 = $this->createOrder($this->Customer);
        $this->Order2 = $this->createOrder($this->createCustomer('test@example.com'));
        // 新規受付にしておく
        $NewStatus = $this->app['eccube.repository.order_status']->find($this->app['config']['order_new']);
        $this->Order1
            ->setOrderStatus($NewStatus)
            ->setOrderDate(new \DateTime());
        $this->Order2
            ->setOrderStatus($NewStatus)
            ->setOrderDate(new \DateTime());
        $this->app['orm.em']->flush();
    }

    public function scenario()
    {
        $this->Results = $this->app['eccube.repository.order']->getQueryBuilderBySearchDataForAdmin($this->searchData)
            ->getQuery()
            ->getResult();
    }

    public function testOrderIdStart()
    {
        $this->searchData = array(
            'order_id_start' => $this->Order->getId()
        );
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();

    }

    public function testMultiWithName()
    {
        $this->Order2
            ->setName01('立方')
            ->setName02('隊長');
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'multi' => '立方'
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testMultiWithKana()
    {
        $this->Order2
            ->setKana01('リッポウ')
            ->setKana02('タイチョウ');
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'multi' => 'タイチョウ'
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testMultiWithID()
    {
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'multi' => $this->Order2->getId()
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testOrderIdEnd()
    {
        $this->searchData = array(
            'order_id_end' => $this->Order->getId()
        );
        $this->scenario();

        // $this->Order は購入処理中なので 0 件になる
        $this->expected = 0;
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

    public function testMultiStatus()
    {
        $this->Order1->setOrderStatus($this->app['eccube.repository.order_status']->find($this->app['config']['order_new']));
        $this->Order2->setOrderStatus($this->app['eccube.repository.order_status']->find($this->app['config']['order_cancel']));
        $this->app['orm.em']->flush();

        $Statuses = new ArrayCollection(array(
            $this->app['config']['order_new'],
            $this->app['config']['order_cancel'],
            $this->app['config']['order_pending'],
        ));
        $this->searchData = array(
            'multi_status' => $Statuses,
        );
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testName()
    {
        $this->Order2
            ->setName01('立方')
            ->setName02('隊長');
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'name' => '立方隊長'
        );
        $this->scenario();

        $this->expected = 1;
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
            'email' => 'test@example.com'
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testTel()
    {
        $Orders = $this->app['eccube.repository.order']->findAll();
        // 全受注の Tel を変更しておく
        foreach ($Orders as $Order) {
            $Order
                ->setTel01('111')
                ->setTel02('2222')
                ->setTel03('8888');
        }
        $this->app['orm.em']->flush();

        // 1受注のみ検索対象とする
        $this->Order1
            ->setTel01('999')
            ->setTel02('9999')
            ->setTel03('8888');
        $this->app['orm.em']->flush();

        $this->searchData = array(
            'tel' => '999'
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBirthStart()
    {
        $this->Customer->setBirth(new \DateTime('2006-09-01'));
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
        $this->Order1->setSex($Male);
        $this->Order2->setSex(null);
        $this->app['orm.em']->flush();

        $Sexs = new ArrayCollection(array($Male, $Female));
        $this->searchData = array(
            'sex' => $Sexs
        );
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testOrderDateStart()
    {
        $this->searchData = array(
            'order_date_start' => new \DateTime('- 1 days')
        );

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testOrderDateEnd()
    {
        $this->searchData = array(
            'order_date_end' => new \DateTime('+ 1 days')
        );

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testUpdateDateStart()
    {
        $this->searchData = array(
            'update_date_start' => new \DateTime('- 1 days')
        );

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testUpdateDateEnd()
    {
        $this->searchData = array(
            'update_date_end' => new \DateTime('+ 1 days')
        );

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testPaymentDateStart()
    {
        $Status = $this->app['eccube.repository.order_status']->find(6);
        $this->app['eccube.repository.order']->changeStatus($this->Order2->getId(), $Status);

        $this->searchData = array(
            'payment_date_start' => new \DateTime('- 1 days')
        );

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testPaymentDateEnd()
    {
        $Status = $this->app['eccube.repository.order_status']->find(6);
        $this->app['eccube.repository.order']->changeStatus($this->Order2->getId(), $Status);
        $this->searchData = array(
            'payment_date_end' => new \DateTime('+ 1 days')
        );

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCommitDateStart()
    {
        $Status = $this->app['eccube.repository.order_status']->find(5);
        $this->app['eccube.repository.order']->changeStatus($this->Order2->getId(), $Status);

        $this->searchData = array(
            'commit_date_start' => new \DateTime('- 1 days')
        );

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCommitDateEnd()
    {
        $Status = $this->app['eccube.repository.order_status']->find(5);
        $this->app['eccube.repository.order']->changeStatus($this->Order2->getId(), $Status);
        $this->searchData = array(
            'commit_date_end' => new \DateTime('+ 1 days')
        );

        $this->scenario();

        $this->expected = 1;
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

        $this->expected = 1;
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
