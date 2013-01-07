<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(realpath(dirname(__FILE__)) . "/../require.php");
require_once(realpath(dirname(__FILE__)) . "/../../data/class/SC_DbConn.php");

/**
 * SC_DbConn のテストケース.
 *
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class SC_DbConn_Test extends PHPUnit_Framework_TestCase {

    /** SC_DbConn インスタンス */
    var $objDbConn;

    var $expected;
    var $actual;

    function setUp() {
        $this->objDbConn = new SC_DbConn();
        $this->objDbConn->query('BEGIN');
    }

    function tearDown() {
        $this->objDbConn->query('ROLLBACK');
        $this->objDbConn = null;
    }

    function verify() {
        $this->assertEquals($this->expected, $this->actual);
    }

    /**
     * インスタンスを取得するテストケース.
     */
    function testGetInstance() {
        $this->expected = true;
        $this->actual = is_object($this->objDbConn);

        $this->verify();
    }

    /**
     * SC_DbConn:query() を使用して, CREATE TABLE を実行するテストケース.
     */
    function testCreateTable() {
        $result = $this->createTestTable();

        $this->expected = false;
        $this->actual = PEAR::isError($result);

        $this->verify();
    }

    /**
     * SC_DbConn::getAll() のテストケース.
     */
    function testGetAll() {
        $this->createTestTable();
        $result = $this->setTestData(1, '2', 'f');

        $this->expected =  array(array('id' => '1',
                                       'column1' => '1',
                                       'column2' => '2',
                                       'column3' => 'f'));
        $this->actual = $this->objDbConn->getAll("SELECT * FROM test_table WHERE id = ?", array(1));

        $this->verify();
    }

    /**
     * SC_DbConn::getAll() のテストケース(エラー).
     */
    /*
    function testGetAllIsError() {

        // SC_DbConn::getAll() は接続エラーが発生すると 0 を返す
        $failur_dsn = "pgsql://user:pass@127.0.0.1:/xxxxx";
        $failurDbConn = new SC_DbConn($failur_dsn, false, true);
        $this->expected = 0;
        $this->actual = $failurDbConn->getAll("SELECT * FROM test_table");

        $this->verify();
    }
    */

    /**
     * SC_DbConn::getOne() のテストケース.
     */
    function testGetOne() {
        $this->createTestTable();
        $this->setTestData(1, '2', 'f');
        $this->setTestData(1, '2', 'f');
        $this->setTestData(1, '2', 'f');

        $this->expected = 3;
        $this->actual = $this->objDbConn->getOne("SELECT COUNT(*) FROM test_table");

        $this->verify();
    }

    /**
     * SC_DbConn::getOne() のテストケース(エラー).
     */
    /*
    function testGetOneIsError() {
        $this->createTestTable();
        $this->setTestData(1, '2', 'f');
        $this->setTestData(1, '2', 'f');
        $this->setTestData(1, '2', 'f');

        //$this->expected = new PEAR_Error();
        $this->actual = $this->objDbConn->getOne("SELECT COUNT(*) FROM xxx_table");
        var_dump($this->actual);
        $this->verify();
    }
    */

    /**
     * SC_DbConn::getRow() のテストケース.
     */
    function testGetRow() {
        $this->createTestTable();
        $this->setTestData(1, '1', 'f');
        $this->setTestData(2, '2', 'f');
        $this->setTestData(3, '3', 'f');

        $this->expected = array('column1' => 1, 'column2' => 1);
        $this->actual = $this->objDbConn->getRow("SELECT column1, column2 FROM test_table WHERE id = ?", array(1));
        $this->verify();
    }

    /**
     * SC_DbConn::getCol() のテストケース.
     */
    function testGetCol() {
        $this->createTestTable();
        $this->setTestData(1, '1', 'f');
        $this->setTestData(2, '2', 'f');
        $this->setTestData(3, '3', 'f');

        $this->expected = array(1, 2);
        $this->actual = $this->objDbConn->getCol("SELECT column1, column2 FROM test_table WHERE id < ?", 'column1', array(3));

        $this->verify();

    }

    /**
     * SC_DbConn::autoExecute() で INSERT を実行するテストケース.
     */
    /*
    function testAutoExecuteOfInsert() {
        $this->createTestTable();
        $result = $this->setTestData(1, '2', 'f');

        $this->expected =  array(array('id' => '1',
                                       'column1' => '1',
                                       'column2' => '2',
                                       'column3' => 'f'));
        $this->actual = $this->objDbConn->getAll("SELECT * FROM test_table");

        //$this->assertEquals(1, $result);
        $this->verify();
    }
    */
    /**
     * SC_DbConn::autoExecute() で UPDATE を実行するテストケース.
     */
    /*
    function testAutoExecuteOfUpdate() {
        $this->createTestTable();
        $this->setTestData(1, '2', 'f');

        $data = array('id' => '1',
                      'column1' => '2',
                      'column2' => '3',
                      'column3' => 't');

        $result = $this->objDbConn->autoExecute('test_table', $data, "id = 1");

        $this->expected =  array($data);
        $this->actual = $this->objDbConn->getAll("SELECT * FROM test_table");

        $this->assertEquals(1, $result);
        $this->verify();
    }
    */

    /**
     * SC_DbConn::query() で INSERT を実行するテストケース.
     */
    function testQuery1() {
        $this->createTestTable();
        $sql = "INSERT INTO test_table VALUES (?, ?, ?, ?)";
        $data = array('1', '1', '1', 'f');

        $this->objDbConn->query($sql, $data);

        $this->expected =  array(array('id' => '1',
                                       'column1' => '1',
                                       'column2' => '1',
                                       'column3' => 'f'));

        $this->actual = $this->objDbConn->getAll("SELECT * FROM test_table");

        $this->verify();
    }

    /**
     * SC_DbConn::query() で UPDATE を実行するテストケース.
     */
    function testQuery2() {
        $this->createTestTable();
        $this->setTestData(1, '2', 'f');

        $sql = "UPDATE test_table SET column1 = ?, column2 = ? WHERE id = ?";
        $data = array('2', '2', '1');

        $this->objDbConn->query($sql, $data);

        $this->expected =  array(array('id' => '1',
                                       'column1' => '2',
                                       'column2' => '2',
                                       'column3' => 'f'));

        $this->actual = $this->objDbConn->getAll("SELECT * FROM test_table");

        $this->verify();
    }


    /**
     * SC_DbConn::prepare() は未使用
     */
    function testPrepare() {
    }

    /**
     * SC_DbConn::execute() は未使用
     */
    function testExecute() {
    }

    /**
     * SC_DbConn::reset() は未使用
     */
    function testReset() {
    }

    function createTestTable() {
        $sql = "CREATE TABLE test_table ("
            . "id SERIAL PRIMARY KEY,"
            . "column1 numeric(9),"
            . "column2 varchar(20),"
            . "column3 char(1)"
            . ")";
        return $this->objDbConn->query($sql);
    }

    function setTestData($column1, $column2, $column3) {
        $fields_values = array($column1, $column2, $column3);
        $sql = "INSERT INTO test_table (column1, column2, column3) VALUES (?, ?, ?)";
        $result = $this->objDbConn->query($sql, $fields_values);
        if (PEAR::isError($result)) {
            var_dump($result);
        }
        return $result;
    }
}
?>
