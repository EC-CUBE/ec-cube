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

namespace Eccube\Page\Bloc;

use Eccube\Application;
use Eccube\Framework\Customer;
use Eccube\Framework\Cookie;
use Eccube\Framework\Db\MasterData;
use Eccube\Framework\Helper\SessionHelper;

/**
 * ログイン のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Login extends AbstractBloc
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_login = false;
        $this->tpl_disable_logout = false;
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        // クッキー管理クラス
        /* @var $objCookie Cookie */
        $objCookie = Application::alias('eccube.cookie');

        // ログイン判定
        if ($objCustomer->isLoginSuccess()) {
            $this->tpl_login = true;
            $this->tpl_user_point = $objCustomer->getValue('point');
            $this->tpl_name1 = $objCustomer->getValue('name01');
            $this->tpl_name2 = $objCustomer->getValue('name02');
        } else {
            // クッキー判定
            $this->tpl_login_email = $objCookie->getCookie('login_email');
            if ($this->tpl_login_email != '') {
                $this->tpl_login_memory = '1';
            }
            // POSTされてきたIDがある場合は優先する。
            if (isset($_POST['login_email']) && $_POST['login_email'] != '') {
                $this->tpl_login_email = $_POST['login_email'];
            }
        }

        $this->tpl_disable_logout = $this->lfCheckDisableLogout();
        //スマートフォン版ログアウト処理で不正なページ移動エラーを防ぐ為、トークンをセット
        $this->transactionid = SessionHelper::getToken();
    }

    /**
     * lfCheckDisableLogout.
     *
     * @return boolean
     */
    public function lfCheckDisableLogout()
    {
        $masterData = Application::alias('eccube.db.master_data');
        $arrDisableLogout = $masterData->getMasterData('mtb_disable_logout');

        $current_page = $_SERVER['SCRIPT_NAME'];

        foreach ($arrDisableLogout as $val) {
            if ($current_page == $val) {
                return true;
            }
        }

        return false;
    }
}
