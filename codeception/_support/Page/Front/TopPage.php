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


class TopPage extends AbstractFrontPage
{

    public static $検索_カテゴリ選択 = ['id' => 'category_id'];
    public static $検索_キーワード = ['id' => 'name'];


    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        return $page->goPage('');
    }

    public function 新着情報選択($rowNum)
    {
        $this->tester->click(['css' => "div.ec-news .ec-newsline:nth-child($rowNum) a"]);
        return $this;
    }

    public function 新着情報詳細($rowNum)
    {
        return $this->tester->grabTextFrom(['css' => "div.ec-news .ec-newsline:nth-child($rowNum) .ec-newsline__description"]);
    }

    public function 新着情報リンククリック($rowNum)
    {
        $this->tester->click(['css' => "div.ec-news .ec-newsline:nth-child($rowNum) .ec-newsline__description a"]);
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

    public function 検索()
    {
        $this->tester->click('button.ec-headerSearch__keywordBtn');
        return $this;
    }
}