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
     * $_SESSION['pc_disp'] = true の場合はPC表示。
     *
     * @return boolean
     */
    function isSmartphone() {
        $nu = new Net_UserAgent_Mobile();
        // SPでかつPC表示OFFの場合
        return $nu->isSmartphone() && !SC_SmartphoneUserAgent::getSmartphonePcFlag();
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
        $_SESSION['pc_disp'] = empty($_SESSION['pc_disp']) ? false : $_SESSION['pc_disp'];
        return $_SESSION['pc_disp'];
    }

    /**
     * PC表示ON
     */
    function setPcDsiplayOn() {
        $_SESSION['pc_disp'] = true;
    }

    /**
     * PC表示OFF
     */
    function setPcDsiplayOff() {
        $_SESSION['pc_disp'] = false;
    }

    /**
     * スマートフォン端末の判定とリダイレクト
     *
     * @return void
     */
    function sfAutoRedirectSmartphoneSite() {
        // SPでない場合は、処理しない
        if (SC_SmartphoneUserAgent::isNonSmartphone()) return;


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
