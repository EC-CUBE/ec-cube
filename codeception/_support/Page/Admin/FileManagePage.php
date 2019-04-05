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

class FileManagePage extends AbstractAdminPageStyleGuide
{
    /**
     * FileManagePage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/content/file_manager', 'ファイル管理コンテンツ管理');
    }

    public function 入力_ファイル($fileName)
    {
        $this->tester->attachFile(['id' => 'form_file'], $fileName);

        return $this;
    }

    public function アップロード()
    {
        $this->tester->click('#upload_box__file a.action-upload');

        return $this;
    }

    public function 入力_フォルダ名($value)
    {
        $this->tester->fillField(['id' => 'form_create_file'], $value);

        return $this;
    }

    public function フォルダ作成()
    {
        $this->tester->click('#form1 a.action-create');

        return $this;
    }

    public function ファイル名($rowNum)
    {
        return "#fileList table > tbody > tr:nth-child(${rowNum}) > td:nth-child(2)";
    }

    public function 一覧_ダウンロード($rowNum)
    {
        $this->tester->click("#fileList table > tbody > tr:nth-child(${rowNum}) > td:nth-child(5) a.action-download");

        return $this;
    }

    public function 一覧_表示($rowNum)
    {
        $this->tester->click("#fileList table > tbody > tr:nth-child(${rowNum}) > td:nth-child(5) a.action-view");

        return $this;
    }

    public function 一覧_パスをコピー($rowNum)
    {
        $this->tester->click("#fileList table > tbody > tr:nth-child(${rowNum}) > td:nth-child(5) a.action-copy");

        return $this;
    }

    public function 一覧_ファイル名_クリック($rowNum)
    {
        $this->tester->click("#fileList table > tbody > tr:nth-child(${rowNum}) > td:nth-child(2) a");

        return $this;
    }

    public function 一覧_削除($rowNum)
    {
        $this->tester->click("#fileList table > tbody > tr:nth-child(${rowNum}) > td:nth-child(5) a.action-delete");

        return $this;
    }

    public function 一覧_削除_accept($rowNum)
    {
        $this->tester->waitForElementVisible("#confirmModal-${rowNum} div.modal-footer a.btn-ec-delete");
        $this->tester->click("#confirmModal-${rowNum} div.modal-footer a.btn-ec-delete");

        return $this;
    }

    /**
     * パンくずのパスを取得
     *
     * @param $index int 1から始まる(1はuser_data)
     *
     * @return string
     */
    public function パンくず($index)
    {
        return "//*[@id=\"bread\"]/li[${index}]";
    }
}
