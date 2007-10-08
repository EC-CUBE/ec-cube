<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "helper/SC_Helper_Session.php");

/**
 * セッション関連のヘルパークラス(拡張).
 *
 * SC_Helper_Session_Ex をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Helper_Session_Ex extends SC_Helper_Session {

    // }}}
    // {{{ constructors

    /**
     * デフォルトコンストラクタ.
     */
    function SC_Helper_Session_Ex() {
        parent::SC_Heler_Session();
    }
}
?>
