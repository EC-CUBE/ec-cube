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

class BlockEditPage extends AbstractAdminPageStyleGuide
{
    public static $登録完了メッセージ = 'body > div.c-container > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span';

    /**
     * BlockEditPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->atPage('ブロック管理コンテンツ管理');
        $page->tester->see('ブロック設定', '#content_block_form > div.c-contentsArea__cols > div > div > div > div.card-header > div > div.col-8 > span');

        return $page;
    }

    public function 入力_ブロック名($value)
    {
        $this->tester->fillField(['id' => 'block_name'], $value);

        return $this;
    }

    public function 入力_ファイル名($value)
    {
        $this->tester->fillField(['id' => 'block_file_name'], $value);

        return $this;
    }

    public function 入力_データ($value)
    {
        $value = preg_replace("/([^\\\])'/", "$1\\'", $value);
        $this->tester->executeJS("ace.edit('editor').setValue('$value')");

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#content_block_form > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');

        return $this;
    }
}
