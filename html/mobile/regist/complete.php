<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 * モバイルサイト/会員登録完了
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_EX_PATH . "page_extends/regist/LC_Page_Regist_Complete_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_Regist_Complete_Ex();
$objPage->mobileInit();
$objPage->mobileProcess();
register_shutdown_function(array($objPage, "destroy"));
?>
