<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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
