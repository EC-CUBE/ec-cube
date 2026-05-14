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

namespace Page\Front;

class ProductDetailPage extends AbstractFrontPage
{
    /**
     * @param $I
     * @param $id
     */
    public static function go($I, $id): ProductDetailPage
    {
        $page = new self($I);

        return $page->goPage('/products/detail/'.$id);
    }

    public function カテゴリ選択($categories)
    {
        $xpath = "//*[@class='ec-layoutRole__header']/";
        foreach ($categories as $category) {
            $xpath .= "/ul/li/a[contains(text(), '$category')]/parent::node()";
            $this->tester->waitForElement(['xpath' => $xpath]);
            $this->tester->moveMouseOver(['xpath' => $xpath]);
        }
        $this->tester->click(['xpath' => $xpath]);

        return $this;
    }

    public function サムネイル切替($num)
    {
        $this->tester->click("div:nth-child(1) > div > div.item_nav > div:nth-child({$num})");

        return $this;
    }

    public function サムネイル画像URL($num)
    {
        return $this->tester->grabAttributeFrom("div:nth-child(1) > div > div.item_nav > div:nth-child({$num}) > img", 'src');
    }

    public function 規格選択($array)
    {
        foreach ($array as $index => $option) {
            $this->tester->selectOption(['id' => 'classcategory_id'.($index + 1)], $option);
        }

        return $this;
    }

    public function カートに入れる(int $num, ?array $category1 = null, ?array $category2 = null): ProductDetailPage
    {
        $this->tester->fillField(['id' => 'quantity'], $num);
        if (!is_null($category1)) {
            $this->tester->selectOption(['id' => 'classcategory_id1'], $category1);
            if (!is_null($category2)) {
                $category2_id = current(array_keys($category2));
                $this->tester->waitForElement(['xpath' => "//*[@id='classcategory_id2']/option[@value='{$category2_id}']"]);
                $this->tester->selectOption(['id' => 'classcategory_id2'], $category2);
            }
        }
        $this->tester->click(['class' => 'add-cart']);
        $this->tester->waitForElementVisible(['css' => 'div.ec-modal-box']);

        return $this;
    }

    public function お気に入りに追加()
    {
        $this->tester->click('#favorite');

        return $this;
    }

    public function カートに追加()
    {
        return $this->tester->grabTextFrom(['xpath' => '//*[@id="ec-modal-header"]']);
    }

    public function カートへ進む(): CartPage
    {
        $this->tester->click('div.ec-modal-box > div > a');

        return CartPage::at($this->tester);
    }
}
