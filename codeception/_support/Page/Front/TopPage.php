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
        $this->tester->click(['css' => "#page_homepage > div.ec-layoutRole > div.ec-layoutRole__contents > div > div.ec-layoutRole__mainBottom > div:nth-child(6) > div > div.ec-newsRole__news > div:nth-child($rowNum) > div.ec-newsRole__newsHeading > div.ec-newsRole__newsColumn > div.ec-newsRole__newsClose > a"]);

        return $this;
    }

    public function 新着情報詳細($rowNum)
    {
        return $this->tester->grabTextFrom(['css' => "#page_homepage > div.ec-layoutRole > div.ec-layoutRole__contents > div > div.ec-layoutRole__mainBottom > div:nth-child(6) > div > div.ec-newsRole__news > div.ec-newsRole__newsItem.is_active > div.ec-newsRole__newsDescription"]);
    }

    public function 新着情報リンククリック($rowNum)
    {
        $this->tester->click(['css' => "#page_homepage > div.ec-layoutRole > div.ec-layoutRole__contents > div > div.ec-layoutRole__mainBottom > div:nth-child(6) > div > div.ec-newsRole__news > div.ec-newsRole__newsItem.is_active > div.ec-newsRole__newsDescription > a"]);
    }

    public function カテゴリ選択($categories)
    {
        $xpath = "//*[@class='ec-layoutRole__header']/";
        foreach ($categories as $i => $category) {
            $xpath .= "/ul/li/a[contains(text(), '$category')]/parent::node()";
            $this->tester->waitForElement(['xpath' => $xpath]);
            $this->tester->moveMouseOver(['xpath' => $xpath]);
        }
        $this->tester->click(['xpath' => $xpath]);

        return $this;
    }

    public function 検索($keyword = null)
    {
        if ($keyword) {
            $this->tester->fillField(['id' => 'name'], $keyword);
        }
        $this->tester->click('button.ec-headerSearch__keywordBtn');

        return ProductListPage::at($this->tester);
    }
}
