<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

$SC_VIEW_PHP_DIR = realpath(dirname(__FILE__));
require_once($SC_VIEW_PHP_DIR . "/../module/Smarty/libs/Smarty.class.php");
require_once($SC_VIEW_PHP_DIR . "/../include/php_ini.inc");
//require_once(CLASS_PATH . "util_extends/SC_Utils_Ex.php");

class SC_View {

    var $_smarty;
    var $objSiteInfo; // サイト情報

    // コンストラクタ
    function SC_View($siteinfo = true) {
        global $SC_VIEW_PHP_DIR;

        $this->_smarty = new Smarty;
        $this->_smarty->left_delimiter = '<!--{';
        $this->_smarty->right_delimiter = '}-->';
        $this->_smarty->register_modifier("sfDispDBDate", array("SC_Utils_Ex", "sfDispDBDate"));
        $this->_smarty->register_modifier("sfConvSendDateToDisp", array("SC_Utils_Ex", "sfConvSendDateToDisp"));
        $this->_smarty->register_modifier("sfConvSendWdayToDisp", array("SC_Utils_Ex", "sfConvSendWdayToDisp"));
        $this->_smarty->register_modifier("sfGetVal", array("SC_Utils_Ex", "sfGetVal"));
        $this->_smarty->register_modifier("sfSetErrorStyle", array("SC_Utils_Ex", "sfSetErrorStyle"));
        $this->_smarty->register_modifier("sfGetErrorColor", array("SC_Utils_Ex", "sfGetErrorColor"));
        $this->_smarty->register_modifier("sfTrim", array("SC_Utils_Ex", "sfTrim"));
        $this->_smarty->register_modifier("sfPreTax", array("SC_Utils_Ex", "sfPreTax"));
        $this->_smarty->register_modifier("sfPrePoint", array("SC_Utils_Ex", "sfPrePoint"));
        $this->_smarty->register_modifier("sfGetChecked",array("SC_Utils_Ex", "sfGetChecked"));
        $this->_smarty->register_modifier("sfTrimURL", array("SC_Utils_Ex", "sfTrimURL"));
        $this->_smarty->register_modifier("sfMultiply", array("SC_Utils_Ex", "sfMultiply"));
        $this->_smarty->register_modifier("sfPutBR", array("SC_Utils_Ex", "sfPutBR"));
        $this->_smarty->register_modifier("sfRmDupSlash", array("SC_Utils_Ex", "sfRmDupSlash"));
        $this->_smarty->register_modifier("sfCutString", array("SC_Utils_Ex", "sfCutString"));
        $this->_smarty->plugins_dir=array("plugins", $SC_VIEW_PHP_DIR . "/../smarty_extends");
        $this->_smarty->register_modifier("sf_mb_convert_encoding", array("SC_Utils_Ex", "sf_mb_convert_encoding"));
        $this->_smarty->register_modifier("sf_mktime", array("SC_Utils_Ex", "sf_mktime"));
        $this->_smarty->register_modifier("sf_date", array("SC_Utils_Ex", "sf_date"));
        $this->_smarty->register_modifier("str_replace", array("SC_Utils_Ex", "str_replace"));
//        $this->_smarty->register_modifier("sfPrintEbisTag", array("SC_Utils_Ex", "sfPrintEbisTag"));
//        $this->_smarty->register_modifier("sfPrintAffTag", array("SC_Utils_Ex", "sfPrintAffTag"));
        $this->_smarty->register_modifier("sfGetCategoryId", array("SC_Utils_Ex", "sfGetCategoryId"));
        $this->_smarty->register_function("sfIsHTTPS", array("SC_Utils_Ex", "sfIsHTTPS"));
        $this->_smarty->default_modifiers = array('script_escape');

        if(ADMIN_MODE == '1') {
            $this->time_start = time();
        }

        // サイト情報を取得する
        if($siteinfo) {
            if(!defined('LOAD_SITEINFO')) {
                $this->objSiteInfo = new SC_SiteInfo();
                $arrInfo['arrSiteInfo'] = $this->objSiteInfo->data;

                // 都道府県名を変換
                global $arrPref;
                $arrInfo['arrSiteInfo']['pref'] = $arrPref[$arrInfo['arrSiteInfo']['pref']];

                 // サイト情報を割り当てる
                foreach ($arrInfo as $key => $value){
                    $this->_smarty->assign($key, $value);
                }

                define('LOAD_SITEINFO', 1);
            }
        }
    }

    // テンプレートに値を割り当てる
    function assign($val1, $val2) {
        $this->_smarty->assign($val1, $val2);
    }

    // テンプレートの処理結果を取得
    function fetch($template) {
        return $this->_smarty->fetch($template);
    }

    // テンプレートの処理結果を表示
    function display($template, $no_error = false) {
        if(!$no_error) {
            global $GLOBAL_ERR;
            if(!defined('OUTPUT_ERR')) {
                print($GLOBAL_ERR);
                define('OUTPUT_ERR','ON');
            }
        }

        $this->_smarty->display($template);
        if(ADMIN_MODE == '1') {
            $time_end = time();
            $time = $time_end - $this->time_start;
            print("処理時間:" . $time . "秒");
        }
    }

