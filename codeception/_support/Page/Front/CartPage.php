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


class CartPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I) {
        $page = new self($I);
        $page->goPage('/cart');
        return $page;
    }

    public function 商品名($index)
    {
        return $this->tester->grabTextFrom(["xpath" => "//div[@class='ec-cartRole']//ul[@class='ec-cartRow'][position()=${index}]//div[@class='ec-cartRow__name']"]);
    }

    public function 商品数量($index)
    {
        return $this->tester->grabTextFrom(["xpath" => "//div[@class='ec-cartRole']//ul[@class='ec-cartRow'][position()=${index}]//div[@class='ec-cartRow__amount']"]);
    }

    public function 商品数量増やす($index)
    {
        $this->tester->click(["xpath" => "//div[@class='ec-cartRole']//ul[@class='ec-cartRow'][position()=${index}]//div[@class='ec-cartRow__amountUpDown']/a[@class='ec-cartRow__amountUpButton']"]);
        return $this;
    }

    public function 商品数量減らす($index)
    {
        $this->tester->click(["xpath" => "//div[@class='ec-cartRole']//ul[@class='ec-cartRow'][position()=${index}]//div[@class='ec-cartRow__amountUpDown']/a[@class='ec-cartRow__amountDownButton']"]);
        return $this;
    }

    public function 商品削除($index)
    {
        $this->tester->click(["xpath" => "//div[@class='ec-cartRole']//ul[@class='ec-cartRow'][position()=${index}]//li[@class='ec-cartRow__delColumn']/a"]);
        $this->tester->acceptPopup();
        return $this;
    }

    public function エラーメッセージ()
    {
        return $this->tester->grabTextFrom(["css" => 'div.ec-cartRole__error div.ec-alert-warning__text']);
    }

    /**
     * @return ShoppingPage
     */
    public function レジに進む()
    {
        $this->tester->click(['css' => 'div.ec-cartRole__actions a.ec-blockBtn--action']);
        return new ShoppingPage($this->tester);
    }

    /**
     * @return TopPage
     */
    public function お買い物を続ける()
    {
        $this->tester->click(['css' => 'div.ec-cartRole__actions a.ec-blockBtn--cancel']);
        return new TopPage($this->tester);
    }
}