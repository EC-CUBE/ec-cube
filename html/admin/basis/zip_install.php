<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_PATH . "page_extends/admin/basis/LC_Page_Admin_Basis_ZipInstall_Ex.php");

ini_set("max_execution_time", 600);

// }}}
// {{{ generate page

$objPage = new LC_Page_Admin_Basis_ZipInstall_Ex();
$objPage->init();
$objPage->process();
register_shutdown_function(array($objPage, "destroy"));
?>
