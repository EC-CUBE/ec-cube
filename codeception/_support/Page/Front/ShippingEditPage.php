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


class ShippingEditPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('お届け先の変更', 'div.ec-pageHeader h1');
        return $page;
    }

    public function 入力_姓($value)
    {
        $this->tester->fillField(['id' => 'shopping_shipping_name_name01'], $value);
        return $this;
    }

    public function 登録する()
    {
        $this->tester->click('div.ec-RegisterRole__actions button.ec-blockBtn--action');
        return $this;
    }
}