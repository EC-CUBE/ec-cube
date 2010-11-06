<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
require_once(dirname(__FILE__) . '/../module/Net/UserAgent/Mobile.php');

/**
 * スマートフォンの情報を扱うクラス
 *
 */
class SC_SmartphoneUserAgent {

    /**
     * スマートフォンかどうかを判別する。
     *
     * @return boolean
     */
    function isSmartphone() {
        $objAgent =& Net_UserAgent_Mobile::singleton();
        if (Net_UserAgent_Mobile::isError($objAgent)) {
            return false;
        } else {
            return SC_SmartphoneUserAgent::isSmartPhone();
        }
    }

    /**
     * スマートフォンかどうかを判別する。
     *
     * @return boolean
     */
    function isNonSmartphone() {
        return !SC_SmartphoneUserAgent::isSmartphone();
    }

    /**
     * PC表示フラグの取得
     *
     * @return string
     */
    function getSmartphonePcFlag() {
        return SC_Session::GetSession('pc_disp');
    }

    /**
     * PC表示ON
     */
    function setPcDsiplayOn() {
        SC_Session::SetSession('pc_disp', true);
    }

    /**
     * PC表示OFF
     */
    function setPcDsiplayOff() {
        SC_Session::SetSession('pc_disp', false);
    }

    /**
     * スマートフォン端末の判定とリダイレクト
     *
     * @return void
     */
    function sfAutoRedirectSmartphoneSite() {
        // SPでない場合は、処理しない
        if (SC_SmartphoneUserAgent::isNonSmartphone()) return;
        // SPで、PC表示がtrueの場合は、処理しない
        if (SC_SmartphoneUserAgent::isSmartphone() && SC_SmartphoneUserAgent::getSphonePcFlag()) return;

        $url = SC_Utils_Ex::sfIsHTTPS()
            ? SPHONE_SSL_URL
            : SPHONE_SITE_URL
        ;

        $url .= (preg_match('|^' . URL_DIR . '(.*)$|', $_SERVER['REQUEST_URI'], $matches))
            ? $matches[1]
            : ''
        ;

        header("Location: ". SC_Utils_Ex::sfRmDupSlash($url));
        exit;
    }
}
?>
