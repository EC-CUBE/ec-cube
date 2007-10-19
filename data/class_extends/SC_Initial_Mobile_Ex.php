<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "SC_Initial_Mobile.php");

/**
 * モバイルアプリケーションの初期設定クラス(拡張).
 *
 * SC_Initial_Mobile をカスタマイズする場合はこのクラスを編集する.
 *
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Initial_Mobile_Ex extends SC_Initial_Mobile {

    // {{{ constructor

    /**
     * コンストラクタ
     */
    function SC_Initial_Mobile_Ex() {
        parent::SC_Initial_Mobile();
    }
}
?>
