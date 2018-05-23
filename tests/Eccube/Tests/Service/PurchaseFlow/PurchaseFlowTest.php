<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Service\PurchaseFlow;

use Eccube\Entity\Cart;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Service\PurchaseFlow\ItemHolderProcessor;
use Eccube\Service\PurchaseFlow\ItemProcessor;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\PurchaseFlow\PurchaseFlowResult;
use Eccube\Service\PurchaseFlow\ValidatableItemHolderProcessor;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;
use Eccube\Tests\EccubeTestCase;

class PurchaseFlowTest extends EccubeTestCase
{
    /**
     * @var PurchaseFlow
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

        $expected = new PurchaseFlowResult($itemHolder);
        $this->assertEquals($expected, $this->flow->calculate($itemHolder, new PurchaseContext()));
    }

    public function testAddProcesser()
    {
        // TODO: FIXME
        $this->markTestIncomplete(__METHOD__.'may be not implement');
        $processor = new PurchaseFlowTest_ItemHolderProcessor();
        $this->flow->addItemHolderProcessor($processor);

        $processor = new PurchaseFlowTest_ItemProcessor();
        $this->flow->addItemProcessor($processor);
    }

    public function testProcessItemProcessors()
    {
        $this->flow->addItemProcessor(new PurchaseFlowTest_ItemProcessor());
        $itemHolder = new Cart();

        $expected = new PurchaseFlowResult($itemHolder);
        self::assertEquals($expected, $this->flow->calculate($itemHolder, new PurchaseContext()));
    }

    public function testProcessItemHolderProcessor()
    {
        $this->flow->addItemHolderProcessor(new PurchaseFlowTest_ItemHolderProcessor());
        $itemHolder = new Cart();

        $expected = new PurchaseFlowResult($itemHolder);
        $expected->addProcessResult(ProcessResult::success());
        self::assertEquals($expected, $this->flow->calculate($itemHolder, new PurchaseContext()));
    }

    public function testProcessItemHolderProcessor_validationErrors()
    {
        $this->flow->addItemHolderProcessor(new PurchaseFlowTest_FailItemHolderProcessor('error 1'));
        $itemHolder = new Cart();

        $expected = new PurchaseFlowResult($itemHolder);
        $expected->addProcessResult(ProcessResult::error('error 1'));
        self::assertEquals($expected, $this->flow->calculate($itemHolder, new PurchaseContext()));
    }

    public function testProcessItemProcessors_validationErrors()
    {
        $this->flow->addItemProcessor(new PurchaseFlowTest_FailProcessor('error 1'));
        $this->flow->addItemProcessor(new PurchaseFlowTest_FailProcessor('error 2'));
        $itemHolder = new Order();
        $itemHolder->addOrderItem(new OrderItem());

        $expected = new PurchaseFlowResult($itemHolder);
        $expected->addProcessResult(ProcessResult::warn('error 1'));
        $expected->addProcessResult(ProcessResult::warn('error 2'));
        self::assertEquals($expected, $this->flow->calculate($itemHolder, new PurchaseContext()));
    }

    public function testProcessItemProcessors_validationErrors_with_multi_items()
    {
        $this->flow->addItemProcessor(new PurchaseFlowTest_FailProcessor('error 1'));
        $this->flow->addItemProcessor(new PurchaseFlowTest_FailProcessor('error 2'));
        $itemHolder = new Order();
        $itemHolder->addOrderItem(new OrderItem());
        $itemHolder->addOrderItem(new OrderItem());

        $expected = new PurchaseFlowResult($itemHolder);
        $expected->addProcessResult(ProcessResult::warn('error 1'));
        $expected->addProcessResult(ProcessResult::warn('error 2'));
        $expected->addProcessResult(ProcessResult::warn('error 1'));
        $expected->addProcessResult(ProcessResult::warn('error 2'));
        self::assertEquals($expected, $this->flow->calculate($itemHolder, new PurchaseContext()));
    }
}

class PurchaseFlowTest_ItemHolderProcessor implements ItemHolderProcessor
{
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        return ProcessResult::success();
    }
}

class PurchaseFlowTest_ItemProcessor implements ItemProcessor
{
    public function process(ItemInterface $item, PurchaseContext $context)
    {
        return ProcessResult::success();
    }
}

class PurchaseFlowTest_FailProcessor extends ValidatableItemProcessor
{
    private $errorMessage;

    /**
     * PurchaseFlowTest_FailProcessor constructor.
     *
     * @param $errorMessage
     */
    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        throw new InvalidItemException($this->errorMessage);
    }
}

class PurchaseFlowTest_FailItemHolderProcessor extends ValidatableItemHolderProcessor
{
    private $errorMessage;

    /**
     * PurchaseFlowTest_FailProcessor constructor.
     *
     * @param $errorMessage
     */
    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    protected function validate(ItemHolderInterface $item, PurchaseContext $context)
    {
        // TODO ItemHolerValidateException が必要か検討
        throw new InvalidItemException($this->errorMessage);
    }
}
