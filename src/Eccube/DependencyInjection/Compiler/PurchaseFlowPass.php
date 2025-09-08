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

use Eccube\Annotation\CartFlow;
use Eccube\Annotation\OrderFlow;
use Eccube\Annotation\ShoppingFlow;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class PurchaseFlowPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const ITEM_VALIDATOR_TAG = 'eccube.item.validator';
    public const ITEM_HOLDER_VALIDATOR_TAG = 'eccube.item.holder.validator';
    public const ITEM_PREPROCESSOR_TAG = 'eccube.item.preprocessor';
    public const ITEM_HOLDER_PREPROCESSOR_TAG = 'eccube.item.holder.preprocessor';
    public const DISCOUNT_PROCESSOR_TAG = 'eccube.discount.processor';
    public const ITEM_HOLDER_POST_VALIDATOR_TAG = 'eccube.item.holder.post.validator';
    public const PURCHASE_PROCESSOR_TAG = 'eccube.purchase.processor';

    #[\Override]
    public function process(ContainerBuilder $container)
    {
        $flowTypes = [
            PurchaseContext::CART_FLOW => $container->findDefinition('eccube.purchase.flow.cart'),
            PurchaseContext::SHOPPING_FLOW => $container->findDefinition('eccube.purchase.flow.shopping'),
            PurchaseContext::ORDER_FLOW => $container->findDefinition('eccube.purchase.flow.order'),
        ];

        /*
         * purchaseflow.yamlに定義を追加した場合の処理
         */
        foreach ($this->getProcessorTags() as $tag => $methodName) {
            $allMethod = [];
            $i = 0;
            // findAndSortTaggedServicesでは、flow_typeごとのソートができないため
            // その並びをもとに配列に入れ直す
            foreach ($this->findAndSortTaggedServices($tag, $container) as $id) {
                $def = $container->findDefinition($id);
                foreach ($def->getTag($tag) as $attributes) {
                    if (isset($attributes['flow_type'])) {
                        $attributes['id'] = $id;
                        $attributes['index'] = ++$i;
                        $attributes['priority'] ??= 0;
                        $allMethod[$attributes['flow_type']][] = $attributes;
                    }
                }
            }
            /**
             * @var string $flowType
             * @var Definition $purchaseFlowDef
             */
            foreach ($allMethod as $flowType => $flowMethod) {
                $purchaseFlowDef = $flowTypes[$flowType] ?? null;
                if (!is_null($purchaseFlowDef) && count($flowMethod) > 0) {
                    // flow_typeごとにソートをしてセットする
                    uasort($flowMethod, static fn ($a, $b) => $b['priority'] <=> $a['priority'] ?: $a['index'] <=> $b['index']);
                    foreach ($flowMethod as $attributes) {
                        $purchaseFlowDef->addMethodCall($methodName, [$attributes['id']]);
                    }
                }
            }
        }

        $flowDefs = [
            CartFlow::class => $container->findDefinition('eccube.purchase.flow.cart'),
            ShoppingFlow::class => $container->findDefinition('eccube.purchase.flow.shopping'),
            OrderFlow::class => $container->findDefinition('eccube.purchase.flow.order'),
        ];

        /*
         * アトリビュートで追加対象のフローを指定した場合の処理
         */
        foreach ($this->getProcessorTags() as $tag => $methodName) {
            /** @var Reference $id */
            foreach ($this->findAndSortTaggedServices($tag, $container) as $id) {
                $def = $container->getDefinition($id);
                // %param% を含むことがあるため、解決してからReflectionする。
                $class = $container->getParameterBag()->resolveValue($def->getClass());
                // クラスが無い場合はスキップ
                if (!\is_string($class) || !class_exists($class)) {
                    continue;
                }
                $rc = new \ReflectionClass($class);

                /**
                 * @var string     $attributeClass
                 * @var Definition $purchaseFlowDef
                 */
                foreach ($flowDefs as $attributeClass => $purchaseFlowDef) {
                    // IS_INSTANCEOFで継承した属性も拾う
                    if (\count($rc->getAttributes($attributeClass, \ReflectionAttribute::IS_INSTANCEOF)) > 0) {
                        // 既にYAML側で配線済みならスキップ（重複防止）
                        if (!$this->alreadyWired($purchaseFlowDef, $methodName, $id)) {
                            $purchaseFlowDef->addMethodCall($methodName, [$id]);
                            $purchaseFlowDef->setPublic(true);
                        }
                    }
                }
            }
        }
    }

    /**
     * @return string[]
     */
    private function getProcessorTags(): array
    {
        return [
            self::ITEM_PREPROCESSOR_TAG => 'addItemPreprocessor',
            self::ITEM_VALIDATOR_TAG => 'addItemValidator',
            self::ITEM_HOLDER_PREPROCESSOR_TAG => 'addItemHolderPreprocessor',
            self::ITEM_HOLDER_VALIDATOR_TAG => 'addItemHolderValidator',
            self::ITEM_HOLDER_POST_VALIDATOR_TAG => 'addItemHolderPostValidator',
            self::DISCOUNT_PROCESSOR_TAG => 'addDiscountProcessor',
            self::PURCHASE_PROCESSOR_TAG => 'addPurchaseProcessor',
        ];
    }

    /**
     * 既に同一メソッド・同一サービスIDが登録済みかを定義から判定し、二重登録を防ぐ。
     *
     * @param Definition $flowDef
     * @param string     $methodName
     * @param string     $serviceId
     *
     * @return bool
     */
    private function alreadyWired(Definition $flowDef, string $methodName, string $serviceId): bool
    {
        foreach ($flowDef->getMethodCalls() as [$m, $args]) {
            if ($m !== $methodName || empty($args)) {
                continue;
            }
            // 文字列IDでも Reference でも比較可能に正規化
            $arg0 = (string) $args[0];
            if ($arg0 === $serviceId) {
                return true;
            }
        }

        return false;
    }
}
