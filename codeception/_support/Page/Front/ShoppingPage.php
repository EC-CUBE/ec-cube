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


class ShoppingPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('ご注文手続き', 'div.ec-pageHeader h1');
        $page->tester->see('お客様情報', '#shopping-form div.ec-orderAccount div.ec-rectHeading h2');
        $page->tester->see('配送情報', '#shopping-form div.ec-orderDelivery div.ec-rectHeading h2');
        $page->tester->see('お届け先', '#shopping-form div.ec-orderDelivery div.ec-orderDelivery__title');
        $page->tester->see('お支払方法', '#shopping-form div.ec-orderPayment div.ec-rectHeading h2');
        $page->tester->see('お問い合わせ', '#shopping-form div.ec-orderConfirm div.ec-rectHeading h2');
        $page->tester->see('小計', '#shopping-form div.ec-orderRole__summary div.ec-totalBox');
        $page->tester->see('手数料', '#shopping-form div.ec-orderRole__summary div.ec-totalBox');
        $page->tester->see('送料', '#shopping-form div.ec-orderRole__summary div.ec-totalBox');
        $page->tester->see('合計', '#shopping-form div.ec-orderRole__summary div.ec-totalBox');
        return $page;
    }

    public function 確認する()
    {
        $this->tester->click('#shopping-form div.ec-orderRole__summary div.ec-totalBox button');
        return $this;
    }

    public function お客様情報変更()
    {
        $this->tester->click('#shopping-form #customer');
        $this->tester->waitForElementVisible(['id' => 'edit0']);
        return $this;
    }

    public function 入力_姓($value)
    {
        $this->tester->fillField(['id' => 'edit0'], $value);
        return $this;
    }

    public function お客様情報変更OK()
    {
        $this->tester->click('div.ec-orderAccount #customer-ok button');
        $this->tester->wait(5);
        return $this;
    }

    public function お届け先変更()
    {
        $this->tester->click('#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div.ec-orderDelivery__title > div > button');
        return $this;
    }

    public function お届け先追加()
    {
        $this->tester->click('#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div.ec-orderDelivery__edit > button');
        return $this;
    }
}