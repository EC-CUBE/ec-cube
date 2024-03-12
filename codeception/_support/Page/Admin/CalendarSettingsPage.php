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

class CalendarSettingsPage extends AbstractAdminPageStyleGuide
{
    public static $登録完了メッセージ = '#page_admin_setting_shop_calendar .alert-success';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/setting/shop/calendar', '定休日カレンダー設定店舗設定');
    }

    public function 入力_タイトル($value)
    {
        $this->tester->fillField(['id' => 'calendar_title'], $value);

        return $this;
    }

    public function 入力_日付($value)
    {
        $this->tester->executeJS("$('#calendar_holiday').val('{$value}');");
        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#calendar_item_new button');

        return $this;
    }
}
