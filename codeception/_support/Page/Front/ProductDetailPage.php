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
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    /**
     * @param $I
     * @param $id
     *
     * @return ProductDetailPage
     */
    public static function go($I, $id)
    {
        $page = new self($I);

        return $page->goPage('products/detail/'.$id);
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

    public function サムネイル切替($num)
    {
        $this->tester->click("div:nth-child(1) > div > div.item_nav > div:nth-child(${num})");

        return $this;
    }

    public function サムネイル画像URL($num)
    {
        return $this->tester->grabAttributeFrom("div:nth-child(1) > div > div.item_nav > div:nth-child(${num}) > img", 'src');
    }

    public function 規格選択($array)
    {
        foreach ($array as $index => $option) {
            $this->tester->selectOption(['id' => 'classcategory_id'.($index + 1)], $option);
        }

        return $this;
    }

    /**
     * @param $num |int
     * @param null $category1
     * @param null $category2
     *
     * @return ProductDetailPage
     */
    public function カートに入れる($num, $category1 = null, $category2 = null)
    {
        $this->tester->fillField(['id' => 'quantity'], $num);
        if (!is_null($category1)) {
            $this->tester->selectOption(['id' => 'classcategory_id1'], $category1);
            if (!is_null($category2)) {
                $category2_id = current(array_keys($category2));
                $this->tester->waitForElement(['xpath' => "//*[@id='classcategory_id2']/option[@value='${category2_id}']"]);
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

    /**
     * @return CartPage
     */
    public function カートへ進む()
    {
        $this->tester->click('div.ec-modal-box > div > a');

        return CartPage::at($this->tester);
    }
}
