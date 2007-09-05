<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/define.php");
if (!defined("CLASS_PATH")) {
    /** クラスパス */
    define("CLASS_PATH", $include_dir . HTML2DATA_DIR . "class/");
}
require_once($include_dir . HTML2DATA_DIR. "conf/conf.php");
require_once($include_dir . HTML2DATA_DIR . "include/module.inc");
require_once(CLASS_PATH . "util_extends/GC_Utils_Ex.php");
require_once(CLASS_PATH . "util_extends/SC_Utils_Ex.php");
require_once(CLASS_PATH . "db_extends/SC_DB_MasterData_Ex.php");
require_once(CLASS_PATH . "db_extends/SC_DB_DBFactory_Ex.php");
//require_once($include_dir . HTML2DATA_DIR . "lib/glib.php");
//require_once($include_dir . HTML2DATA_DIR . "lib/slib.php");
require_once(CLASS_PATH . "SC_View.php");
require_once(CLASS_PATH . "SC_DbConn.php");
require_once(CLASS_PATH . "SC_Session.php");
require_once(CLASS_PATH . "SC_Query.php");
require_once(CLASS_PATH . "SC_SelectSql.php");
require_once(CLASS_PATH . "SC_CheckError.php");
require_once(CLASS_PATH . "SC_PageNavi.php");
require_once(CLASS_PATH . "SC_Date.php");
require_once(CLASS_PATH . "SC_Image.php");
require_once(CLASS_PATH . "SC_UploadFile.php");
require_once(CLASS_PATH . "SC_SiteInfo.php");
require_once(CLASS_PATH . "GC_SendMail.php");
require_once(CLASS_PATH . "SC_FormParam.php");
require_once(CLASS_PATH . "SC_CartSession.php");
require_once(CLASS_PATH . "SC_SiteSession.php");
require_once(CLASS_PATH . "SC_CampaignSession.php");
require_once(CLASS_PATH . "SC_Customer.php");
require_once(CLASS_PATH . "SC_CustomerList.php");
require_once(CLASS_PATH . "SC_Cookie.php");
require_once(CLASS_PATH . "SC_Page.php");
require_once(CLASS_PATH . "SC_Pdf.php");
require_once(CLASS_PATH . "GC_MobileUserAgent.php");
require_once(CLASS_PATH . "GC_MobileEmoji.php");
require_once(CLASS_PATH . "helper_extends/SC_Helper_PageLayout_Ex.php");
require_once(CLASS_PATH . "helper_extends/SC_Helper_DB_Ex.php");
require_once(CLASS_PATH . "helper_extends/SC_Helper_Session_Ex.php");
require_once(CLASS_PATH . "helper_extends/SC_Helper_Mail_Ex.php");

// セッションハンドラ開始
//$objSession = new SC_Helper_Session_Ex();

// アップデートで取得したPHPを読み出す
SC_Utils_Ex::sfLoadUpdateModule();

// 携帯端末の場合は mobile 以下へリダイレクトする。
// TODO LC_Page::init() に移動可能か？
if (GC_MobileUserAgent::isMobile()) {
    if (preg_match('|^' . URL_DIR . '(.*)$|', $_SERVER['REQUEST_URI'], $matches)) {
        $path = $matches[1];
    } else {
        $path = '';
    }
    header("Location: " . URL_DIR . "mobile/$path");
    exit;
}

// 絵文字変換 (除去) フィルターを組み込む。
ob_start(array('GC_MobileEmoji', 'handler'));
?>
