<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
 
$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/define.php");
require_once($include_dir . HTML2DATA_DIR. "conf/conf.php");
require_once($include_dir . HTML2DATA_DIR . "include/module.inc");
require_once($include_dir . HTML2DATA_DIR . "lib/glib.php");
require_once($include_dir . HTML2DATA_DIR . "lib/slib.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_View.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_DbConn.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_Session.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_Query.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_SelectSql.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_CheckError.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_PageNavi.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_Date.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_Image.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_UploadFile.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_SiteInfo.php");
require_once($include_dir . HTML2DATA_DIR . "class/GC_SendMail.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_FormParam.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_CartSession.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_SiteSession.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_CampaignSession.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_Customer.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_Cookie.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_Page.php");
require_once($include_dir . HTML2DATA_DIR . "class/SC_Pdf.php");
require_once($include_dir . HTML2DATA_DIR . "class/GC_MobileUserAgent.php");
require_once($include_dir . HTML2DATA_DIR . "class/GC_MobileEmoji.php");
require_once($include_dir . HTML2DATA_DIR . "include/page_layout.inc");

// アップデートで取得したPHPを読み出す
sfLoadUpdateModule();

// 携帯端末の場合は mobile 以下へリダイレクトする。
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
