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

    public function 入力_支払方法($value)
    {
        $this->tester->fillField(['id' => 'payment_register_method'], $value);

        return $this;
    }

    public function 入力_手数料($value)
    {
        $this->tester->fillField(['id' => 'payment_register_charge'], $value);

        return $this;
    }

    public function 入力_利用条件下限($value)
    {
        $this->tester->fillField(['id' => 'payment_register_rule_min'], $value);

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#form1 > .c-conversionArea > .c-conversionArea__container button.btn-ec-conversion');
    }
}
