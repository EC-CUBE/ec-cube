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

class CustomerAgreementSettingPage extends AbstractAdminPage
{
    public static $登録完了メッセージ = '#main .container-fluid div:nth-child(1) .alert-success';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/setting/shop/customer_agreement', 'ショップ設定利用規約管理');
    }

    public function 入力_会員規約($value)
    {
        $this->tester->fillField(['id' => 'customer_agreement_customer_agreement'], $value);

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#form1 #aside_column button');
    }
}