      // オブジェクト内の変数をすべて割り当てる。
      function assignobj($obj) {
        $data = get_object_vars($obj);

        foreach ($data as $key => $value){
            $this->_smarty->assign($key, $value);
        }
      }

      // 連想配列内の変数をすべて割り当てる。
      function assignarray($array) {
          foreach ($array as $key => $val) {
              $this->_smarty->assign($key, $val);
          }
      }

    /* サイト初期設定 */
    function initpath() {
        global $SC_VIEW_PHP_DIR;

        $array['tpl_mainnavi'] = $SC_VIEW_PHP_DIR . '/../Smarty/templates/frontparts/mainnavi.tpl';

        $objDb = new SC_Helper_DB_Ex();
        $array['tpl_root_id'] = $objDb->sfGetRootId();
        $this->assignarray($array);
    }

    // デバッグ
    function debug($var = true){
        $this->_smarty->debugging = $var;
    }


}

class SC_AdminView extends SC_View{
    function SC_AdminView() {
        parent::SC_View(false);
        $this->_smarty->template_dir = TEMPLATE_ADMIN_DIR;
        $this->_smarty->compile_dir = COMPILE_ADMIN_DIR;
        $this->initpath();
    }

    function printr($data){
        print_r($data);
    }
}

class SC_SiteView extends SC_View{
    function SC_SiteView($cart = true) {
        parent::SC_View();

        $this->_smarty->template_dir = TEMPLATE_DIR;
        $this->_smarty->compile_dir = COMPILE_DIR;
        $this->initpath();

        // PHP5ではsessionをスタートする前にヘッダー情報を送信していると警告が出るため、先にセッションをスタートするように変更
        SC_Utils_Ex::sfDomainSessionStart();

        if($cart){
            $include_dir = realpath(dirname( __FILE__));
            require_once($include_dir . "/SC_CartSession.php");
            $objCartSess = new SC_CartSession();
            $objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
        }
    }
}

class SC_UserView extends SC_SiteView{
    function SC_UserView($template_dir, $compile_dir = COMPILE_DIR) {
        parent::SC_SiteView();
        $this->_smarty->template_dir = $template_dir;
        $this->_smarty->compile_dir = $compile_dir;
    }
}

class SC_InstallView extends SC_View{
    function SC_InstallView($template_dir, $compile_dir = COMPILE_DIR) {
        parent::SC_View(false);
        $this->_smarty->template_dir = $template_dir;
        $this->_smarty->compile_dir = $compile_dir;
    }
}

class SC_MobileView extends SC_SiteView {
    function SC_MobileView() {
        parent::SC_SiteView();
        $this->_smarty->template_dir = MOBILE_TEMPLATE_DIR;
        $this->_smarty->compile_dir = MOBILE_COMPILE_DIR;
    }
}

//function sfCutString($str, $len, $byte = true, $commadisp = true) {
//        if($byte) {
//            if(strlen($str) > ($len + 2)) {
//                $ret =substr($str, 0, $len);
//                $cut = substr($str, $len);
//            } else {
//                $ret = $str;
//                $commadisp = false;
//            }
//        } else {
//            if(mb_strlen($str) > ($len + 1)) {
//                $ret = mb_substr($str, 0, $len);
//                $cut = mb_substr($str, $len);
//            } else {
//                $ret = $str;
//                $commadisp = false;
//            }
//        }
//
//        // 絵文字タグの途中で分断されないようにする。
//        if (isset($cut)) {
//            // 分割位置より前の最後の [ 以降を取得する。
//            $head = strrchr($ret, '[');
//
//            // 分割位置より後の最初の ] 以前を取得する。
//            $tail_pos = strpos($cut, ']');
//            if ($tail_pos !== false) {
//                $tail = substr($cut, 0, $tail_pos + 1);
//            }
//
//            // 分割位置より前に [、後に ] が見つかった場合は、[ から ] までを
//            // 接続して絵文字タグ1個分になるかどうかをチェックする。
//            if ($head !== false && $tail_pos !== false) {
//                $subject = $head . $tail;
//                if (preg_match('/^\[emoji:e?\d+\]$/', $subject)) {
//                    // 絵文字タグが見つかったので削除する。
//                    $ret = substr($ret, 0, -strlen($head));
//                }
//            }
//        }
//
//        if($commadisp){
//            $ret = $ret . "...";
//        }
//        return $ret;
//    }
//
//function sfRmDupSlash($istr){
//        if(ereg("^http://", $istr)) {
//            $str = substr($istr, 7);
//            $head = "http://";
//        } else if(ereg("^https://", $istr)) {
//            $str = substr($istr, 8);
//            $head = "https://";
//        } else {
//            $str = $istr;
//        }
//        $str = ereg_replace("[/]+", "/", $str);
//        $ret = $head . $str;
//        return $ret;
//    }
?>
