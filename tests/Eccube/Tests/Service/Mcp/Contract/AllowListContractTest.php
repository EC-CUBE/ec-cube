<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Service\Mcp\Contract;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\Product;
use Eccube\Service\Mcp\AllowListResolver;
use Eccube\Service\Mcp\EntityArraySerializer;
use Eccube\Tests\EccubeTestCase;

/**
 * 受入基準 §8 #3 「Tool の出力フィールドが api44 の allow_list と一致する (未列挙フィールドが漏れない)」
 * の契約テスト。
 *
 * `EntityArraySerializer` は実装上「allow_list で許可されたプロパティのみ getter を呼ぶ」 構造なので、
 * 出力 keys ⊆ allow_list は構造的に保証される。 本テストは「allow_list 駆動の絞り込み」 が将来
 * リファクタリングで壊れないよう、 主要 3 entity (Product / Customer / Order) でこれを明示的に
 * 確認する回帰テスト。
 *
 * allow_list が空 (Api44 未 install 時) でも全プロパティ非公開という安全側に倒れる挙動も含めて検証。
 */
final class AllowListContractTest extends EccubeTestCase
{
    private ?AllowListResolver $resolver = null;
    private ?EntityArraySerializer $serializer = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = static::getContainer()->get(AllowListResolver::class);
        $this->serializer = static::getContainer()->get(EntityArraySerializer::class);
    }

    public function testProductOutputKeysAreSubsetOfAllowList(): void
    {
        $product = $this->createProduct('mcp-contract-product', 1);
        $output = $this->serializer->toArray($product);

        $this->assertSubsetOfAllowList(Product::class, $output);
    }

    public function testCustomerOutputKeysAreSubsetOfAllowList(): void
    {
        $customer = $this->createCustomer('mcp-contract-customer@example.com');
        $output = $this->serializer->toArray($customer);

        $this->assertSubsetOfAllowList(Customer::class, $output);
    }

    public function testOrderOutputKeysAreSubsetOfAllowList(): void
    {
        $customer = $this->createCustomer('mcp-contract-order@example.com');
        $order = $this->createOrder($customer);
        // PROCESSING (デフォルト) のままだと検索系で除外されるが、 シリアライズ自体には影響しない
        $orderStatusRepo = $this->entityManager->getRepository(OrderStatus::class);
        $order->setOrderStatus($orderStatusRepo->find(OrderStatus::NEW));
        $this->entityManager->flush();

        $output = $this->serializer->toArray($order);

        $this->assertSubsetOfAllowList(Order::class, $output);
    }

    public function testAllowListIsNotEmptyForCoreEntitiesWhenApi44Installed(): void
    {
        // Api44 が install されていれば、 これらの entity は少なくとも 1 つ以上のプロパティが allow_list に乗る
        foreach ([Product::class, Customer::class, Order::class] as $fqcn) {
            $props = $this->resolver->getAllowedProperties($fqcn);
            $this->assertNotEmpty($props, "{$fqcn} の allow_list が Api44 経由で取れている");
        }
    }

    /**
     * 出力 keys が allow_list の subset であり、 余分な key が無いことを確認。
     *
     * @param array<string, mixed> $output
     */
    private function assertSubsetOfAllowList(string $entityFqcn, array $output): void
    {
        $allowed = $this->resolver->getAllowedProperties($entityFqcn);
        $outputKeys = array_keys($output);

        $extra = array_diff($outputKeys, $allowed);

        $this->assertEmpty(
            $extra,
            sprintf(
                '%s の出力に allow_list 外の key が含まれている: [%s]。 allow_list: [%s]',
                $entityFqcn,
                implode(', ', $extra),
                implode(', ', $allowed),
            ),
        );
    }
}
