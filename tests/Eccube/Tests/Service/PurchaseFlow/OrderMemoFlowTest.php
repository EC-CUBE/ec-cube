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
 * 受注管理用メモのコピー処理が購入フローへ正しく配線されていることを検証する。
 *
 * 振る舞い(明細へのコピー・送料等の除外・NULL)は OrderMemoPreprocessorTest が担保する。
 * 本テストは purchaseflow.yaml の登録、すなわち
 *   - 確定フロー(shopping)には登録されている        … 注文確定時にコピーされる
 *   - 受注フロー(order)には登録されていない          … 確定後は再コピーされず、確定時点のスナップショットが保持される
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

    public function testNotRegisteredInOrderFlow(): void
    {
        // 受注フローには登録されていない（確定後の再コピーを防ぎスナップショットを保持する）
        $this->assertStringNotContainsString(OrderMemoPreprocessor::class, $this->orderFlow->dump());
    }
}
