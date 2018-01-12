<?php

namespace Eccube\Tests\Entity\Event;

use Eccube\Annotation\PreUpdate;
use Eccube\Annotation\TargetEntity;
use Eccube\Entity\Event\EntityEventDispatcher;
use Eccube\Entity\Event\EntityEventListener;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Tests\EccubeTestCase;

class UpdatePointEventListenerTest extends EccubeTestCase
{

    protected $Customer;
    protected $Order;

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();

        $this->markTestSkipped("プラグインアップデートできなくなるので一旦スキップ");

        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $OrderNew = $this->app['orm.em']->find(OrderStatus::class, OrderStatus::NEW);
        $this->Order->setOrderStatus($OrderNew);
        $this->app['orm.em']->flush($this->Order);
    }

    public function testAddPoint()
    {
        $prevPoint = $this->Customer->getPoint();
        $this->Order->setAddPoint(100);
        $OrderDeliv = $this->app['orm.em']->find(OrderStatus::class, OrderStatus::DELIVERED);

        $this->Order->setOrderStatus($OrderDeliv);
        $this->app['orm.em']->flush($this->Order);

        $this->actual = $this->Customer->getPoint();
        $this->expected = $prevPoint + 100;
        $this->verify();
    }

    public function testReturnOfAddPoint()
    {
        $OrderDeliv = $this->app['orm.em']->find(OrderStatus::class, OrderStatus::DELIVERED);
        $this->Order->setAddPoint(100);
        $this->Order->setOrderStatus($OrderDeliv);
        $this->app['orm.em']->flush($this->Order);

        $prevPoint = $this->Customer->getPoint();
        // Return of add point
        $OrderNew = $this->app['orm.em']->find(OrderStatus::class, OrderStatus::NEW);
        $this->Order->setOrderStatus($OrderNew);
        $this->app['orm.em']->flush($this->Order);

        $this->actual = $this->Customer->getPoint();
        $this->expected = $prevPoint - 100;
        $this->verify();
    }

    public function testUsePoint()
    {
        $prevPoint = $this->Customer->getPoint();
        $this->Order->setUsePoint(100);
        $OrderCancel = $this->app['orm.em']->find(OrderStatus::class, OrderStatus::CANCEL);
        $this->Order->setOrderStatus($OrderCancel);
        $this->app['orm.em']->flush($this->Order);

        $OrderNew = $this->app['orm.em']->find(OrderStatus::class, OrderStatus::NEW);
        $this->Order->setOrderStatus($OrderNew);
        $this->app['orm.em']->flush($this->Order);

        $this->actual = $this->Customer->getPoint();
        $this->expected = $prevPoint - 100;
        $this->verify();
    }

    public function testReturnOfUsePoint()
    {
        $prevPoint = $this->Customer->getPoint();
        $this->Order->setUsePoint(100);
        $this->app['orm.em']->flush($this->Order);

        $OrderCancel = $this->app['orm.em']->find(OrderStatus::class, OrderStatus::CANCEL);
        $this->Order->setOrderStatus($OrderCancel);
        $this->app['orm.em']->flush($this->Order);

        $this->actual = $this->Customer->getPoint();
        $this->expected = $prevPoint + 100;
        $this->verify();
    }
}
