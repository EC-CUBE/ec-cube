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

namespace Eccube\Tests\Service;

use Eccube\Entity\CartItem;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\OrderItem;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;
use Eccube\Tests\EccubeTestCase;

class ValidatableItemProcessorTest extends EccubeTestCase
{
    /*
     * カートの場合
     *      エラーなら明細丸め処理 ＆ カート画面にエラー表示¨
     *      正常時は丸め処理しない
     * 購入の場合
     *      エラーなら購入エラーで終了
     *      正常時は丸め処理しない
     */
    public function testValidateCartSuccess()
    {
        $validator = new ValidatableItemProcessorTest_NormalValidator();
        $item = new CartItem();

        $validator->process($item, new PurchaseContext());
        $this->assertFalse($validator->handleCalled);
    }

    public function testValidateCartFail()
    {
        // TODO: FIXME
        $this->markTestIncomplete(__METHOD__.'may be not implement');

        $validator = new ValidatableItemProcessorTest_FailValidator();
        $item = new CartItem();

        $validator->process($item, new PurchaseContext());
    }

    public function testValidateOrderSuccess()
    {
        $validator = new ValidatableItemProcessorTest_NormalValidator();
        $item = new OrderItem();

        $result = $validator->process($item, new PurchaseContext());
        self::assertFalse($validator->handleCalled);
        self::assertFalse($result->isError());
    }

    public function testValidateOrderFail()
    {
        $validator = new ValidatableItemProcessorTest_FailValidator();
        $item = new OrderItem();

        $result = $validator->process($item, new PurchaseContext());
        self::assertFalse($validator->handleCalled);
        self::assertTrue($result->isWarning());
    }
}

class ValidatableItemProcessorTest_NormalValidator extends ValidatableItemProcessor
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

class ValidatableItemProcessorTest_FailValidator extends ValidatableItemProcessor
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
