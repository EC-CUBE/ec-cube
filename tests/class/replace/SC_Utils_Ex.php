<?php
require_once(realpath(dirname(__FILE__)) . "/../../../data/class/util/SC_Utils.php");

/**
 * テスト用にexitしないSC_Utilsクラスです
 */
class SC_Utils_Ex extends SC_Utils
{
    public static function sfDispError($type) {
        return false;
    }

    public static function sfDispSiteError($type, $objSiteSess = '', $return_top = false, $err_msg = '') {
        return false;
    }
}
