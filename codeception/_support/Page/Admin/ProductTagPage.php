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

/**
 * 商品管理/タグ管理
 */
class ProductTagPage extends AbstractAdminPageStyleGuide
{
    public static $URL = '/product/tag';

    public static $アラートメッセージ = ['css' => '.c-contentsArea > .alert' ];
    public static $タグ一覧 = ['css' => '.c-primaryCol .list-group' ];

    /** @var \AcceptanceTester */
    protected $tester;

    /**
     * ProductTagPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go(\AcceptanceTester $I)
    {
        $page = new ProductTagPage($I);
        return $page->goPage(self::$URL, 'タグ管理商品管理');
    }

    public function 入力_タグ名($value)
    {
        $this->tester->fillField(['id' => 'admin_product_tag_name'], $value);
        return $this;
    }

    public function 新規作成()
    {
        $this->tester->click(['css' => '.c-primaryCol form button']);
        return $this;
    }

    public function タグ編集_開始($row)
    {
        $row = $row + 2;
        $this->tester->click(['css' => ".c-primaryCol .list-group > li:nth-child({$row}) a[data-original-title=編集]"]);
        $this->tester->waitForElementVisible(['css' => ".c-primaryCol .list-group > li:nth-child({$row}) form"]);
        return $this;
    }

    public function タグ編集_入力($row, $value)
    {
        $row = $row + 2;
        $this->tester->fillField(['css' => ".c-primaryCol .list-group > li:nth-child({$row}) input[type=text]"], $value);
        return $this;
    }

    public function タグ編集_決定($row)
    {
        $row = $row + 2;
        $this->tester->click(['css' => ".c-primaryCol .list-group > li:nth-child({$row}) .btn-ec-conversion"]);
        $this->tester->waitForElementNotVisible(['css' => ".c-primaryCol .list-group > li:nth-child({$row}) form"]);
        return $this;
    }

    public function タグ削除($row)
    {
        $row = $row + 2;
        $this->tester->click(['css' => ".c-primaryCol .list-group > li:nth-child({$row}) a[data-target='#DeleteModal']"]);
        $this->tester->waitForElementVisible(['id' => 'DeleteModal']);
        $this->tester->wait(1);
        return $this;
    }

    public function タグ削除_決定()
    {
        $this->tester->click(['css' => '.modal.show .btn-ec-delete']);
        $this->tester->waitForElementNotVisible(['id' => 'DeleteModal']);
        return $this;
    }
}
