<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

/* カートセッション管理クラス */
class SC_SiteSession {
    /* コンストラクタ */
    function __construct() {
        // 前ページでの登録成功判定を引き継ぐ
        $_SESSION['site']['pre_regist_success'] =
                isset($_SESSION['site']['regist_success'])
                    ? $_SESSION['site']['regist_success'] : '';

        $_SESSION['site']['regist_success'] = false;
        $_SESSION['site']['pre_page'] =
                isset($_SESSION['site']['now_page'])
                    ? $_SESSION['site']['now_page'] : '';

        $_SESSION['site']['now_page'] = $_SERVER['SCRIPT_NAME'];
    }

    /* 前ページが正当であるかの判定 */
    function isPrePage() {
        if ($_SESSION['site']['pre_page'] != '' && $_SESSION['site']['now_page'] != '') {
            if ($_SESSION['site']['pre_regist_success'] || $_SESSION['site']['pre_page'] == $_SESSION['site']['now_page']) {
                return true;
            }
        }
        return false;
    }

    function setNowPage($path) {
        $_SESSION['site']['now_page'] = $path;
    }

    /* 値の取得 */
    function getValue($keyname) {
        return $_SESSION['site'][$keyname];
    }

    /* ユニークIDの取得 */
    function getUniqId() {
        // ユニークIDがセットされていない場合はセットする。
        if (!isset($_SESSION['site']['uniqid']) || $_SESSION['site']['uniqid'] == '') {
            $this->setUniqId();
        }
        return $_SESSION['site']['uniqid'];
    }

    /* ユニークIDのセット */
    function setUniqId() {
        // 予測されないようにランダム文字列を付与する。
        $_SESSION['site']['uniqid'] = SC_Utils_Ex::sfGetUniqRandomId();
    }

    /* ユニークIDのチェック */
    function checkUniqId() {
        if (!empty($_POST['uniqid'])) {
            if ($_POST['uniqid'] != $_SESSION['site']['uniqid']) {
                return false;
            }
        }
        return true;
    }

    /* ユニークIDの解除 */
    function unsetUniqId() {
        $_SESSION['site']['uniqid'] = '';
    }

    /* 登録成功を記録 */
    function setRegistFlag() {
        $_SESSION['site']['regist_success'] = true;
    }
}
