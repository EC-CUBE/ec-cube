<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 * モバイルサイト/パスワード再発行
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_EX_PATH . "page_extends/forgot/LC_Page_Forgot_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_Forgot_Ex();
register_shutdown_function(array($objPage, "destroy"));
$objPage->mobileInit();
$objPage->mobileProcess();
?>
