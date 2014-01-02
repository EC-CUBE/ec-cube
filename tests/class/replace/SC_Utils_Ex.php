<?php
require_once(realpath(dirname(__FILE__)) . "/../../../data/class/util/SC_Utils.php");

/**
 * テスト用にexitしないSC_Utilsクラスです
 */
class SC_Utils_Ex extends SC_Utils
{
    public function sfDispError($type) {
        return false;
    }

    public function sfDispSiteError($type, $objSiteSess = '', $return_top = false, $err_msg = '') {
        return false;
    }
}
