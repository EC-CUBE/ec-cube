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

class DeliveryManagePage extends AbstractAdminPageStyleGuide
{
    public static $登録完了メッセージ = '.c-container div.c-contentsArea > div.alert-success';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);
        $page->goPage('/setting/shop/delivery', '配送方法一覧店舗設定');

        return $page;
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->atPage('配送方法一覧店舗設定');

        return $page;
    }

    public function 一覧_編集($rowNum)
    {
        $this->tester->click("#page_admin_setting_shop_delivery > div > div.c-contentsArea > form > div > div > div.c-primaryCol > div > div > div > ul > li:nth-child($rowNum) > div > div.col.d-flex.align-items-center > a");

        return $this;
    }

    public function 一覧_削除($rowNum)
    {
        $this->tester->click("#page_admin_setting_shop_delivery > div > div.c-contentsArea > form > div > div > div.c-primaryCol > div > div > div > ul > li:nth-child($rowNum) > div > div.col-auto.text-right > div > a");

        // accept modal
        $this->tester->waitForElementVisible('#DeleteModal');
        $this->tester->wait(1);
        $this->tester->click('#DeleteModal > div > div > div.modal-footer > a');

        return $this;
    }

    public function 新規登録()
    {
        $this->tester->click('#page_admin_setting_shop_delivery > div.c-container > div.c-contentsArea > form > div > div > div > div.d-block.mb-3 > a');
    }

    public function 一覧_名称($rowNum)
    {
        return ['css' => "div.c-primaryCol ul > li:nth-child($rowNum) > div > div.col.d-flex.align-items-center > a"];
    }

    public function 一覧_上に($rowNum)
    {
        $this->tester->dragAndDropBy("div.c-primaryCol ul > li:nth-child($rowNum) > div", 0, -60);

        return $this;
    }

    public function 一覧_下に($rowNum)
    {
        $this->tester->dragAndDropBy("div.c-primaryCol ul > li:nth-child($rowNum) > div", 0, 60);

        return $this;
    }

    public function 一覧_件数取得()
    {
        return count($this->tester->grabMultiple('.c-contentsArea__primaryCol li.sortable-item'));
    }
}
