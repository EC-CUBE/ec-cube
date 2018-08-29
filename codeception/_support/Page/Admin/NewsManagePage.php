<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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

        return $page->goPage('/content/news', 'コンテンツ管理新着情報管理');
    }

    public static function at($I)
    {
        $page = new self($I);

        return $page->atPage('コンテンツ管理新着情報管理');
    }

    public function 新規登録()
    {
        $this->tester->click('.c-contentsArea .c-contentsArea__cols .c-contentsArea__primaryCol .justify-content-between #addNew
        ');
    }

    public function 一覧_編集($rowNum)
    {
        $this->tester->click(" ul .list-group li:nth-child(${rowNum})
     div > div :nth-child(4) > a");

        return $this;
    }

    public function 一覧_タイトル($rowNum)
    {
        return $this->tester->grabTextFrom(['css' => "ul.list-group li:nth-child(${rowNum}) div > div:nth-child(4) a"]);
    }

    public function 一覧_下へ($rowNum)
    {
        $this->tester->click(" ul .list-group li:nth-child(${rowNum})
     div > div :nth-child(4) > a");

        return $this;
    }

    public function 一覧_削除($rowNum)
    {
        $this->tester->click("ul.list-group li:nth-child(${rowNum}) div > div:nth-child(5) > div > div:nth-child(3) > a.btn-ec-actionIcon");

        return $this;
    }

    public function ポップアップを受け入れます($rowNum)
    {
        $modal = "ul.list-group li:nth-child(${rowNum}) div > div:nth-child(5) > div > div:nth-child(3) div.modal";
        $this->tester->waitForElementVisible(['css' => $modal]);
        $this->tester->click($modal.' .modal-footer a.btn-ec-delete');

        return $this;
    }
}
