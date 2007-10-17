<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 * モバイルサイト/受注履歴詳細
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_EX_PATH . "page_extends/mypage/LC_Page_Mypage_HistoryDetail_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_Mypage_HistoryDetail_Ex();
$objPage->mobileInit();
$objPage->mobileProcess();
$objPage->process();
?>
