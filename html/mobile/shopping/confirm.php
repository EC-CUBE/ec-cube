<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 * モバイルサイト/注文確認
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_EX_PATH . "page_extends/shopping/LC_Page_Shopping_Confirm_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_Shopping_Confirm_Ex();
register_shutdown_function(array($objPage, "destroy"));
$objPage->mobileInit();
$objPage->mobileProcess();
?>
