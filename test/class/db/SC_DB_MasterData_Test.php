<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(realpath(dirname(__FILE__)) . "/../../../data/class_extends/db_extends/SC_DB_MasterData_Ex.php");

/**
 * SC_DB_MasterData のテストケース.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id:SC_DB_MasterData_Test.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class SC_DB_MasterData_Test extends PHPUnit_Framework_TestCase {

    // }}}
    // {{{ functions

    /**
     * SC_DB_MasterData::getMasterData() のテストケース
     */
    function testGetMasterData() {
        $columns = array('id', 'name', 'rank');
        $masterData = new SC_DB_MasterData_Ex();
        $actual = $masterData->getMasterData('mtb_pref', $columns);

        $objQuery = new SC_Query();
        $objQuery->setorder($columns[2]);
        $results = $objQuery->select($columns[0] . ", " . $columns[1], 'mtb_pref');

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

        $columns = array('id', 'name', 'rank');
        $masterData = new SC_DB_MasterData_Ex();

        // Transaction を有効にするため接続しておく
        $masterData->objQuery = new SC_Query();
        $masterData->objQuery->begin();

        $expected = array('10' => "北海道", '20' => "愛知", '30' => "岐阜");
        $masterData->updateMasterData('mtb_pref', $columns, $expected, false);

        $actual = $masterData->getDBMasterData('mtb_pref', $columns);

        $this->assertEquals($expected['10'], $actual['10']);
        $this->assertEquals($expected['20'], $actual['20']);
        $this->assertEquals($expected['30'], $actual['30']);

        $masterData->objQuery->rollback();
        $masterData->clearCache('mtb_pref');
    }

    /**
     * SC_DB_MasterData::createCache() のテストケース.
     */
    function testCreateCache() {
        $masterData = new SC_DB_MasterData_Ex();
        $masterData->clearCache('mtb_constants');
        $masterData->createCache('mtb_constants', array(), true, array('id', 'remarks'));
        $this->assertEquals(true, defined('ECCUBE_VERSION'));
    }
}
?>
