<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once(realpath(dirname(__FILE__)) . "/../../require.php");
require_once(realpath(dirname(__FILE__)) . "/../../../data/class_extends/db_extends/SC_DB_DBFactory_Ex.php");

/**
 * SC_DB_DBFactory TestCase
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id:SC_DB_DBFactory_Test.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class SC_DB_DBFactory_Test extends PHPUnit_Framework_TestCase {

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
