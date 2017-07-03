<?php

namespace Eccube\Tests\Service;

use Eccube\Entity\Cart;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\ItemHolderProcessor;
use Eccube\Service\ItemProcessor;
use Eccube\Service\PurchaseFlow;
use Eccube\Tests\EccubeTestCase;

class PurchaseFlowTest extends EccubeTestCase
{
    protected $flow;

    public function setUp()
    {
        parent::setUp();

        $this->flow = new PurchaseFlow();
    }

    public function testExecute()
    {

        $this->assertInstanceOf(PurchaseFlow::class, $this->flow);

        $itemHolder = new Cart();
        $this->assertEquals($itemHolder, $this->flow->execute($itemHolder));
    }

    public function testAddProcesser()
    {
        $processor = new PurchaseFlowTest_HogeProcessor();
        $this->flow->addItemHolderProcessor($processor);

        $processor = new PurchaseFlowTest_FugaProcessor();
        $this->flow->addItemProcessor($processor);
    }
}

class PurchaseFlowTest_HogeProcessor implements ItemHolderProcessor
{
    public function process(ItemHolderInterface $itemHolder)
    {
        // TODO: Implement process() method.
    }
}

class PurchaseFlowTest_FugaProcessor implements ItemProcessor
{

}