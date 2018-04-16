<?php

namespace Page\Admin;


class OrderManagePage extends AbstractAdminPageStyleGuide
{
    public static $検索条件_受注ステータス = ['id' => 'admin_search_order_status'];
    public static $検索結果_メッセージ = '#search_form #search_total_count';

    /**
     * OrderListPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        return $page->goPage('/order', '受注マスター受注管理');
    }

    public static function at(\AcceptanceTester $I)
    {
        $page = new self($I);
        return $page->atPage('受注マスター受注管理');
    }

    public function 検索($value = '')
    {
        $this->tester->fillField(['id' => 'admin_search_order_multi'], $value);
        $this->tester->click('#search_form #search_submit');
        return $this;
    }

    public function 受注CSVダウンロード実行()
    {
        $this->tester->click('#form_bulk #btn_csv_download');
        return $this;
    }

    public function 受注CSV出力項目設定()
    {
        $this->tester->click('#form_bulk #btn_csv_setting');
        return $this;
    }

    public function 一覧_編集($rowNum)
    {
        $this->tester->click("#search_result > tbody > tr:nth-child(${rowNum}) a.action-edit");
    }

    public function 一覧_削除()
    {
        $this->tester->click("#form_bulk > div.row.justify-content-between.mb-2 .btn-bulk-wrapper button.btn.btn-ec-delete");
        return $this;
    }

    public function Accept_削除()
    {
        $this->tester->waitForElementVisible(['id' => 'btn_bulk_delete']);
        $this->tester->click("#btn_bulk_delete");
        return $this;
    }

    public function Cancel_削除()
    {
        $this->tester->click("#bulkDeleteModal > div > div > div.modal-footer > button.btn.btn-ec-sub");
        return $this;
    }

    public function 一覧_メール通知($rowNum)
    {
        $this->tester->click("#search_result > tbody > tr:nth-child(${rowNum}) a.action-mail");
        return $this;
    }

    public function 一覧_選択($rowNum)
    {
        $targetOrderId = $this->一覧_注文番号($rowNum);
        $this->tester->checkOption(['id' => 'check_'.$targetOrderId]);
        return $this;
    }

    public function 一覧_全選択()
    {
        $this->tester->checkOption('#check_all');
        return $this;
    }


    public function メール一括通知()
    {
        $this->tester->click('#form_bulk #btn_bulk_mail');
    }

    public function 一覧_注文番号($rowNum)
    {
        return $this->tester->grabTextFrom("#search_result > tbody > tr:nth-child($rowNum) a.action-edit");
    }

    public function 受注ステータス検索($value = '')
    {
        $this->tester->checkOption(['id' => 'admin_search_order_multi_status_' . $value]);
        $this->tester->click('#search_form #search_submit');
        return $this;
    }

    public function 受注ステータス変更($option = [])
    {
        $this->tester->selectOption('#option_bulk_status', $option);
        $this->tester->click('#form_bulk #btn_bulk_status');
        $this->tester->waitForElementVisible('#confirmBulkModal', 5);
        $this->tester->click('#confirmBulkModal button[data-action="execute"]');
        return $this;
    }
}