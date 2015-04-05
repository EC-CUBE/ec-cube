<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Page\Mypage;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Cookie;
use Eccube\Framework\Customer;
use Eccube\Framework\Display;

/**
 * Mypage の基底クラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
abstract class AbstractMypage extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        // mypage 共通
        $this->tpl_title        = 'MYページ';
        $this->tpl_navi         = 'mypage/navi.tpl';
        $this->tpl_mainno       = 'mypage';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
        // ログインチェック
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');

        // ログインしていない場合は必ずログインページを表示する
        if ($objCustomer->isLoginSuccess(true) === false) {
            // クッキー管理クラス
            /* @var $objCookie Cookie */
            $objCookie = Application::alias('eccube.cookie');
            // クッキー判定(メールアドレスをクッキーに保存しているか）
            $this->tpl_login_email = $objCookie->getCookie('login_email');
            if ($this->tpl_login_email != '') {
                $this->tpl_login_memory = '1';
            }

            // POSTされてきたIDがある場合は優先する。
            if (isset($_POST['login_email'])
                && $_POST['login_email'] != ''
            ) {
                $this->tpl_login_email = $_POST['login_email'];
            }

            // 携帯端末IDが一致する会員が存在するかどうかをチェックする。
            if (Application::alias('eccube.display')->detectDevice() === DEVICE_TYPE_MOBILE) {
                $this->tpl_valid_phone_id = $objCustomer->checkMobilePhoneId();
            }
            $this->tpl_title        = 'MYページ(ログイン)';
            $this->tpl_mainpage     = 'mypage/login.tpl';
        } else {
            //マイページ会員情報表示用共通処理
            $this->tpl_login     = true;
            $this->CustomerName1 = $objCustomer->getValue('name01');
            $this->CustomerName2 = $objCustomer->getValue('name02');
            $this->CustomerPoint = $objCustomer->getValue('point');
            $this->action();
        }

        $this->sendResponse();
    }
}
