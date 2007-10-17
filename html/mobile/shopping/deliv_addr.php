<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 * モバイルサイト/配送情報追加
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_EX_PATH . "page_extends/shopping/LC_Page_Shopping_DelivAddr_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_Shopping_DelivAddr_Ex();
$objPage->mobileInit();
$objPage->mobileProcess();
$objPage->process();
?>
