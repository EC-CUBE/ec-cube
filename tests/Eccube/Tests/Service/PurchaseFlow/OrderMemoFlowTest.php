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
 * 振る舞い(明細へのスナップショットコピー・送料等の除外・NULL)は
 * OrderMemoPreprocessorTest が担保する。
 * 本テストは purchaseflow.yaml の登録、すなわち
 *   - 確定フロー(shopping)にのみ登録されている … 注文確定時にコピーされる
 *   - 受注フロー(order)には登録されていない     … 確定後の受注編集では再コピーしない
 *                                                  (確定時点のスナップショットを保持する)
 * を担保する(Issue #6821 §3.2)。
 */
final class OrderMemoFlowTest extends EccubeTestCase
{
    private ?PurchaseFlow $shoppingFlow = null;

    private ?PurchaseFlow $orderFlow = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shoppingFlow = static::getContainer()->get(PurchaseFlow::class);
        $this->orderFlow = static::getContainer()->get(PurchaseFlow::class);
    }

    public function testRegisteredInShoppingFlow(): void
    {
        // 確定フローには OrderMemoPreprocessor が登録されている
        $this->assertStringContainsString(OrderMemoPreprocessor::class, $this->shoppingFlow->dump());
    }

    public function testNotRegisteredInOrderFlow(): void
    {
        // 受注フローには登録しない(確定後の受注編集で再コピーせず、確定時点の値を保持する)
        $this->assertStringNotContainsString(OrderMemoPreprocessor::class, $this->orderFlow->dump());
    }
}
