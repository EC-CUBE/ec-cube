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

class CategoryCsvUploadPage extends AbstractAdminPageStyleGuide
{
    public static $完了メッセージ = '.c-container div.c-contentsArea > div.alert-success';

    /**
     * CategoryCsvUploadPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/product/category_csv_upload', 'カテゴリCSV登録商品管理');
    }

    public function 入力_CSVファイル($fileName)
    {
        $this->tester->attachFile(['id' => 'admin_csv_import_import_file'], $fileName);

        return $this;
    }

    public function CSVアップロード()
    {
        $this->tester->click(['id' => 'upload-button']);

        return $this;
    }

    public function 雛形ダウンロード()
    {
        $this->tester->click('#download-button');

        return $this;
    }
}
