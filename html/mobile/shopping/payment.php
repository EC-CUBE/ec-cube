<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 * モバイルサイト/支払い方法選択
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_EX_PATH . "page_extends/shopping/LC_Page_Shopping_Payment_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_Shopping_Payment_Ex();
$objPage->mobileInit();
$objPage->mobileProcess();
register_shutdown_function(array($objPage, "destroy"));
?>
