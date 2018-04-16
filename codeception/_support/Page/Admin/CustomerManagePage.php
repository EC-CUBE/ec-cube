<?php

namespace Page\Admin;


class CustomerManagePage extends AbstractAdminPageStyleGuide
{
    public static $URL = '/customer';

    public static $検索ボタン = '#search_form .c-outsideBlock__contents button';
    public static $検索結果メッセージ = '#search_form > div.c-outsideBlock__contents.mb-5 > span';
    public static $検索結果_結果なしメッセージ = '.c-contentsArea .c-contentsArea__cols div.text-center.h5';
    public static $検索条件_仮会員 = ['id' => 'admin_search_customer_customer_status_0'];
    public static $検索条件_本会員 = ['id' => 'admin_search_customer_customer_status_1'];

    /**
     * CustomerListPage constructor.
     * @param $I
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        return $page->goPage(self::$URL, '会員マスター会員管理');
    }

    public function 検索($value = '')
    {
        $this->tester->fillField(['id' => 'admin_search_customer_multi'], $value);
        $this->tester->click(self::$検索ボタン);
        $this->tester->see('会員マスター会員管理', '.c-pageTitle');
        return $this;
    }

    public function 一覧_編集($rowNum)
    {
        $this->tester->click("#search_form > div.c-contentsArea__cols > div > div > div.card.rounded.border-0.mb-4 > div > table > tbody > tr:nth-child(${rowNum}) > td:nth-child(2) > a");
        return $this;
    }

    public function 一覧_削除($rowNum)
    {
        $this->一覧_メニュー($rowNum);
        $this->tester->click("#search_form > div.row > div > div > div.box-body > div.table_list > div > table > tbody > tr:nth-child(${rowNum}) > td.icon_edit > div > ul > li:nth-child(2) > a");
        return $this;
    }

    public function 一覧_仮会員メール再送($rowNum)
    {
        $this->一覧_メニュー($rowNum);
        $this->tester->click("#search_form > div.row > div > div > div.box-body > div.table_list > div > table > tbody > tr:nth-child(${rowNum}) > td.icon_edit > div > ul > li:nth-child(3) > a");
        return $this;
    }

    private function 一覧_メニュー($rowNum)
    {
        $this->tester->click("#search_form > div.row > div > div > div.box-body > div.table_list > div > table > tbody > tr:nth-child(${rowNum}) > td.icon_edit > div > a");
        return $this;
    }

    public function CSVダウンロード()
    {
        $this->tester->click('#search_form > div.c-contentsArea__cols > div > div > div.row.justify-content-between.mb-2 > div.col-5.text-right > div:nth-child(2) > div > button:nth-child(1)');
        return $this;
    }

    public function CSV出力項目設定()
    {
        $this->tester->click('#search_form > div.c-contentsArea__cols > div > div > div.row.justify-content-between.mb-2 > div.col-5.text-right > div:nth-child(2) > div > button:nth-child(2)');
    }

    public function 一覧_会員ID($rowNum)
    {
        return $this->tester->grabTextFrom("#search_form > div.row > div > div > div.box-body > div.table_list > div > table > tbody > tr:nth-child($rowNum) > td.member_id");
    }
}
