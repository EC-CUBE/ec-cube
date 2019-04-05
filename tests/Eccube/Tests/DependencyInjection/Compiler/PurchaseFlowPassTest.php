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

namespace Eccube\Tests\DependencyInjection\Compiler;

use Eccube\Annotation\CartFlow;
use Eccube\Annotation\OrderFlow;
use Eccube\Annotation\ShoppingFlow;
use Eccube\DependencyInjection\Compiler\PurchaseFlowPass;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\DiscountProcessor;
use Eccube\Service\PurchaseFlow\ItemHolderPostValidator;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\ItemPreprocessor;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PurchaseFlowPassTest extends EccubeTestCase
{
    public static $called = false;

    public function setUp()
    {
        self::$called = false;
        parent::setUp();
    }

    /**
     * @dataProvider dataProcessorProvider
     *
     * @param $class
     * @param $id
     * @param $tagName
     *
     * @throws \Exception
     */
    public function testProcess($class, $id, $tagName)
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);

        $container = $this->createContainer();

        $container->register($class)
            ->addTag($tagName);

        $container->compile();

        $purchaseFlow = $container->get($id);
        $purchaseFlow->validate($Order, new PurchaseContext());
        $purchaseFlow->prepare($Order, new PurchaseContext());

        self::assertTrue(PurchaseFlowPassTest::$called);
    }

    public function dataProcessorProvider()
    {
        return [
            [PurchaseFlowPassTest_CartFlow::class, 'eccube.purchase.flow.cart', PurchaseFlowPass::ITEM_HOLDER_VALIDATOR_TAG],
            [PurchaseFlowPassTest_ShoppingFlow::class, 'eccube.purchase.flow.shopping', PurchaseFlowPass::ITEM_HOLDER_VALIDATOR_TAG],
            [PurchaseFlowPassTest_OrderFlow::class, 'eccube.purchase.flow.order', PurchaseFlowPass::ITEM_HOLDER_VALIDATOR_TAG],
            [PurchaseFlowPassTest_ItemPreprocessor::class, 'eccube.purchase.flow.cart', PurchaseFlowPass::ITEM_PREPROCESSOR_TAG],
            [PurchaseFlowPassTest_ItemValidator::class, 'eccube.purchase.flow.cart', PurchaseFlowPass::ITEM_VALIDATOR_TAG],
            [PurchaseFlowPassTest_ItemHolderPreprocessor::class, 'eccube.purchase.flow.cart', PurchaseFlowPass::ITEM_HOLDER_PREPROCESSOR_TAG],
            [PurchaseFlowPassTest_ItemHolderValidator::class, 'eccube.purchase.flow.cart', PurchaseFlowPass::ITEM_HOLDER_VALIDATOR_TAG],
            [PurchaseFlowPassTest_ItemHolderPostValidator::class, 'eccube.purchase.flow.cart', PurchaseFlowPass::ITEM_HOLDER_POST_VALIDATOR_TAG],
            [PurchaseFlowPassTest_DiscountProcessor::class, 'eccube.purchase.flow.cart', PurchaseFlowPass::DISCOUNT_PROCESSOR_TAG],
            [PurchaseFlowPassTest_PurchaseProcessor::class, 'eccube.purchase.flow.cart', PurchaseFlowPass::PURCHASE_PROCESSOR_TAG],
        ];
    }

    public function createContainer()
    {
        $container = new ContainerBuilder();

        $container->register('eccube.purchase.flow.cart', PurchaseFlow::class);
        $container->register('eccube.purchase.flow.shopping', PurchaseFlow::class);
        $container->register('eccube.purchase.flow.order', PurchaseFlow::class);

        $container->addCompilerPass(new PurchaseFlowPass());

        return $container;
    }
}

/**
 * Class PurchaseFlowPassTest_CartFlow
 *
 * @CartFlow
 */
class PurchaseFlowPassTest_CartFlow extends ItemHolderValidator
{
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        PurchaseFlowPassTest::$called = true;
    }
}

/**
 * Class PurchaseFlowPassTest_ShoppingFlow
 *
 * @ShoppingFlow
 */
class PurchaseFlowPassTest_ShoppingFlow extends ItemHolderValidator
{
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        PurchaseFlowPassTest::$called = true;
    }
}

/**
 * Class PurchaseFlowPassTest_OrderFlow
 *
 * @OrderFlow
 */
class PurchaseFlowPassTest_OrderFlow extends ItemHolderValidator
{
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        PurchaseFlowPassTest::$called = true;
    }
}

/**
 * Class PurchaseFlowPassTest_ItemPreprocessor
 *
 * @CartFlow
 */
class PurchaseFlowPassTest_ItemPreprocessor implements ItemPreprocessor
{
    public function process(ItemInterface $item, PurchaseContext $context)
    {
        PurchaseFlowPassTest::$called = true;
    }
}

/**
 * Class PurchaseFlowPassTest_ItemValidator
 *
 * @CartFlow
 */
class PurchaseFlowPassTest_ItemValidator extends ItemValidator
{
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        PurchaseFlowPassTest::$called = true;
    }
}

/**
 * Class PurchaseFlowPassTest_ItemHolderPreprocessor
 *
 * @CartFlow
 */
class PurchaseFlowPassTest_ItemHolderPreprocessor implements ItemHolderPreprocessor
{
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        PurchaseFlowPassTest::$called = true;
    }
}

/**
 * Class PurchaseFlowPassTest_ItemHolderValidator
 *
 * @CartFlow
 */
class PurchaseFlowPassTest_ItemHolderValidator extends ItemHolderValidator
{
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        PurchaseFlowPassTest::$called = true;
    }
}

/**
 * Class PurchaseFlowPassTest_ItemHolderPostValidator
 *
 * @CartFlow
 */
class PurchaseFlowPassTest_ItemHolderPostValidator extends ItemHolderPostValidator
{
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        PurchaseFlowPassTest::$called = true;
    }
}

/**
 * Class PurchaseFlowPassTest_DiscountProcessor
 *
 * @CartFlow
 */
class PurchaseFlowPassTest_DiscountProcessor implements DiscountProcessor
{
    public function removeDiscountItem(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        PurchaseFlowPassTest::$called = true;
    }

    public function addDiscountItem(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        PurchaseFlowPassTest::$called = true;
    }
}

/**
 * Class PurchaseFlowPassTest_PurchaseProcessor
 *
 * @CartFlow
 */
class PurchaseFlowPassTest_PurchaseProcessor implements PurchaseProcessor
{
    public function prepare(ItemHolderInterface $target, PurchaseContext $context)
    {
        PurchaseFlowPassTest::$called = true;
    }

    public function commit(ItemHolderInterface $target, PurchaseContext $context)
    {
        PurchaseFlowPassTest::$called = true;
    }

    public function rollback(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        PurchaseFlowPassTest::$called = true;
    }
}
