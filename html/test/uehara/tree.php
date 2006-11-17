<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

$objView = new SC_UserView("./templates/");
$objQuery = new SC_Query();

//$objView->assignobj($objPage);
$objView->display("tree.tpl")

//-----------------------------------------------------------------------------------------------------------------------------------

?>