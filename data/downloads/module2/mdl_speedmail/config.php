<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(MODULE2_PATH . "mdl_speedmail/LC_Page_Mdl_SpeedMail.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_Mdl_SpeedMail();
$objPage->init();
$objPage->process();
register_shutdown_function(array($objPage, "destroy"));
?>
