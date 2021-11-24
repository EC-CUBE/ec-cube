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

class RelatedProductEditPage extends ProductEditPage
{
    public static function goEdit($I, $id)
    {
        $page = new self($I);
        return $page->goPage("/product/product/${id}/edit", '商品登録商品管理');
    }

    public function 選択_関連商品1()
    {
        $this->tester->scrollTo(['id' => 'RelatedProduct-search0'], 0, -100);
        $this->tester->click(['id' => 'RelatedProduct-search0']);
        $this->tester->waitForElementVisible(['id' => 'RelatedProductSearchButton']);
        $this->tester->click(['id' => 'RelatedProductSearchButton']);
        $this->tester->waitForElementVisible(['xpath' => '//*[@id="RelatedProductSearchResult"]/div/table/tbody/tr[1]/td[2]/button']);
        $this->tester->click(['xpath' => '//*[@id="RelatedProductSearchResult"]/div/table/tbody/tr[1]/td[2]/button']);
        return $this;
    }

    public function 削除_関連商品1() {
        $this->tester->scrollTo(['id' => 'RelatedProduct-delete0'], 0, -100);
        $this->tester->click(['id' => 'RelatedProduct-delete0']);
        return $this;
    }

    public function 入力_説明文1($value)
    {
        $this->tester->fillField(['id' => 'admin_product_RelatedProducts_1_content'], $value);
        return $this;
    }
}
