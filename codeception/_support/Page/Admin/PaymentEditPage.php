<?php


namespace Page\Admin;


class PaymentEditPage extends AbstractAdminPageStyleGuide
{
    public static $登録完了メッセージ = '.c-container .c-contentsArea div.alert-success';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);
        return $page->atPage('支払方法設定店舗設定');
    }

    public function 入力_支払方法($value) {
        $this->tester->fillField(['id' => 'payment_register_method'], $value);
        return $this;
    }

    public function 入力_手数料($value) {
        $this->tester->fillField(['id' => 'payment_register_charge'], $value);
        return $this;
    }

    public function 入力_利用条件下限($value) {
        $this->tester->fillField(['id' => 'payment_register_rule_min'], $value);
        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#form1 > .c-conversionArea > .c-conversionArea__container button.btn-ec-conversion');
    }

}