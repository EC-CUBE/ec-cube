<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 * モバイルサイト/ご利用ガイド
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_EX_PATH . "page_extends/guide/LC_Page_Guide_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_Guide_Ex();
$objPage->mobileInit();
$objPage->mobileProcess();
$objPage->process();
?>
