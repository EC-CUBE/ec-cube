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

class ProductReviewManagePage extends AbstractAdminPageStyleGuide
{
    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        $page->goPage('/product_review/', 'レビュー管理商品管理');
        return $page;
    }

    public function 検索()
    {
        $this->tester->click(['css' => '#search_form > div:nth-child(4) > button']);
        return $this;
    }

    public function 編集($i)
    {
        $this->tester->click(['xpath' => "//*[@id=\"page_product_review_admin_product_review\"]/div[1]/div[3]/div[3]/div/div/div[2]/div[1]/table/tbody/tr[${i}]/td[8]/div[1]/a[1]"]);
        return $this;
    }

    public function 削除($i)
    {
        $this->tester->click(['xpath' => "//*[@id=\"page_product_review_admin_product_review\"]/div[1]/div[3]/div[3]/div/div/div[2]/div[1]/table/tbody/tr[${i}]/td[8]/div[1]/a[2]"]);
        $this->tester->waitForElementVisible(['xpath' => "//*[@id=\"page_product_review_admin_product_review\"]/div[1]/div[3]/div[3]/div/div/div[2]/div[1]/table/tbody/tr[${i}]//div[contains(@class, 'modal')]//a[contains(@class, 'btn-ec-delete')]"]);
        $this->tester->click(['xpath' => "//*[@id=\"page_product_review_admin_product_review\"]/div[1]/div[3]/div[3]/div/div/div[2]/div[1]/table/tbody/tr[${i}]//div[contains(@class, 'modal')]//a[contains(@class, 'btn-ec-delete')]"]);
        return $this;
    }

    public function CSVダウンロード()
    {
        $this->tester->click(['css' => '#page_product_review_admin_product_review > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > div.row.justify-content-between.mb-2 > div.col-5.text-right > div:nth-child(2) > div > button:nth-child(1)']);
        return $this;
    }
}
