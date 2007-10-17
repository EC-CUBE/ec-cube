<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 * モバイルサイト/News
 */

// {{{ requires
require_once(CLASS_EX_PATH . "page_extends/frontparts/bloc/LC_Page_FrontParts_Bloc_News_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_FrontParts_Bloc_News_Ex();
$objPage->mobileInit();
$objPage->mobileProcess();
$objPage->process();
?>
