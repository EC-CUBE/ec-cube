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

namespace Page\Front;

class HistoryPage extends AbstractFrontPage
{
    public static $ポイント値引き額 = '//dt[contains(text(), "ポイント")]/../dd';
    public static $利用ポイント = '//dt[contains(text(), "ご利用ポイント")]/../dd';
    public static $加算ポイント = '//dt[contains(text(), "加算ポイント")]/../dd';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('ご注文履歴詳細', 'div.ec-pageHeader h1');

        return $page;
    }
}
