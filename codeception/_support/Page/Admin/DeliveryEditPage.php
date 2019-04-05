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

class DeliveryEditPage extends AbstractAdminPageStyleGuide
{
    public static $登録完了メッセージ = '.c-container div.c-contentsArea > div.alert-success';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);

        return $page->atPage('配送方法設定店舗設定');
    }

    public function 入力_配送業者名($value)
    {
        $this->tester->fillField(['id' => 'delivery_name'], $value);

        return $this;
    }

    public function 入力_名称($value)
    {
        $this->tester->fillField(['id' => 'delivery_service_name'], $value);

        return $this;
    }

    public function 入力_支払方法選択($array)
    {
        foreach ($array as $id) {
            $this->tester->checkOption(['id' => "delivery_payments_${id}"]);
        }

        return $this;
    }

    public function 入力_全国一律送料($value)
    {
        $this->tester->fillField(['id' => 'delivery_free_all'], $value);
        $this->tester->click('#set_fee_all');

        return $this;
    }

    public function 登録()
    {
        $this->tester->click(['xpath' => '//button/span[text()="登録"]']);

        return $this;
    }
}
