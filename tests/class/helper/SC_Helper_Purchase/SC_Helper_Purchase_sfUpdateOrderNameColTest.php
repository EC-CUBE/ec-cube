<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_TestBase.php");
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

/**
 * SC_Helper_Purchase::sfUpdateOrderNameCol()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_sfUpdateOrderNameColTest extends SC_Helper_Purchase_TestBase {
  var $helper;

  protected function setUp() {
    parent::setUp();
    $this->setUpOrder();
    $this->setUpOrderTemp();
    $this->setUpPayment();
    $this->setUpDeliv();
    $this->setUpDelivTime();
    $this->setUpShippingOnDb();

    $this->helper = new SC_Helper_Purchase();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfUpdateOrderNameCol_TEMPフラグがOFFの場合_受注テーブルと発送テーブルが更新される() {
    $order_id = '1002';

    $this->helper->sfUpdateOrderNameCol($order_id);

    $this->expected['shipping'] = array(array('shipping_time' => '午前'));
    $this->expected['order'] = array(array('payment_method' => '支払方法1002'));
    $this->expected['order_temp'] = array(array('payment_method' => '支払方法1001')); // 変更されていない

    $this->actual['shipping'] = $this->objQuery->select(
      'shipping_time', 'dtb_shipping', 'order_id = ?', array($order_id)
    );
    $this->actual['order'] = $this->objQuery->select(
      'payment_method', 'dtb_order', 'order_id = ?', array($order_id)
    );
    $this->actual['order_temp'] = $this->objQuery->select(
      'payment_method', 'dtb_order_temp', 'order_temp_id = ?', array($order_id)
    );
    $this->verify();
  }

  public function testSfUpdateOrderNameCol_TEMPフラグがONの場合_一時テーブルが更新される() {
    $order_id = '1002';

    $this->helper->sfUpdateOrderNameCol($order_id, true);

    $this->expected['shipping'] = array(array('shipping_time' => '午後')); // 変更されていない
    $this->expected['order'] = array(array('payment_method' => '支払方法1001')); // 変更されていない
    $this->expected['order_temp'] = array(array('payment_method' => '支払方法1002'));

    $this->actual['shipping'] = $this->objQuery->select(
      'shipping_time', 'dtb_shipping', 'order_id = ?', array($order_id)
    );
    $this->actual['order'] = $this->objQuery->select(
      'payment_method', 'dtb_order', 'order_id = ?', array($order_id)
    );
    $this->actual['order_temp'] = $this->objQuery->select(
      'payment_method', 'dtb_order_temp', 'order_temp_id = ?', array($order_id)
    );
    $this->verify();
  }

  //////////////////////////////////////////

}

