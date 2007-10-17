<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 * モバイルサイト/注文処理
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_EX_PATH . "page_extends/shopping/LC_Page_Shopping_Deliv_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_Shopping_Deliv_Ex();
$objPage->mobileInit();
$objPage->mobileProcess();
$objPage->process();
?>
