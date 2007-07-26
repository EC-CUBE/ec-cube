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
require_once("class/db/SC_DB_DBFactory_Test.php");
require_once("class/db/SC_DB_MasterData_Test.php");
require_once("PHPUnit.php");

$suites = array();
$suites[0] = new PHPUnit_TestSuite("LC_Page_Test");
$suites[1] = new PHPUnit_TestSuite("SC_DB_DBFactory_Test");
$suites[2] = new PHPUnit_TestSuite("SC_DB_MasterData_Test");

foreach ($suites as $suite) {
    $result = PHPUnit::run($suite);
    print $result->toString();
}
?>
