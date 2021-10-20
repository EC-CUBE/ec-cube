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

namespace Page\Admin;

class RecommendEditPage extends AbstractAdminPageStyleGuide
{
    public static function at($I)
    {
        $page = new RecommendEditPage($I);
        $page->tester->see('おすすめ商品内容設定', 'body > div > div.c-contentsArea > div.c-pageTitle > div > span');

        return $page;
    }

    public function 商品追加()
    {
        $this->tester->click(['id' => 'showSearchProductModal']);
        $this->tester->waitForElementVisible(['id' => 'searchProductModalButton']);
        $this->tester->click(['id' => 'searchProductModalButton']);
        $this->tester->waitForElementVisible(['css' => '#searchProductModalList > div > table > tbody > tr']);
        $this->tester->click(['css' => '#searchProductModalList > div > table > tbody > tr:nth-child(2) > td.text-right > button']);
        return $this;
    }
    public function 入力_説明文($value)
    {
        $this->tester->fillField(['id' => 'recommend_product_comment'], $value);
        return $this;
    }

    public function 登録()
    {
        $this->tester->click(['css' => '#form1 > div > div > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button']);
        return $this;
    }
}
