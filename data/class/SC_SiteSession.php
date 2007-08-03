<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/* カートセッション管理クラス */
class SC_SiteSession {
    /* コンストラクタ */
    function SC_SiteSession() {
        SC_Utils_Ex::sfDomainSessionStart();
        // 前ページでの登録成功判定を引き継ぐ
        $_SESSION['site']['pre_regist_success'] = $_SESSION['site']['regist_success'];
        $_SESSION['site']['regist_success'] = false;
        $_SESSION['site']['pre_page'] = $_SESSION['site']['now_page'];
        $_SESSION['site']['now_page'] = $_SERVER['PHP_SELF'];
    }

    /* 前ページが正当であるかの判定 */
    function isPrePage() {
        if($_SESSION['site']['pre_page'] != "" && $_SESSION['site']['now_page'] != "") {
            if($_SESSION['site']['pre_regist_success'] || $_SESSION['site']['pre_page'] == $_SESSION['site']['now_page']) {
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
        if(!isset($_SESSION['site']['uniqid']) || $_SESSION['site']['uniqid'] == "") {
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
        if($_POST['uniqid'] != "") {
            if($_POST['uniqid'] != $_SESSION['site']['uniqid']) {
                return false;
            }
        }
        return true;
    }

    /* ユニークIDの解除 */
    function unsetUniqId() {
        $_SESSION['site']['uniqid'] = "";
    }

    /* 登録成功を記録 */
    function setRegistFlag() {
        $_SESSION['site']['regist_success'] = true;
    }
}
?>
