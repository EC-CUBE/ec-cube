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

class NewsManagePage extends AbstractAdminPage
{
    public static $登録完了メッセージ = '.c-container .c-contentsArea .alert-success';

    /**
     * ContentsRegisterPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/content/news', '新着情報管理コンテンツ管理');
    }

    public static function at($I)
    {
        $page = new self($I);

        return $page->atPage('新着情報管理コンテンツ管理');
    }

    public function 新規登録()
    {
        $this->tester->click('#addNew');
    }

    public function 一覧_編集($rowNum)
    {
        $this->tester->click(".c-contentsArea .list-group > li:nth-child(${rowNum}) a[title=編集]");

        return $this;
    }

    public function 一覧_タイトル($rowNum)
    {
        return $this->tester->grabTextFrom(['css' => ".c-contentsArea .list-group li:nth-child(${rowNum}) a:nth-of-type(1)"]);
    }

    public function 一覧_下へ($rowNum)
    {
        $this->tester->click(" ul .list-group li:nth-child(${rowNum})
     div > div :nth-child(4) > a");

        return $this;
    }

    public function 一覧_削除($rowNum)
    {
        $this->tester->click(".c-contentsArea .list-group > li:nth-child(${rowNum}) [title=削除] a");

        return $this;
    }

    public function ポップアップを受け入れます($rowNum)
    {
        $this->tester->waitForElementVisible(['css' => '.modal.show']);
        $this->tester->click('.modal.show .btn-ec-delete');

        return $this;
    }
}
