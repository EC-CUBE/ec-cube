<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 */
$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/../define.php");
if (!defined("CLASS_PATH")) {
    /** クラスパス */
    define("CLASS_PATH", $include_dir . "/.." . HTML2DATA_DIR . "class/");
}

if (!defined("CLASS_EX_PATH")) {
    /** クラスパス */
    define("CLASS_EX_PATH", $include_dir . "/.." . HTML2DATA_DIR . "class_extends/");
}

if (!defined("CACHE_PATH")) {
    /** キャッシュ生成ディレクトリ */
    define("CACHE_PATH", $include_dir . "/.." . HTML2DATA_DIR . "cache/");
}
require_once(CLASS_EX_PATH . "SC_Initial_Mobile_Ex.php");
// アプリケーション初期化処理
$objInit = new SC_Initial_Mobile_Ex();
$objInit->init();

//require_once($include_dir . "/.." . HTML2DATA_DIR . "conf/conf.php");
//require_once($include_dir . "/.." . HTML2DATA_DIR . "conf/mobile_conf.php");
require_once($include_dir . "/.." . HTML2DATA_DIR . "include/module.inc");
require_once(CLASS_EX_PATH . "util_extends/GC_Utils_Ex.php");
require_once(CLASS_EX_PATH . "util_extends/SC_Utils_Ex.php");
require_once(CLASS_EX_PATH . "db_extends/SC_DB_MasterData_Ex.php");
require_once(CLASS_EX_PATH . "db_extends/SC_DB_DBFactory_Ex.php");
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
require_once(CLASS_EX_PATH . "SC_SendMail_Ex.php");
require_once(CLASS_PATH . "SC_FormParam.php");
require_once(CLASS_PATH . "SC_CartSession.php");
require_once(CLASS_PATH . "SC_SiteSession.php");
require_once(CLASS_PATH . "SC_Customer.php");
require_once(CLASS_PATH . "SC_Cookie.php");
require_once(CLASS_PATH . "SC_Pdf.php");
require_once(CLASS_PATH . "SC_MobileUserAgent.php");
require_once(CLASS_PATH . "SC_MobileEmoji.php");
require_once(CLASS_PATH . "SC_MobileImage.php");
require_once(CLASS_EX_PATH . "helper_extends/SC_Helper_PageLayout_Ex.php");
require_once(CLASS_EX_PATH . "helper_extends/SC_Helper_DB_Ex.php");
require_once(CLASS_EX_PATH . "helper_extends/SC_Helper_Mobile_Ex.php");
require_once(CLASS_EX_PATH . "helper_extends/SC_Helper_Session_Ex.php");
require_once(CLASS_EX_PATH . "helper_extends/SC_Helper_Mail_Ex.php");
include_once($include_dir . "/require_plugin.php");

// セッションハンドラ開始
$objSession = new SC_Helper_Session_Ex();

// アップデートで取得したPHPを読み出す
SC_Utils_Ex::sfLoadUpdateModule();

// モバイルサイト用の初期処理を実行する。
if (!defined('SKIP_MOBILE_INIT')) {
    $objMobile = new SC_Helper_Mobile_Ex();
    $objMobile->sfMobileInit();
}
?>
