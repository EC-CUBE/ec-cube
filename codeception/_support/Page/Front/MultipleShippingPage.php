<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
        $page->tester->see('お届け先の複数指定', 'div.ec-layoutRole__main h1');
        return $page;
    }

    public function 新規お届け先を追加する()
    {
        $this->tester->click('#multiple_list_box__body > p:nth-child(2) > a');
        return $this;
    }

    public function お届け先追加()
    {
        $this->tester->click('#button__add0');
        return $this;
    }

    public function 入力_お届け先($productNo, $shippingNo, $text)
    {
        $id = 'form_shipping_multiple_' . $productNo . '_shipping_' . $shippingNo . '_customer_address';
        $this->tester->selectOption(['id' => $id], ['text' => $text]);
        return $this;
    }

    public function 入力_数量($productNo, $shippingNo, $value)
    {
        $id = 'form_shipping_multiple_' . $productNo . '_shipping_' . $shippingNo . '_quantity';
        $this->tester->fillField(['id' => $id], $value);
        return $this;
    }

    public function 選択したお届け先に送る()
    {
        $this->tester->click('#button__confirm');
        return $this;
    }
}