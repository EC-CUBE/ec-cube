<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "SC_Initial.php");

/**
 * アプリケーションの初期設定クラス(拡張).
 *
 * SC_Initial をカスタマイズする場合はこのクラスを編集する.
 *
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Initial_Ex extends SC_Initial {

    // {{{ constructor

    /**
     * コンストラクタ
     */
    function SC_Initial_Ex() {
        parent::SC_Initial();
    }
}
?>
