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

class MultipleShippingPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('お届け先の複数指定', 'div.ec-pageHeader h1');

        return $page;
    }

    public function 新規お届け先を追加する()
    {
        $this->tester->click('#shipping-multiple-form > div.ec-AddAddress__new > a');

        return $this;
    }

    public function お届け先追加()
    {
        $this->tester->click('#button__add0');

        return $this;
    }

    public function 入力_お届け先($productNo, $shippingNo, $text)
    {
        $id = 'form_shipping_multiple_'.$productNo.'_shipping_'.$shippingNo.'_customer_address';
        $this->tester->selectOption(['id' => $id], ['text' => $text]);

        return $this;
    }

    public function 入力_数量($productNo, $shippingNo, $value)
    {
        $id = 'form_shipping_multiple_'.$productNo.'_shipping_'.$shippingNo.'_quantity';
        $this->tester->fillField(['id' => $id], $value);

        return $this;
    }

    public function 選択したお届け先に送る()
    {
        $this->tester->click('#button__confirm');

        return $this;
    }
}
