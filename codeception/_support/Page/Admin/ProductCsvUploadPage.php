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

class ProductCsvUploadPage extends AbstractAdminPageStyleGuide
{
    public static $完了メッセージ = '#importCsvModal > div > div > div.modal-body.text-left > p';

    /**
     * ProductCsvUploadPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new ProductCsvUploadPage($I);

        return $page->goPage('/product/product_csv_upload', '商品CSV登録商品管理');
    }

    public static function at($I)
    {
        $page = new self($I);

        return $page->atPage('商品CSV登録商品管理');
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

    public function アップロードボタン有効化()
    {
        // $this->tester->attachFileでイベントが効かずボタンが有効化されないので、テストコードで有効化する.
        $this->tester->waitForJS('return $("#upload-button").prop("disabled", false);', 1);

        return $this;
    }

    public function モーダルを表示()
    {
        $this->tester->click(['id' => 'upload-button']);

        return $this;
    }

    public function CSVアップロード実行()
    {
        $this->tester->wait(1);
        $this->tester->click(['id' => 'importCsv']);

        return $this;
    }

    public function CSVアップロード確認()
    {
        $this->tester->wait(5);
        $this->tester->see('CSVファイルをアップロードしました', ProductCsvUploadPage::$完了メッセージ);

        return $this;
    }

    public function モーダルを閉じる()
    {
        $this->tester->click(['id' => 'importCsvDone']);

        return $this;
    }

    public function 雛形ダウンロード()
    {
        $this->tester->click('#download-button');

        return $this;
    }
}
