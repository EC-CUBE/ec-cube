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

class ProductListPage extends AbstractFrontPage
{
    public static function at($I)
    {
        $page = new self($I);
        $page->tester->seeInCurrentUrl('/products/list');

        return $page;
    }

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

    public function カートに入れる($index, $num = 1, $category1 = null, $category2 = null)
    {
        $this->tester->fillField(['css' => "ul.ec-shelfGrid li.ec-shelfGrid__item:nth-child(${index}) form input[name='quantity']"], $num);
        if (!is_null($category1)) {
            $this->tester->selectOption(['css' => "ul.ec-shelfGrid li.ec-shelfGrid__item:nth-child(${index}) form select[name='classcategory_id1']"], $category1);
            if (!is_null($category2)) {
                $category2_id = current(array_keys($category2));
                $this->tester->waitForElement(['xpath' => "//ul[@class='ec-shelfGrid']/li[@class='ec-shelfGrid__item'][${index}]//select[@name='classcategory_id2']/option[@value='${category2_id}']"]);
                $this->tester->selectOption(['css' => "ul.ec-shelfGrid li.ec-shelfGrid__item:nth-child(${index}) form select[name='classcategory_id2']"], $category2);
            }
        }
        $this->tester->click(['class' => 'add-cart']);
        $this->tester->waitForElementVisible(['css' => 'div.ec-modal-box']);

        return $this;
    }

    public function カートへ進む()
    {
        $this->tester->click('div.ec-modal-box > div > a');

        return CartPage::at($this->tester);
    }
}
