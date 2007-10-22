<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * ブロック の基底クラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_FrontParts_Bloc extends LC_Page {

    /**
     * ブロックファイルに応じて tpl_mainpage を設定する
     *
     * @param string $bloc_file ブロックファイル名
     * @return void
     */
    function setTplMainpage($bloc_file) {
        if (is_file(USER_PATH . BLOC_DIR . $bloc_file)) {
            $this->tpl_mainpage = USER_PATH . BLOC_DIR . $bloc_file;
        } else {
            $this->tpl_mainpage = BLOC_PATH . $bloc_file;
        }
    }
}
?>
