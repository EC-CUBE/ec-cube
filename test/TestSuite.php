#!/usr/local/bin/php
<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once("../html/require.php");
require_once("class/page/LC_Page_Test.php");
require_once("PHPUnit.php");

$suite = new PHPUnit_TestSuite("LC_Page_Test");
$result = PHPUnit::run($suite);

print $result->toString();
?>
