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

namespace Page\Front;

class ShoppingConfirmPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('ご注文内容のご確認', 'div.ec-pageHeader h1');
        $page->tester->see('お客様情報', '#shopping-form div.ec-orderAccount div.ec-rectHeading h2');
        $page->tester->see('配送情報', '#shopping-form div.ec-orderDelivery div.ec-rectHeading h2');
        $page->tester->see('お支払方法', '#shopping-form div.ec-orderPayment div.ec-rectHeading h2');
        $page->tester->see('お問い合わせ', '#shopping-form div.ec-orderConfirm div.ec-rectHeading h2');
        $page->tester->see('小計', '#shopping-form div.ec-orderRole__summary div.ec-totalBox');
        $page->tester->see('手数料', '#shopping-form div.ec-orderRole__summary div.ec-totalBox');
        $page->tester->see('送料', '#shopping-form div.ec-orderRole__summary div.ec-totalBox');
        $page->tester->see('合計', '#shopping-form div.ec-orderRole__summary div.ec-totalBox');

        return $page;
    }

    public function 注文する()
    {
        $this->tester->click('#shopping-form div.ec-orderRole__summary div.ec-totalBox button');

        return $this;
    }
}
