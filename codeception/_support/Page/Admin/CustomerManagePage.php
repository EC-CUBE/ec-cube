<?php

namespace Page\Admin;


class CustomerManagePage extends AbstractAdminPageStyleGuide
{
    public static $URL = '/customer';

    public static $検索ボタン = '#search_form .c-outsideBlock__contents button';
    public static $詳細検索ボタン = '//*[@id="search_form"]/div[1]/div[1]/div/div/div[2]/a/span';
    public static $検索結果メッセージ = '#search_form > div.c-outsideBlock__contents.mb-5 > span';
    public static $検索結果_結果なしメッセージ = '.c-contentsArea .c-contentsArea__cols div.text-center.h5';
    public static $検索結果_エラーメッセージ = '.c-contentsArea .c-contentsArea__cols div.text-center.h5';
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
        return $page->goPage(self::$URL, '会員一覧会員管理');
    }

    public function 検索($value = '')
    {
        $this->tester->fillField(['id' => 'admin_search_customer_multi'], $value);
        $this->tester->click(self::$検索ボタン);
        $this->tester->see('会員一覧会員管理', '.c-pageTitle');
        return $this;
    }

    public function 詳細検索_電話番号($value = '')
    {
        $this->tester->click(self::$詳細検索ボタン);
        $this->tester->wait(1);
        $this->tester->fillField(['id' => 'admin_search_customer_phone_number'], $value);
        $this->tester->click(self::$検索ボタン);
        $this->tester->see('会員一覧会員管理', '.c-pageTitle');
        return $this;
    }

    /**
     * @param integer $rowNum
     */
    public function 一覧_編集($rowNum)
    {
        $this->tester->click("#search_form > div.c-contentsArea__cols > div > div > div.card.rounded.border-0.mb-4 > div > table > tbody > tr:nth-child(${rowNum}) > td:nth-child(2) > a");
        return $this;
    }

    /**
     * @param integer $rowNum
     */
    public function 一覧_削除($rowNum, $execute = true)
    {
        $this->tester->click("#search_form > div.c-contentsArea__cols > div > div > div.card.rounded.border-0.mb-4 > div > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3 > div > div > a");
        $this->tester->waitForElementVisible("#search_form > div.c-contentsArea__cols > div > div > div.card.rounded.border-0.mb-4 > div > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3 > div > div > div.modal");
        $this->tester->wait(1);
        if ($execute) {
            $this->tester->click("#search_form > div.c-contentsArea__cols > div > div > div.card.rounded.border-0.mb-4 > div > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3 > div > div > div.modal a.btn-ec-delete");
        } else {
            $this->tester->click("#search_form > div.c-contentsArea__cols > div > div > div.card.rounded.border-0.mb-4 > div > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3 > div > div > div.modal button.btn-ec-sub");
        }
        return $this;
    }

    /**
     * @param integer $rowNum
     */
    public function 一覧_仮会員メール再送($rowNum)
    {
        $this->tester->click(['xpath' => "//*[@id='search_form']//div/table/tbody/tr[${rowNum}]/td[6]/div/div[1]/a"]);
        $this->tester->wait(5);
        $this->tester->click('再送');
        return $this;
    }

    private function 一覧_メニュー($rowNum)
    {
        $this->tester->click("#search_form > div.row > div > div > div.box-body > div.table_list > div > table > tbody > tr:nth-child(${rowNum}) > td.icon_edit > div > a");
        return $this;
    }

    public function CSVダウンロード()
    {
        $this->tester->click('#search_form > div.c-contentsArea__cols > div > div > div.row.justify-content-between.mb-2 > div.col-5.text-right > div:nth-child(2) > div > a:nth-child(1)');
        return $this;
    }

    public function CSV出力項目設定()
    {
        $this->tester->click('#search_form > div.c-contentsArea__cols > div > div > div.row.justify-content-between.mb-2 > div.col-5.text-right > div:nth-child(2) > div > a:nth-child(2)');
    }

    /**
     * @param integer $rowNum
     */
    public function 一覧_会員ID($rowNum)
    {
        return $this->tester->grabTextFrom("#search_form > div.c-contentsArea__cols > div > div > div.card.rounded.border-0.mb-4 > div > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pl-3");
    }
}
