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

class LoginHistoryPage extends AbstractAdminPageStyleGuide
{
    public static $URL = '/setting/system/login_history';

    public static $検索条件 = ['id' => 'admin_search_login_history_multi'];
    public static $検索ボタン = '#search_form .c-outsideBlock__contents button';
    public static $詳細検索ボタン = '//*[@id="search_form"]/div[1]/div[1]/div/div/div[2]/a/span';
    public static $検索結果_メッセージ = '//*[@id="search_form"]/div[2]/span';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);

        return $page->goPage(self::$URL, 'ログイン履歴システム設定');
    }

    /**
     * 指定したログインID/IPアドレスで検索する。
     *
     * @param string $multi ログインID/IPアドレス
     *
     * @return $this
     */
    public function 検索($multi = '')
    {
        $this->tester->fillField(self::$検索条件, $multi);
        $this->tester->click(self::$検索ボタン);
        $this->tester->see('ログイン履歴システム設定', '.c-pageTitle');

        return $this;
    }

    public function 詳細検索_ステータス($value)
    {
        $this->tester->click(self::$詳細検索ボタン);
        $this->tester->wait(1);
        $this->tester->checkOption(['id' => 'admin_search_login_history_Status_'.$value]);
        $this->tester->click(self::$検索ボタン);
        $this->tester->see('ログイン履歴システム設定', '.c-pageTitle');

        return $this;
    }
}
