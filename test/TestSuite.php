#!/usr/local/bin/php
<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

ini_set("include_path", ".:/usr/local/share/pear");
// {{{ requires
require_once("../html/require.php");
require_once("class/page/LC_Page_Test.php");
require_once("class/db/SC_DB_DBFactory_Test.php");
require_once("class/db/SC_DB_MasterData_Test.php");
require_once("class/helper/SC_Helper_DB_Test.php");
require_once("PHPUnit.php");

$suites = array();
$suites[0] = new PHPUnit_TestSuite("LC_Page_Test");
$suites[1] = new PHPUnit_TestSuite("SC_DB_DBFactory_Test");
$suites[2] = new PHPUnit_TestSuite("SC_DB_MasterData_Test");
$suites[3] = new PHPUnit_TestSuite("SC_Helper_DB_Test");

foreach ($suites as $suite) {
    $result = PHPUnit::run($suite);
    print $result->toString();
}
?>
