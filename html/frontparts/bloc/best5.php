<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "page_extends/frontparts/bloc/LC_Page_FrontParts_Bloc_Best5_Ex.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_FrontParts_BLoc_Best5_Ex();
$objPage->init();
$objPage->process();
register_shutdown_function(array($objPage, "destroy"));
?>
