<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 * モバイルサイト/カテゴリ
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_PATH . "page_extends/frontparts/bloc/LC_Page_FrontParts_Bloc_Category_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_FrontParts_Bloc_Category_Ex();
$objPage->mobileInit();
$objPage->mobileProcess();
register_shutdown_function(array($objPage, "destroy"));
?>
