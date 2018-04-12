<?php


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
        $this->tester->click('#aside_wrap > form > div.col-md-9 > div > div.box-header.form-horizontal > div.form-group.form-inline > div > a');
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
        $this->tester->click("#aside_wrap > form > div.col-md-9 > div > div.box-body > div > div > table > tbody > tr:nth-child(${rowNum}) > td:nth-child(4) > a");
        return $this;
    }

    public function 一覧_削除($rowNum)
    {
        $this->tester->click("#aside_wrap > form > div.col-md-9 > div > div.box-body > div > div > table > tbody > tr:nth-child(${rowNum}) > td:nth-child(6) > a");
        return $this;
    }

    /**
     * パンくずのパスを取得
     * @param $index int 0から始まる(0はuser_data)
     * @return string
     */
    public function パンくず($index)
    {
        $index = ($index * 2) + 1;
        return "#bread > a:nth-child(${index})";
    }
}