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
 * SC_DB_DBFactory のテストケース.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_DB_DBFactory_Test extends PHPUnit_TestCase {

    // }}}
    // {{{ functions

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
}
?>
