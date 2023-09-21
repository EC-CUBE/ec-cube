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

namespace Eccube\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Eccube\Annotation\CartFlow;
use Eccube\Annotation\OrderFlow;
use Eccube\Annotation\ShoppingFlow;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PurchaseFlowPass implements CompilerPassInterface
{
    public const ITEM_PREPROCESSOR_TAG = 'eccube.item.preprocessor';
    public const ITEM_VALIDATOR_TAG = 'eccube.item.validator';
    public const ITEM_HOLDER_PREPROCESSOR_TAG = 'eccube.item.holder.preprocessor';
    public const ITEM_HOLDER_VALIDATOR_TAG = 'eccube.item.holder.validator';
    public const ITEM_HOLDER_POST_VALIDATOR_TAG = 'eccube.item.holder.post.validator';
    public const DISCOUNT_PROCESSOR_TAG = 'eccube.discount.processor';
    public const PURCHASE_PROCESSOR_TAG = 'eccube.purchase.processor';

    public function process(ContainerBuilder $container)
    {
        $flowDefs = [
            CartFlow::class => $container->getDefinition('eccube.purchase.flow.cart'),
            ShoppingFlow::class => $container->getDefinition('eccube.purchase.flow.shopping'),
            OrderFlow::class => $container->getDefinition('eccube.purchase.flow.order'),
        ];

        $processorTags = [
            self::ITEM_PREPROCESSOR_TAG => 'addItemPreprocessor',
            self::ITEM_VALIDATOR_TAG => 'addItemValidator',
            self::ITEM_HOLDER_PREPROCESSOR_TAG => 'addItemHolderPreprocessor',
            self::ITEM_HOLDER_VALIDATOR_TAG => 'addItemHolderValidator',
            self::ITEM_HOLDER_POST_VALIDATOR_TAG => 'addItemHolderPostValidator',
            self::DISCOUNT_PROCESSOR_TAG => 'addDiscountProcessor',
            self::PURCHASE_PROCESSOR_TAG => 'addPurchaseProcessor',
        ];

        AnnotationRegistry::registerAutoloadNamespace('Eccube\Annotation', __DIR__.'/../../../../src');
        $reader = new AnnotationReader();

        foreach ($processorTags as $tag => $methodName) {
            $ids = $container->findTaggedServiceIds($tag);
            foreach ($ids as $id => $tags) {
                $def = $container->getDefinition($id);
                foreach ($flowDefs as $annotationName => $purchaseFlowDef) {
                    $anno = $reader->getClassAnnotation(new \ReflectionClass($def->getClass()), $annotationName);
                    if ($anno) {
                        $purchaseFlowDef->addMethodCall($methodName, [new Reference($id)]);
                        $purchaseFlowDef->setPublic(true);
                    }
                }
            }
        }
    }
}
