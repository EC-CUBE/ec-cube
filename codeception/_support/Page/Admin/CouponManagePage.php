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

class CouponManagePage extends AbstractAdminPageStyleGuide
{
    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        $page->goPage('/plugin/coupon', 'クーポン管理');
        return $page;
    }

    public static function at(\AcceptanceTester $I)
    {
        $page = new RecommendManagePage($I);
        $page->atPage('クーポン管理');
        return $page;
    }

    public function 新規登録()
    {
        $this->tester->click(['css' => '#search_form > div > div > div.card.rounded.border-0 > div > div > a']);
        return $this;
    }

    public function 編集($i)
    {
        $this->tester->click(['xpath' => '//*[@id="search_form"]/div/div/div[1]/div/div[2]/table/tbody/tr['.$i.']/td[10]/a']);
        return $this;
    }

    public function 状態変更($i)
    {
        $this->tester->click(['xpath' => '//*[@id="search_form"]/div/div/div[1]/div/div[2]/table/tbody/tr['.$i.']/td[11]/a']);
        return $this;
    }

    public function 削除($i)
    {
        $this->tester->click(['xpath' => '//*[@id="search_form"]/div/div/div[1]/div/div[2]/table/tbody/tr['.$i.']/td[12]/a']);
        $this->tester->waitForElementVisible(['xpath' => '//*[@id="search_form"]/div/div/div[1]/div/div[2]/table/tbody/tr['.$i.']/td[12]//a[contains(@class, "btn-ec-delete")]']);
        $this->tester->click(['xpath' => '//*[@id="search_form"]/div/div/div[1]/div/div[2]/table/tbody/tr['.$i.']/td[12]//a[contains(@class, "btn-ec-delete")]']);
        return $this;
    }
}
