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

namespace Eccube\Tests\Service;

use Eccube\Entity\CartItem;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\OrderItem;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class ValidatableItemProcessorTest extends EccubeTestCase
{
    /*
     * カートの場合
     *      エラーなら明細丸め処理 ＆ カート画面にエラー表示¨
     *      正常時は丸め処理しない
     * 購入の場合
     *      Warningなら明細丸め処理 ＆ 注文手続き画面にエラー表示
     *      Errorなら購入エラーで終了
     *      正常時は丸め処理しない
     */
    public function testValidateCartSuccess()
    {
        $validator = new ItemValidatorTest_NormalValidator();
        $item = new CartItem();

        $validator->execute($item, new PurchaseContext());
        $this->assertFalse($validator->handleCalled);
    }

    public function testValidateCartFail()
    {
        // TODO: FIXME
        $this->markTestIncomplete(__METHOD__.'may be not implement');

        $validator = new ItemValidatorTest_FailValidator();
        $item = new CartItem();

        $validator->execute($item, new PurchaseContext());
    }

    public function testValidateOrderSuccess()
    {
        $validator = new ItemValidatorTest_NormalValidator();
        $item = new OrderItem();

        $result = $validator->execute($item, new PurchaseContext());
        self::assertFalse($validator->handleCalled);
        self::assertFalse($result->isError());
    }

    public function testValidateOrderFail()
    {
        $validator = new ItemValidatorTest_FailValidator();
        $item = new OrderItem();

        $result = $validator->execute($item, new PurchaseContext());
        self::assertTrue($validator->handleCalled);
        self::assertTrue($result->isWarning());
    }
}

class ItemValidatorTest_NormalValidator extends ItemValidator
{
    public $handleCalled = false;

    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        $this->handleCalled = true;
    }
}

class ItemValidatorTest_FailValidator extends ItemValidator
{
    public $handleCalled = false;

    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        throw new InvalidItemException();
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        $this->handleCalled = true;
    }
}
