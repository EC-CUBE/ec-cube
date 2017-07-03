<?php

namespace Eccube\Tests\Service;

use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ItemInterface;
use Eccube\Service\ItemHolderProcessor;
use Eccube\Service\ItemProcessor;
use Eccube\Service\ItemValidateException;
use Eccube\Service\PurchaseFlow;
use Eccube\Service\ValidatableItemProcessor;
use Eccube\Tests\EccubeTestCase;

class PurchaseFlowTest extends EccubeTestCase
{
    /**
     * @var PurchaseFlow $flow
     */
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

    public function testProcessItemProcessors()
    {
        $this->flow->addItemProcessor(new PurchaseFlowTest_FugaProcessor());
        $itemHolder = new Cart();

        self::assertEquals($itemHolder, $this->flow->execute($itemHolder));
    }

    public function testProcessItemProcessors_validationErrors()
    {
        $this->flow->addItemProcessor(new PurchaseFlowTest_FailProcessor("error 1"));
        $this->flow->addItemProcessor(new PurchaseFlowTest_FailProcessor("error 2"));
        $itemHolder = new Cart();
        $itemHolder->addCartItem(new CartItem());

        self::assertEquals($itemHolder, $this->flow->execute($itemHolder));

        self::assertEquals([
            "error 1", "error 2"
        ], array_map(function($exception) { return $exception->getMessage(); }, $itemHolder->getErrors()));
    }

    public function testProcessItemProcessors_validationErrors_with_multi_items()
    {
        $this->flow->addItemProcessor(new PurchaseFlowTest_FailProcessor("error 1"));
        $this->flow->addItemProcessor(new PurchaseFlowTest_FailProcessor("error 2"));
        $itemHolder = new Cart();
        $itemHolder->addCartItem(new CartItem());
        $itemHolder->addCartItem(new CartItem());

        self::assertEquals($itemHolder, $this->flow->execute($itemHolder));

        self::assertEquals([
            "error 1", "error 2", "error 1", "error 2"
        ], array_map(function($exception) { return $exception->getMessage(); }, $itemHolder->getErrors()));
    }

}

class PurchaseFlowTest_HogeProcessor implements ItemHolderProcessor
{
    public function process(ItemHolderInterface $itemHolder)
    {
    }
}


class PurchaseFlowTest_FugaProcessor implements ItemProcessor
{

    public function process(ItemInterface $item)
    {
    }
}

class PurchaseFlowTest_FailProcessor extends ValidatableItemProcessor
{
    private $errorMessage;

    /**
     * PurchaseFlowTest_FailProcessor constructor.
     * @param $errorMessage
     */
    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    protected function validate(ItemInterface $item)
    {
        throw new ItemValidateException($this->errorMessage);
    }
}
