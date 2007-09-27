<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_PATH . "page_extends/mypage/LC_Page_Mypage_ChangeComplate_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_Mypage_ChangeComplate_Ex();
$objPage->init();
$objPage->process();
register_shutdown_function(array($objPage, "destroy"));
?>
