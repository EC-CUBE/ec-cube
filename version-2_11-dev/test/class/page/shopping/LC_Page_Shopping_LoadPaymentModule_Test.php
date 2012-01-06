<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(realpath(dirname(__FILE__)) . '/../../../require.php');
require_once(realpath(dirname(__FILE__)) . '/../../../../data/class/pages/shopping/LC_Page_Shopping_LoadPaymentModule.php');

/**
 * LC_Page_Admin_Products_ProductClass のテストケース.
 *
 * @package Page
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class LC_Page_Shopping_LoadPaymentModule_Test extends PHPUnit_Framework_TestCase {

    function setUp() {
        $this->objQuery =& SC_Query::getSingletonInstance();
        $this->objQuery->begin();
        $this->objPage = new LC_Page_Shopping_LoadPaymentModule();
    }

    function tearDown() {
        $this->objQuery->rollback();
        $this->objPage = null;
    }

    function testGetOrderIdBySession() {
        $_SESSION['order_id'] = 1;
        $_GET['order_id'] = 2;
        $_POST['order_id'] = 3;

        $this->expected = $_SESSION['order_id'];
        $this->actual = $this->objPage->getOrderId();

        $this->verify();
    }

    function testGetOrderIdByPOST() {
        $_GET['order_id'] = 1;
        $_POST['order_id'] = 1;

        $this->expected = $_POST['order_id'];
        $this->actual = $this->objPage->getOrderId();

        $this->verify();
    }

    function testGetOrderIdByGET() {
        $_GET['order_id'] = 2;

        $this->expected = $_GET['order_id'];
        $this->actual = $this->objPage->getOrderId();

        $this->verify();
    }

    function testGetOrderIdIsNull() {
        $this->assertFalse($this->objPage->getOrderId());
    }

    function testGetModulePath() {
        $order_id = 10000;
        $payment_id = 10000;
        $module_path = __FILE__;
        $this->setPayment($order_id, $payment_id, $module_path);

        $this->expected = __FILE__;
        $this->actual = $this->objPage->getModulePath($order_id);

        $this->verify();
    }

    function testGetModulePathIsFailure() {
        $order_id = 10000;
        $payment_id = 10000;
        $module_path = "aaa";
        $this->setPayment($order_id, $payment_id, $module_path);

        $this->actual = $this->objPage->getModulePath($order_id);

        $this->assertFalse($this->actual);

    }

    function verify() {
        $this->assertEquals($this->expected, $this->actual);
    }

    function setPayment($order_id, $payment_id, $module_path) {
        $this->objQuery->insert('dtb_order', array('order_id' => $order_id,
                                                   'customer_id' => (int) 0,
                                                   'payment_id' => $payment_id,
                                                   'create_date' => 'CURRENT_TIMESTAMP',
                                                   'update_date' => 'CURRENT_TIMESTAMP'));

        $this->objQuery->insert("dtb_payment", array('payment_id' => $order_id,
                                                     'module_path' => $module_path,
                                                     'creator_id' => 1,
                                                     'create_date' => 'CURRENT_TIMESTAMP',
                                                     'update_date' => 'CURRENT_TIMESTAMP'));
    }
}
