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


class ProductDetailPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I, $id)
    {
        $page = new self($I);
        return $page->goPage('products/detail/'.$id);
    }

    public function カテゴリ選択($categories)
    {
        $xpath = "//*[@class='ec-layoutRole__header']/";
        foreach ($categories as $i=>$category) {
            $xpath .= "/ul/li/a[contains(text(), '$category')]/parent::node()";
            $this->tester->waitForElement(['xpath' => $xpath]);
            $this->tester->moveMouseOver(['xpath' => $xpath]);
        }
        $this->tester->click(['xpath' => $xpath]);
        return $this;
    }

    public function サムネイル切替($num)
    {
        $this->tester->click("div.item_nav div.slick-list div.slick-track div.slideThumb:nth-child(${num})");
        return $this;
    }

    public function サムネイル画像URL()
    {
        return $this->tester->grabAttributeFrom('div.item.slick-slide.slick-current.slick-active img', 'src');
    }

    public function 規格選択($array)
    {
        foreach ($array as $index=>$option) {
            $this->tester->selectOption(['id' => 'classcategory_id'.($index+1)], $option);
        }
        return $this;
    }

    /**
     * @param $num|int
     * @return ProductDetailPage
     */
    public function カートに入れる($num)
    {
        $this->tester->fillField(['id' => 'quantity'], $num);
        $this->tester->click(['id' => 'add-cart']);
        $this->tester->wait(1);
        return $this;
    }

    public function お気に入りに追加()
    {
        $this->tester->click('#favorite');
        return $this;
    }
}