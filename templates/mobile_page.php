<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 * モバイルサイト/XXX
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_PATH . "page_extends/LC_Page_XXX_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_XXX_Ex();
$objPage->mobileInit();
$objPage->mobileProcess();
register_shutdown_function(array($objPage, "destroy"));
?>
