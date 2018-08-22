<?php


namespace Page\Admin;


class NewsManagePage extends AbstractAdminPage
{

    public static $登録完了メッセージ = '#main .container-fluid div:nth-child(1) .alert-success';

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
        $this->tester->click('#main > div > div > div > div.row > div > a');
    }

    public function 一覧_編集($rowNum)
    {
        $this->一覧_メニュー($rowNum);
        $this->tester->click("#form1 > div > div > table > tbody > tr:nth-child(${rowNum}) > td.icon_edit > div > ul > li:nth-child(1) > a");
        return $this;
    }

    private function 一覧_メニュー($rowNum)
    {
        $this->tester->click("#form1 > div > div > table > tbody > tr:nth-child(${rowNum}) > td.icon_edit > div > a");
        return $this;
    }

    public function 一覧_タイトル($rowNum)
    {
        return $this->tester->grabTextFrom(['css' => "#form1 > div > div > table > tbody > tr:nth-child(${rowNum}) > td:nth-child(3)"]);
    }

    public function 一覧_下へ($rowNum)
    {
        $this->一覧_メニュー($rowNum);
        $this->tester->click("#form1 > div > div > table > tbody > tr:nth-child(${rowNum}) > td.icon_edit > div > ul > li:nth-child(3) > a");
        return $this;
    }

    public function 一覧_上へ($rowNum)
    {
        $this->一覧_メニュー($rowNum);
        $this->tester->click("#form1 > div > div > table > tbody > tr:nth-child(${rowNum}) > td.icon_edit > div > ul > li:nth-child(3) > a");
        return $this;
    }

    public function 一覧_削除($rowNum)
    {
        $this->一覧_メニュー($rowNum);
        $this->tester->click("#form1 > div > div > table > tbody > tr:nth-child(${rowNum}) > td.icon_edit > div > ul > li:nth-child(2) > a");
        return $this;
    }
}