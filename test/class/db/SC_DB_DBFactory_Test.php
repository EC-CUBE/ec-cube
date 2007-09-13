<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once($include_dir . "/../data/class/db_extends/SC_DB_DBFactory_Ex.php"); // FIXME
require_once("PHPUnit/TestCase.php");

/**
 * SC_DB_DBFactory TestCase
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id:SC_DB_DBFactory_Test.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class SC_DB_DBFactory_Test extends PHPUnit_TestCase {

    // }}}
    // {{{ functions

    /* TODO
    function testSfGetDBVersion() {
        $dbFactory = SC_DB_DBFactory::getInstance();
        $objQuery = new SC_Query(DEFAULT_DSN, true, true);
        switch (DB_TYPE) {
            case "pgsql":
                $this->assertEquals(true, preg_match("/^PostgreSQL [78].[0-9].[0-9]{1,}$/", $dbFactory->sfGetDBVersion()));
            break;

            case "mysql":
                $this->assertEquals(true, preg_match("/^MySQL [78].[0-9].[0-9]{1,}$/", $dbFactory->sfGetDBVersion()));
            break;
            default:
        }
    }
    */

    function testFindTableNames() {
        $dbFactory = SC_DB_DBFactory::getInstance();
        $objQuery = new SC_Query(DEFAULT_DSN, true, true);
        $actual = $dbFactory->findTableNames("mtb_pre");
        $this->assertEquals("mtb_pref", $actual[0]);
    }
}

/*
 * Local variables:
 * coding: utf-8
 * End:
 */
?>
