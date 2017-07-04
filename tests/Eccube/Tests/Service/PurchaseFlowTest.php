<?php

namespace Eccube\Tests\Service;

use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ItemInterface;
use Eccube\Service\ItemHolderProcessor;
use Eccube\Service\ItemProcessor;
use Eccube\Service\ItemValidateException;
use Eccube\Service\ProcessResult;
use Eccube\Service\PurchaseFlow;
use Eccube\Service\ValidatableItemProcessor;
use Eccube\Service\ValidatableItemHolderProcessor;
use Eccube\Tests\EccubeTestCase;

class PurchaseFlowTest extends EccubeTestCase
{
    /**
     * @var PurchaseFlow $flow
     */
    protected $flow;

    protected $Product;

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
        $processor = new PurchaseFlowTest_ItemHolderProcessor();
        $this->flow->addItemHolderProcessor($processor);

        $processor = new PurchaseFlowTest_ItemProcessor();
        $this->flow->addItemProcessor($processor);
    }

    public function testProcessItemProcessors()
    {
        $this->flow->addItemProcessor(new PurchaseFlowTest_ItemProcessor());
        $itemHolder = new Cart();

        self::assertEquals($itemHolder, $this->flow->execute($itemHolder));
    }

    public function testProcessItemHolderProcessor()
    {
        $this->flow->addItemHolderProcessor(new PurchaseFlowTest_ItemHolderProcessor());
        $itemHolder = new Cart();

        self::assertEquals($itemHolder, $this->flow->execute($itemHolder));
    }

    public function testProcessItemHolderProcessor_validationErrors()
    {
        $this->flow->addItemHolderProcessor(new PurchaseFlowTest_FailItemHolderProcessor('error 1'));
        $itemHolder = new Cart();

        self::assertEquals($itemHolder, $this->flow->execute($itemHolder));
        self::assertEquals(['error 1'], $itemHolder->getErrors());
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
        ], $itemHolder->getErrors());
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
        ], $itemHolder->getErrors());
    }

}

class PurchaseFlowTest_ItemHolderProcessor implements ItemHolderProcessor
{
    public function process(ItemHolderInterface $itemHolder)
    {
        return ProcessResult::success();
    }
}

class PurchaseFlowTest_ItemProcessor implements ItemProcessor
{

    public function process(ItemInterface $item)
    {
        return ProcessResult::success();
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

class PurchaseFlowTest_FailItemHolderProcessor extends ValidatableItemHolderProcessor
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

    protected function validate(ItemHolderInterface $item)
    {
        // TODO ItemHolerValidateException が必要か検討
        throw new ItemValidateException($this->errorMessage);
    }
}

