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

namespace Eccube\Tests\Service\PurchaseFlow;

use Eccube\Service\PurchaseFlow\Processor\OrderMemoPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Tests\EccubeTestCase;

/**
 * 受注管理用メモの追記処理が購入フローへ正しく配線されていることを検証する。
 *
 * 振る舞い(明細への追記・送料等の除外・NULL・冪等性)は OrderMemoPreprocessorTest が担保する。
 * 本テストは purchaseflow.yaml の登録、すなわち
 *   - 確定フロー(shopping)に登録されている  … フロントの注文確定時に追記される
 *   - 受注フロー(order)に登録されている      … 管理画面の受注作成・編集時にも追記される
 * を担保する。
 */
final class OrderMemoFlowTest extends EccubeTestCase
{
    private ?PurchaseFlow $shoppingFlow = null;

    private ?PurchaseFlow $orderFlow = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shoppingFlow = static::getContainer()->get('eccube.purchase.flow.shopping');
        $this->orderFlow = static::getContainer()->get('eccube.purchase.flow.order');
    }

    public function testRegisteredInShoppingFlow(): void
    {
        // 確定フローには OrderMemoPreprocessor が登録されている
        $this->assertStringContainsString(OrderMemoPreprocessor::class, $this->shoppingFlow->dump());
    }

    public function testRegisteredInOrderFlow(): void
    {
        // 受注フローにも登録されている（管理画面の受注作成・編集時にも追記する）
        $this->assertStringContainsString(OrderMemoPreprocessor::class, $this->orderFlow->dump());
    }
}
