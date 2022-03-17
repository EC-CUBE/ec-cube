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

class PageEditPage extends AbstractAdminPageStyleGuide
{
    public static $登録完了メッセージ = ['xpath' => "//div[@class='alert alert-success alert-dismissible fade show m-3']"];

    /**
     * PageNewPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->atPage('ページ管理コンテンツ管理');
        $page->tester->see('ページ設定', '#content_page_form > div.c-contentsArea__cols > div > div > div:nth-child(1) > div.card-header > div > div.col-8 > span');

        return $page;
    }

    public function 入力_名称($value)
    {
        $this->tester->fillField(['id' => 'main_edit_name'], $value);

        return $this;
    }

    public function 入力_URL($value)
    {
        $this->tester->fillField(['id' => 'main_edit_url'], $value);

        return $this;
    }

    public function 入力_ファイル名($value)
    {
        $this->tester->fillField(['id' => 'main_edit_file_name'], $value);

        return $this;
    }

    public function 入力_内容($value)
    {
        $value = preg_replace('/\n/', '\n', $value);
        $value = preg_replace("/([^\\\])'/", "$1\\'", $value);
        $this->tester->executeJS("ace.edit('editor').setValue('$value')");

        return $this;
    }

    public function 入力_PC用レイアウト($layoutName)
    {
        $this->tester->selectOption(['id' => 'main_edit_PcLayout'], $layoutName);

        return $this;
    }

    public function 入力_メタ_robot($value)
    {
        $this->tester->fillField(['id' => 'main_edit_meta_robots'], $value);

        return $this;
    }

    public function 出力_内容()
    {
        return $this->tester->executeJS("return ace.edit('editor').getValue()");
    }

    public function 登録()
    {
        $this->tester->click(['xpath' => '//button/span[text()="登録"]']);
    }
}
