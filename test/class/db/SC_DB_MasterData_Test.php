<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "db_extends/SC_DB_MasterData_Ex.php");
require_once("PHPUnit/TestCase.php");

/**
 * SC_DB_MasterData のテストケース.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_DB_MasterData_Test extends PHPUnit_TestCase {

    // }}}
    // {{{ functions

    function testGetMasterData() {

        $masterData = new SC_DB_MasterData_Ex();
        $actual = $masterData->getMasterData("mtb_pref");

        $objQuery = new SC_Query();
        $expected = $objQuery->select("*", $name);

        $this->assertEquals($expected, $actual);
    }
}
?>
