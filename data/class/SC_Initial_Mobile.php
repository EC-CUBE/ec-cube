<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "SC_Initial.php");

/**
 * モバイルアプリケーションの初期設定クラス.
 *
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Initial_Mobile extends SC_Initial {

    // {{{ cunstructor

    /**
     * コンストラクタ.
     */
    function SC_Initial_Mobile() {
        parent::SC_Initial();

        define('MOBILE_SITE', true);
    }
}
?>
