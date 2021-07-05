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

namespace Eccube\Tests\Service\PurchaseFlow;

use Eccube\Entity\Cart;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\ItemPreprocessor;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\PurchaseFlow\PurchaseFlowResult;
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
        $this->assertEquals($expected, $this->flow->validate($itemHolder, new PurchaseContext()));
    }

    public function testAddProcesser()
    {
        // TODO: FIXME
        $this->markTestIncomplete(__METHOD__.'may be not implement');
        $processor = new PurchaseFlowTest_ItemHolderPreprocessor();
        $this->flow->addItemHolderProcessor($processor);

        $processor = new PurchaseFlowTest_ItemPreprocessor();
        $this->flow->addItemProcessor($processor);
    }

    public function testProcessItemProcessors()
    {
        $this->flow->addItemPreprocessor(new PurchaseFlowTest_ItemPreprocessor());
        $itemHolder = new Cart();

        $expected = new PurchaseFlowResult($itemHolder);
        self::assertEquals($expected, $this->flow->validate($itemHolder, new PurchaseContext()));
    }

    public function testProcessItemHolderProcessor()
    {
        $this->flow->addItemHolderPreprocessor(new PurchaseFlowTest_ItemHolderPreprocessor());
        $itemHolder = new Cart();

        $expected = new PurchaseFlowResult($itemHolder);
        self::assertEquals($expected, $this->flow->validate($itemHolder, new PurchaseContext()));
    }

    public function testProcessItemHolderProcessorValidationErrors()
    {
        $this->flow->addItemHolderValidator(new PurchaseFlowTest_FailItemHolderValidator('error 1'));
        $itemHolder = new Cart();

        $expected = new PurchaseFlowResult($itemHolder);
        $expected->addProcessResult(ProcessResult::error('error 1', PurchaseFlowTest_FailItemHolderValidator::class));
        self::assertEquals($expected, $this->flow->validate($itemHolder, new PurchaseContext()));
    }

    public function testProcessItemProcessorsValidationErrors()
    {
        $this->flow->addItemValidator(new PurchaseFlowTest_FailValidator('error 1'));
        $this->flow->addItemValidator(new PurchaseFlowTest_FailValidator('error 2'));
        $itemHolder = new Order();
        $itemHolder->addOrderItem(new OrderItem());

        $expected = new PurchaseFlowResult($itemHolder);
        $expected->addProcessResult(ProcessResult::warn('error 1', PurchaseFlowTest_FailValidator::class));
        $expected->addProcessResult(ProcessResult::warn('error 2', PurchaseFlowTest_FailValidator::class));
        self::assertEquals($expected, $this->flow->validate($itemHolder, new PurchaseContext()));
    }

    public function testProcessItemProcessorsValidationErrorsWithMultiItems()
    {
        $this->flow->addItemValidator(new PurchaseFlowTest_FailValidator('error 1'));
        $this->flow->addItemValidator(new PurchaseFlowTest_FailValidator('error 2'));
        $itemHolder = new Order();
        $itemHolder->addOrderItem(new OrderItem());
        $itemHolder->addOrderItem(new OrderItem());

        $expected = new PurchaseFlowResult($itemHolder);
        $expected->addProcessResult(ProcessResult::warn('error 1', PurchaseFlowTest_FailValidator::class));
        $expected->addProcessResult(ProcessResult::warn('error 2', PurchaseFlowTest_FailValidator::class));
        $expected->addProcessResult(ProcessResult::warn('error 1', PurchaseFlowTest_FailValidator::class));
        $expected->addProcessResult(ProcessResult::warn('error 2', PurchaseFlowTest_FailValidator::class));
        self::assertEquals($expected, $this->flow->validate($itemHolder, new PurchaseContext()));
    }

    /**
     * @dataProvider flowTypeProvider
     *
     * @param $flow
     * @param $message
     */
    public function testFlowType($flow, $message)
    {
        $this->flow->addItemHolderValidator(new PurchaseFlowTest_FlowTypeValidator());
        $itemHolder = new Order();
        $itemHolder->addOrderItem(new OrderItem());

        $expected = new PurchaseFlowResult($itemHolder);
        $expected->addProcessResult(ProcessResult::error($message, PurchaseFlowTest_FlowTypeValidator::class));
        $this->flow->setFlowType($flow);
        self::assertEquals($expected, $this->flow->validate($itemHolder, new PurchaseContext()));
    }

    public function flowTypeProvider()
    {
        return [
            ['cart', 'Cart Flow'],
            ['shopping', 'Shopping Flow'],
            ['order', 'Order Flow'],
        ];
    }
}

class PurchaseFlowTest_ItemHolderPreprocessor implements ItemHolderPreprocessor
{
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
    }
}

class PurchaseFlowTest_ItemPreprocessor implements ItemPreprocessor
{
    public function process(ItemInterface $item, PurchaseContext $context)
    {
    }
}

class PurchaseFlowTest_FailValidator extends ItemValidator
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

class PurchaseFlowTest_FailItemHolderValidator extends ItemHolderValidator
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

class PurchaseFlowTest_FlowTypeValidator extends ItemHolderValidator
{
    protected function validate(ItemHolderInterface $item, PurchaseContext $context)
    {
        if ($context->isCartFlow()) {
            throw new InvalidItemException('Cart Flow');
        }
        if ($context->isShoppingFlow()) {
            throw new InvalidItemException('Shopping Flow');
        }
        if ($context->isOrderFlow()) {
            throw new InvalidItemException('Order Flow');
        }
    }
}
