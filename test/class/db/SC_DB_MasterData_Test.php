<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
if (!defined("CLASS_PATH")) {
    //define("CLASS_PATH", "../../data/class/");
}
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

    /**
     * SC_DB_MasterData::getMasterData() のテストケース
     */
    function testGetMasterData() {

        $columns = array("pref_id", "pref_name", "rank");
        $masterData = new SC_DB_MasterData_Ex();
        $actual = $masterData->getMasterData("mtb_pref", $columns);

        $objQuery = new SC_Query();
        $objQuery->setorder($columns[2]);
        $results = $objQuery->select($columns[0] . ", " . $columns[1], "mtb_pref");

        $expected = array();
        foreach ($results as $result) {

            $expected[$result[$columns[0]]] = $result[$columns[1]];
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * SC_DB_MasterData::updateMasterData() のテストケース
     */
    function testUpdateMasterData() {

        $columns = array("pref_id", "pref_name", "rank");
        $masterData = new SC_DB_MasterData_Ex();

        // Transaction を有効にするため接続しておく
        $masterData->objQuery = new SC_Query();
        $masterData->objQuery->begin();

        $expected = array("10" => "北海道", "20" => "愛知", "30" => "岐阜");
        $masterData->updateMasterData("mtb_pref", $columns, $expected, false);

        $actual = $masterData->getMasterData("mtb_pref", $columns);

        $this->assertEquals($expected, $actual);

        $masterData->objQuery->rollback();
        $masterData->clearCache("mtb_pref");
    }

    /**
     * SC_DB_MasterData::createCache() のテストケース.
     */
    function testCreateCache() {
        $masterData = new SC_DB_MasterData_Ex();
        $masterData->createCache("mtb_constants", $masterData->getMasterData("mtb_constants"), true);

        $this->assertEquals(true, defined("ECCUBE_VERSION"));
    }
}
?>
