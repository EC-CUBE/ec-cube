<?php

namespace Eccube\Tests\Entity\Event;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Eccube\Entity\Customer;
use Eccube\Annotation\PrePersist;
use Eccube\Annotation\PreUpdate;
use Eccube\Annotation\TargetEntity;
use Eccube\Entity\Event\EntityEventDispatcher;
use Eccube\Entity\Event\EntityEventListener;
use Eccube\Entity\Product;
use Eccube\Tests\EccubeTestCase;

class EntityEventDispatcherTest extends EccubeTestCase
{

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    /**
     * @test
     */
    public function addEventListener()
    {
        $dispatcher = new EntityEventDispatcher($this->app['orm.em']);
        $listener = new EntityEventDispatcherTest_SimpleEventListener();
        $dispatcher->addEventListener($listener);
        $actualListeners = $dispatcher->getEventListeners(Events::preUpdate);
        self::assertTrue(in_array($listener, $actualListeners[Product::class]));
    }

    /**
     * @test
     */
    public function addEventListener_with_multi_entity_listener()
    {
        $dispatcher = new EntityEventDispatcher($this->app['orm.em']);
        $listener = new EntityEventDispatcherTest_MultiEntityEventListener();
        $dispatcher->addEventListener($listener);
        $actualListeners = $dispatcher->getEventListeners(Events::preUpdate);
        self::assertTrue(in_array($listener, $actualListeners[Product::class]));
        self::assertTrue(in_array($listener, $actualListeners[Customer::class]));
    }

    /**
     * @test
     */
    public function eventListenerShouldBeCalled()
    {
        $listener = new EntityEventDispatcherTest_ProductPersistListener();
        $this->app['eccube.entity.event.dispatcher']->addEventListener($listener);
        $product = $this->createProduct();
        $this->app['orm.em']->flush($product);
        self::assertTrue($listener->executed);
    }

    /**
     * @test
     */
    public function eventListenerShouldNotBeCalled()
    {
        $listener = new EntityEventDispatcherTest_ProductPersistListener();
        $this->app['eccube.entity.event.dispatcher']->addEventListener($listener);
        $customer = $this->createCustomer();
        $this->app['orm.em']->flush($customer);
        self::assertFalse($listener->executed);
    }
}

/**
 * @PreUpdate("Eccube\Entity\Product")
 */
class EntityEventDispatcherTest_SimpleEventListener implements EntityEventListener
{
    public function execute(LifecycleEventArgs $eventArgs) {}
}

/**
 * @PreUpdate({"Eccube\Entity\Product", "Eccube\Entity\Customer"})
 */
class EntityEventDispatcherTest_MultiEntityEventListener implements EntityEventListener
{
    public function execute(LifecycleEventArgs $eventArgs) {}
}

/**
 * @PrePersist("Eccube\Entity\Product")
 */
class EntityEventDispatcherTest_ProductPersistListener implements EntityEventListener
{

    public $executed = false;

    public function execute(LifecycleEventArgs $eventArgs)
    {
        $this->executed = true;
    }
}