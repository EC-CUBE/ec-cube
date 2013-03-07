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
// TODO SC_Query は, できるだけ単独で動くようにしたい
require_once(realpath(dirname(__FILE__)) . "/../require.php");
require_once(realpath(dirname(__FILE__)) . "/../../data/class/SC_Query.php");

/**
 * SC_Query のテストケース.
 *
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class SC_Query_Test extends PHPUnit_Framework_TestCase 
{

    /** SC_Query インスタンス */
    var $objQuery;

    var $expected;
    var $actual;

    function setUp()
    {
        $this->objQuery = new SC_Query();
        $this->objQuery->begin();
    }

    function tearDown()
    {
        // MySQL では CREATE TABLE がロールバックされないので DROP TABLE を行う
        $this->dropTestTable();
        $this->objQuery->rollback();
        $this->objQuery = null;
    }

    function verify()
    {
        $this->assertEquals($this->expected, $this->actual);
    }

    /**
     * インスタンスを取得するテストケース.
     */
    function testGetInstance()
    {
        $this->expected = true;
        $this->actual = is_object($this->objQuery);

        $this->verify();
    }

    /**
     * SC_Query::query() を使用して, CREATE TABLE を実行するテストケース.
     */
    function testCreateTable()
    {
        $result = $this->createTestTable();

        $this->expected = false;
        $this->actual = PEAR::isError($result);

        $this->verify();
    }

    /**
     * SC_Query::getAll() のテストケース.
     */
    function testGetAll()
    {
        $result = $this->createTestTable();
        $result = $this->setTestData(1, '2', 'f');

        $this->expected =  array(array('id' => '1',
                                       'column1' => '1',
                                       'column2' => '2',
                                       'column3' => 'f'));
        $this->actual = $this->objQuery->getAll("SELECT * FROM test_table WHERE id = ?", array(1));

        $this->verify();
    }

    /**
     * SC_Query::select() のテストケース.
     */
    function testSelect()
    {
        $this->createTestTable();
        $result = $this->setTestData(1, '2', 'f');

        $this->expected =  array(array('id' => '1',
                                       'column1' => '1',
                                       'column2' => '2',
                                       'column3' => 'f'));

        $this->actual = $this->objQuery->setWhere("id = ?")
                                       ->setOrder('id')
                                       ->select("*", 'test_table', "", array(1));

        $this->verify();
    }


    /**
     * SC_Query::getOne() のテストケース.
     */
    function testGetOne()
    {
        $this->createTestTable();
        $this->setTestData(1, '2', 'f');
        $this->setTestData(1, '2', 'f');
        $this->setTestData(1, '2', 'f');

        $this->expected = 3;
        $this->actual = $this->objQuery->getOne("SELECT COUNT(*) FROM test_table");

        $this->verify();
    }

    /**
     * SC_Query::getRow() のテストケース.
     */
    function testGetRow()
    {
        $this->createTestTable();
        $this->setTestData(1, '1', 'f');
        $this->setTestData(2, '2', 'f');
        $this->setTestData(3, '3', 'f');

        $this->expected = array('column1' => 1, 'column2' => 1);
        $this->actual = $this->objQuery->getRow("column1, column2", 'test_table', "id = ?", array(1));
        $this->verify();
    }

    /**
     * SC_Query::getCol() のテストケース.
     */
    function testGetCol()
    {
        $this->createTestTable();
        $this->setTestData(1, '1', 'f');
        $this->setTestData(2, '2', 'f');
        $this->setTestData(3, '3', 'f');

        $this->expected = array(1, 2);
        $this->actual = $this->objQuery->getCol('column1', 'test_table', "id < ?",  array(3));

        $this->verify();

    }

    /**
     * SC_Query::query() で INSERT を実行するテストケース.
     */
    function testQuery1()
    {
        $this->createTestTable();
        $sql = "INSERT INTO test_table VALUES (?, ?, ?, ?)";
        $data = array('1', '1', '1', 'f');

        $this->objQuery->query($sql, $data);

        $this->expected =  array(array('id' => '1',
                                       'column1' => '1',
                                       'column2' => '1',
                                       'column3' => 'f'));

        $this->actual = $this->objQuery->getAll("SELECT * FROM test_table");

        $this->verify();
    }

    function testInsert()
    {
        $this->createTestTable();

        $this->objQuery->insert('test_table',
                                array('id' => '1',
                                      'column1' => '1',
                                      'column2' => '1',
                                      'column3' => 'f'));

        $this->expected =  array(array('id' => '1',
                                       'column1' => '1',
                                       'column2' => '1',
                                       'column3' => 'f'));

        $this->actual = $this->objQuery->getAll("SELECT * FROM test_table");

        $this->verify();
    }

    /**
     * SC_Query::query() で UPDATE を実行するテストケース.
     */
    function testQuery2()
    {
        $this->createTestTable();
        $this->setTestData(1, '2', 'f');

        $sql = "UPDATE test_table SET column1 = ?, column2 = ? WHERE id = ?";
        $data = array('2', '2', '1');

        $this->objQuery->query($sql, $data);

        $this->expected =  array(array('id' => '1',
                                       'column1' => '2',
                                       'column2' => '2',
                                       'column3' => 'f'));

        $this->actual = $this->objQuery->getAll("SELECT * FROM test_table");

        $this->verify();
    }

    function testUpdate()
    {
        $this->createTestTable();
        $this->setTestData(1, '2', 'f');

        $this->objQuery->update('test_table',
                                array('id' => '1',
                                      'column1' => '2',
                                      'column2' => '2',
                                      'column3' => 'f'),
                                "id = ?", array(1));
        $this->expected =  array(array('id' => '1',
                                       'column1' => '2',
                                       'column2' => '2',
                                       'column3' => 'f'));

        $this->actual = $this->objQuery->getAll("SELECT * FROM test_table");

        $this->verify();
    }

    function testListTables()
    {
        $tables = $this->objQuery->listTables();
        $this->assertTrue(in_array('mtb_zip', $tables));
    }

    function testListSequences()
    {
        $sequences = $this->objQuery->listSequences();
        $this->assertTrue(in_array('dtb_products_product_id', $sequences));
    }

    function testListTableFields()
    {
        $this->expected = array('id', 'name', 'rank', 'remarks');
        $this->actual = $this->objQuery->listTableFields('mtb_constants');
        $this->verify();
    }

    function testListTableIndexes()
    {
        $indexes = $this->objQuery->listTableIndexes('dtb_mobile_kara_mail');
        $this->assertTrue(in_array('dtb_mobile_kara_mail_create_date_key', $indexes));
    }

    function createTestTable()
    {
        $sql = "CREATE TABLE test_table ("
            . "id SERIAL PRIMARY KEY,"
            . "column1 numeric(9),"
            . "column2 varchar(20),"
            . "column3 char(1)"
            . ")";
        return $this->objQuery->query($sql);
    }

    function dropTestTable()
    {
        $tables = $this->objQuery->listTables();
        if (in_array('test_table', $tables)) {
            $this->objQuery->query("DROP TABLE test_table");
        }
        return;
    }

    function setTestData($column1, $column2, $column3)
    {
        $fields_values = array($column1, $column2, $column3);
        $sql = "INSERT INTO test_table (column1, column2, column3) VALUES (?, ?, ?)";
        $result = $this->objQuery->query($sql, $fields_values);
        if (PEAR::isError($result)) {
            var_dump($result);
        }
        return $result;
    }
}
?>
