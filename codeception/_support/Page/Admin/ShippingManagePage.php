<?php

namespace Page\Admin;


class ShippingManagePage extends AbstractAdminPageStyleGuide
{
    public static $検索条件_受注ステータス = ['id' => 'admin_search_shipping_status'];
    public static $検索結果_メッセージ = '#search_form > div.c-outsideBlock__contents.mb-3 > span';

    /**
     * ShippingListPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        return $page->goPage('/shipping', '出荷マスター出荷管理');
    }

    public static function at(\AcceptanceTester $I)
    {
        $page = new self($I);
        return $page->atPage('出荷管理出荷マスター');
    }

    public function 検索($value = '')
    {
        $this->tester->fillField(['id' => 'admin_search_shipping_multi'], $value);
        $this->tester->click('#search_form > div.c-outsideBlock__contents.mb-3 > button');
        return $this;
    }

    public function 出荷CSVダウンロード実行()
    {
        $this->tester->click(['xpath' => '//*[@id="form_bulk"]/div[1]/div[2]/div[2]/div/a[1]']);
        return $this;
    }

    public function 出荷CSV出力項目設定()
    {
        $this->tester->click(['xpath' => '//*[@id="form_bulk"]/div[1]/div[2]/div[2]/div/a[2]']);
        return $this;
    }

    public function 一覧_編集($rowNum)
    {
        $this->tester->click(['xpath' => "//*[@id='form_bulk']/div[2]/div/table/tbody/tr[${rowNum}]/td[2]/a"]);
    }

    public function 一覧_削除()
    {
        $this->tester->waitForElementVisible(['xpath' => '//*[@id="btn_bulk"]/button[2]']);
        $this->tester->click(['xpath' => '//*[@id="btn_bulk"]/button[2]']);
        $this->tester->waitForElementVisible(['xpath' => '//*[@id="btn_bulk_delete"]']);
        $this->tester->click(['xpath' => '//*[@id="btn_bulk_delete"]']);
        return $this;
    }

    public function 一覧_削除キャンセル()
    {
        $this->tester->waitForElementVisible(['xpath' => '//*[@id="btn_bulk"]/button[1]']);
        $this->tester->click(['xpath' => '//*[@id="btn_bulk"]/button[1]']);
        return $this;
    }

    public function 一括発送済み更新()
    {
        $this->tester->waitForElementVisible(['xpath' => '//*[@id="btn_bulk"]/button[1]']);
        $this->tester->click(['xpath' => '//*[@id="btn_bulk"]/button[1]']);
        $this->tester->wait(3);
        $this->tester->click(['id' => 'bulkChange']);
        return $this;
    }

    public function 一覧_全選択()
    {
        $this->tester->checkOption(['id' => 'check-all']);
        return $this;
    }

    /**
     * TODO: Should remove this function due to new design does not have other dropdown menu
     */
    private function その他メニュー()
    {
        $this->tester->click('#dropmenu > a');
    }

    public function メール一括通知()
    {
        $this->tester->click('#form_bulk #btn_bulk_mail');
    }

    public function 一覧_チェックボックス($rowNum)
    {
        $this->tester->click(['xpath' => "//*[@id='form_bulk']/div[2]/div/table/tbody/tr[${rowNum}]/td[1]/input"]);
    }
}
