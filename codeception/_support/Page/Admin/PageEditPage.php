<?php


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
        $page->tester->see('ページ詳細編集', '#content_page_form > div.c-contentsArea__cols > div > div > div:nth-child(1) > div.card-header > div > div.col-8 > span');
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
        $value = preg_replace("/([^\\\])'/", "$1\\'", $value);
        $this->tester->executeJS("ace.edit('editor').setValue('$value')");
        return $this;
    }

    public function 入力_PC用レイアウト($layoutName)
    {
        $this->tester->selectOption(['id' => 'main_edit_PcLayout'], $layoutName);
        return $this;
    }

    public function 登録()
    {
        $this->tester->click(['xpath' => '//button[text()="登録"]']);
    }
}