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


class ProductListPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public function 表示件数設定($num)
    {
        $this->tester->selectOption(['css' => "select[name = 'disp_number']"], "${num}件");
        return $this;
    }

    public function 表示順設定($sort)
    {
        $this->tester->selectOption(['css' => "select[name = 'orderby']"], $sort);
        return $this;
    }

    public function 一覧件数取得()
    {
        $products = $this->tester->grabMultiple(['xpath' => "//*[@class='ec-shelfGrid__item']/a/p[1]"]);
        return count($products);
    }
}