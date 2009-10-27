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

// {{{ requires
// {{{ requires
require_once($include_dir . "/../data/class/helper_extends/SC_Helper_DB_Ex.php"); // FIXME
require_once("PHPUnit/TestCase.php");


/**
 * SC_Helper_DB のテストケース.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Helper_DB_Test extends PHPUnit_TestCase {

    /**
     * sfTableExists() のテストケース.
     */
    function testSfTableExists() {
        $objDb = new SC_Helper_DB_Ex();
        $this->assertEquals(true, $objDb->sfTabaleExists("mtb_zip"));
    }

    /**
     * sfColumnExists() のテストケース.
     */
    function testSfColumnExists() {
        $objDb = new SC_Helper_DB_Ex();
        $this->assertEquals(true, $objDb->sfColumnExists("mtb_zip", "zipcode"));
    }

    function testSfIndexExists() {
        $objDb = new SC_Helper_DB_Ex();
        $this->assertEquals(true, $objDb->sfIndexExists("dtb_products", "category_id",
                "dtb_products_category_id_key"));
    }
}
?>
