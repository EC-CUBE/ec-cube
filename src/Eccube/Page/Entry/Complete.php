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

namespace Eccube\Page\Entry;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\CartSession;
use Eccube\Framework\Response;

/**
 * 会員登録(完了) のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Complete extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
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
        // カートが空かどうかを確認する。
        /* @var $objCartSess CartSession */
        $objCartSess = Application::alias('eccube.cart_session');
        $arrCartKeys = $objCartSess->getKeys();
        $this->tpl_cart_empty = true;
        foreach ($arrCartKeys as $cart_key) {
            if (count($objCartSess->getCartList($cart_key)) > 0) {
                $this->tpl_cart_empty = false;
                break;
            }
        }

        // メインテンプレートを設定
        if (CUSTOMER_CONFIRM_MAIL == true) {
            // 仮会員登録完了
            $this->tpl_mainpage     = 'entry/complete.tpl';
        } else {
            // 本会員登録完了
            Application::alias('eccube.response')->sendRedirectFromUrlPath('regist/complete.php');
        }

    }
}
