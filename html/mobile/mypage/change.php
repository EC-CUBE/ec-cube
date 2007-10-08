<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 * モバイルサイト/Myページ登録情報変更
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_EX_PATH . "page_extends/mypage/LC_Page_Mypage_Change_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_Mypage_Change_Ex();
$objPage->mobileInit();
$objPage->mobileProcess();
register_shutdown_function(array($objPage, "destroy"));
?>
