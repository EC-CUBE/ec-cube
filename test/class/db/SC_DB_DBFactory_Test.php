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
            case 'pgsql':
                $this->assertEquals(true, preg_match("/^PostgreSQL [78].[0-9].[0-9]{1,}$/", $dbFactory->sfGetDBVersion()));
            break;

            case 'mysql':
                $this->assertEquals(true, preg_match("/^MySQL [78].[0-9].[0-9]{1,}$/", $dbFactory->sfGetDBVersion()));
            break;
            default:
        }
    }
    */

    function testFindTableNames() {
        $dbFactory = SC_DB_DBFactory::getInstance();
        $objQuery = new SC_Query(DEFAULT_DSN);
        $actual = $dbFactory->findTableNames('mtb_pre');
        $this->assertEquals('mtb_pref', $actual[0]);
    }

    function testConcatColumn() {

        $params = array('column1', 'column2');

        switch (DB_TYPE) {
        case 'pgsql':
            $expected = "column1 || column2";
            break;

        case 'mysql':
            $expected = "concat(column1, column2)";
            break;

        default:
        }

        $dbFactory = SC_DB_DBFactory::getInstance();
        $actual = $dbFactory->concatColumn($params);

        $this->assertEquals($expected, $actual);
    }

    /**
     * 昨日の売上高・売上件数を算出する SQL のテスト.
     */
    function testGetOrderYesterdaySql() {

        switch (DB_TYPE) {
        case 'pgsql':
            $expected = "SELECT COUNT(total) FROM dtb_order "
                       . "WHERE del_flg = 0 "
                         . "AND to_char(create_date,'YYYY/MM/DD') = to_char(CURRENT_TIMESTAMP - interval '1 days','YYYY/MM/DD') "
                         . "AND status <> " . ORDER_CANCEL;
            break;

        case 'mysql':
            $expected = "SELECT COUNT(total) FROM dtb_order "
                       . "WHERE del_flg = 0 "
                         . "AND cast(create_date as date) = DATE_ADD(current_date, interval -1 day) "
                         . "AND status <> " . ORDER_CANCEL;
            break;

        default:
        }

        $dbFactory = SC_DB_DBFactory::getInstance();
        $actual = $dbFactory->getOrderYesterdaySql('COUNT');

        $this->assertEquals($expected, $actual);
    }

    /**
     * 当月の売上高・売上件数を算出する SQL のテスト.
     */
    function testGetOrderMonthSql() {
        switch (DB_TYPE) {
        case 'pgsql':
            $expected =  "SELECT COUNT(total) FROM dtb_order "
                        . "WHERE del_flg = 0 "
                          . "AND to_char(create_date,'YYYY/MM') = ? "
                          . "AND to_char(create_date,'YYYY/MM/DD') <> to_char(CURRENT_TIMESTAMP,'YYYY/MM/DD') "
                          . "AND status <> " . ORDER_CANCEL;
            break;

        case 'mysql':
            $expected = "SELECT COUNT(total) FROM dtb_order "
                       . "WHERE del_flg = 0 "
                         . "AND date_format(create_date, '%Y/%m') = ? "
                         . "AND date_format(create_date, '%Y/%m/%d') <> date_format(CURRENT_TIMESTAMP, '%Y/%m/%d') "
                         . "AND status <> " . ORDER_CANCEL;
            break;

        default:
        }

        $dbFactory = SC_DB_DBFactory::getInstance();
        $actual = $dbFactory->getOrderMonthSql('COUNT');

        $this->assertEquals($expected, $actual);
    }

    /**
     * 昨日のレビュー書き込み件数を算出する SQL のテスト.
     */
    function testGetReviewYesterdaySql() {
        switch (DB_TYPE) {
        case 'pgsql':
            $expected = "SELECT COUNT(*) FROM dtb_review AS A "
                   . "LEFT JOIN dtb_products AS B "
                          . "ON A.product_id = B.product_id "
                       . "WHERE A.del_flg=0 "
                         . "AND B.del_flg = 0 "
                         . "AND to_char(A.create_date, 'YYYY/MM/DD') = to_char(CURRENT_TIMESTAMP - interval '1 days','YYYY/MM/DD') "
                         . "AND to_char(A.create_date,'YYYY/MM/DD') != to_char(CURRENT_TIMESTAMP,'YYYY/MM/DD')";
            break;

        case 'mysql':
            $expected = "SELECT COUNT(*) FROM dtb_review AS A "
                   . "LEFT JOIN dtb_products AS B "
                          . "ON A.product_id = B.product_id "
                       . "WHERE A.del_flg = 0 "
                         . "AND B.del_flg = 0 "
                         . "AND cast(A.create_date as date) = DATE_ADD(current_date, interval -1 day) "
                         . "AND cast(A.create_date as date) != current_date";

            break;

        default:
        }

        $dbFactory = SC_DB_DBFactory::getInstance();
        $actual = $dbFactory->getReviewYesterdaySql();

        $this->assertEquals($expected, $actual);
    }

}

/*
 * Local variables:
 * coding: utf-8
 * End:
 */
?>
