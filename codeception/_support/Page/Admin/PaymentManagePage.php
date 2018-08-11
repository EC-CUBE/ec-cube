<?php


namespace Page\Admin;


class PaymentManagePage extends AbstractAdminPageStyleGuide
{

    public static $登録完了メッセージ = '.c-container .c-contentsArea div.alert-success';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);
        return $page->goPage('/setting/shop/payment', '支払方法設定基本情報設定');
    }

    public static function at($I)
    {
        $page = new self($I);
        return $page->atPage('支払方法設定基本情報設定');
    }

    public function 一覧_支払方法($rowNum)
    {
        $rowNum = $rowNum + 1;
        return ".c-contentsArea__primaryCol .c-primaryCol .card-body ul li:nth-child(${rowNum})";
    }

    public function 一覧_下に($rowNum)
    {
        $rowNum = $rowNum + 1;
        $this->tester->click(".c-contentsArea__primaryCol .list-group-flush .list-group-item:nth-child(${rowNum}) .justify-content-around a.action-down ");
        return $this;
    }

    public function 一覧_編集($rowNum)
    {
        $rowNum = $rowNum + 1;
        $this->tester->click(".c-contentsArea__primaryCol .list-group-flush .list-group-item:nth-child(${rowNum})> div > div:nth-child(2) a ");

    }

    public function 一覧_削除($rowNum)
    {
        $rowNum = $rowNum + 1;
        $this->tester->click(".c-contentsArea__primaryCol .list-group-flush .list-group-item:nth-child(${rowNum}) > div > div.col-3.text-right > div > a");

        // accept modal
        $this->tester->waitForElementVisible("#delete_modal");
        $this->tester->click("#delete_modal > div > div > div.modal-footer > a");
    }

    public function 新規入力()
    {
        $this->tester->click('.c-contentsArea__primaryCol  button.btn-ec-regular');
    }

    public function 一覧_上に($rowNum)
    {
        $rowNum = $rowNum + 1;
        $this->tester->click(".c-contentsArea__primaryCol .list-group-flush .list-group-item:nth-child(${rowNum}) .justify-content-around a.action-up ");
        return $this;
    }
}