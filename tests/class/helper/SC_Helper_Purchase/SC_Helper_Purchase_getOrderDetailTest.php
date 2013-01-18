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
 * SC_Helper_Purchase::getOrderDetail()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_getOrderDetailTest extends SC_Helper_Purchase_TestBase {


  protected function setUp() {
    parent::setUp();
    $this->setUpOrder();
    $this->setUpOrderDetail();
    $this->setUpProductClass();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testGetOrderDetail_存在しない受注IDを指定した場合_結果が空になる() {
    $order_id = '9999';

    $this->expected = array();
    $this->actual = SC_Helper_Purchase::getOrderDetail($order_id);

    $this->verify();
  }

  public function testGetOrderDetail_存在する受注IDを指定した場合_対応する受注詳細情報が取得できる() {
    $order_id = '1001';

    $this->expected = array(
       array(
         'product_id' => '1002',
         'product_class_id' => '1002',
         'product_type_id' => '2',
         'product_code' => 'pc1002',
         'product_name' => '製品名1002',
         'classcategory_name1' => 'cat10021',
         'classcategory_name2' => 'cat10022',
         'price' => '3000',
         'quantity' => '10',
         'point_rate' => '5',
         'status' => '3',
         'payment_date' => '2032-12-31 01:20:30',
         'enable' => '0',
         'effective' => '1',
         'tax_rate' => '5',
         'tax_rule' => '0'
       ),
       array(
         'product_id' => '1001',
         'product_class_id' => '1001',
         'product_type_id' => '1',
         'product_code' => 'pc1001',
         'product_name' => '製品名1001',
         'classcategory_name1' => 'cat10011',
         'classcategory_name2' => 'cat10012',
         'price' => '4000',
         'quantity' => '15',
         'point_rate' => '6',
         'status' => '3',
         'payment_date' => '2032-12-31 01:20:30',
         'enable' => '1',
         'effective' => '1',
         'tax_rate' => '3',
         'tax_rule' => '1'
       )
    );
    $this->actual = SC_Helper_Purchase::getOrderDetail($order_id);

    $this->verify();
  }

  public function testGetOrderDetail_ステータス取得フラグがOFFのの場合_ステータス以外の情報が取得できる() {
    $order_id = '1001';

    $this->expected = array(
       array(
         'product_id' => '1002',
         'product_class_id' => '1002',
         'product_type_id' => '2',
         'product_code' => 'pc1002',
         'product_name' => '製品名1002',
         'classcategory_name1' => 'cat10021',
         'classcategory_name2' => 'cat10022',
         'price' => '3000',
         'quantity' => '10',
         'point_rate' => '5',
         // 'status' => '3',
         // 'payment_date' => '2032-12-31 01:20:30',
         'enable' => '0',
         'effective' => '1',
         'tax_rate' => '5',
         'tax_rule' => '0'
       ),
       array(
         'product_id' => '1001',
         'product_class_id' => '1001',
         'product_type_id' => '1',
         'product_code' => 'pc1001',
         'product_name' => '製品名1001',
         'classcategory_name1' => 'cat10011',
         'classcategory_name2' => 'cat10012',
         'price' => '4000',
         'quantity' => '15',
         'point_rate' => '6',
         // 'status' => '3',
         // 'payment_date' => '2032-12-31 01:20:30',
         'enable' => '1',
         'effective' => '1',
         'tax_rate' => '3',
         'tax_rule' => '1'
       )
    );
    $this->actual = SC_Helper_Purchase::getOrderDetail($order_id, false);

    $this->verify();
  }

  //////////////////////////////////////////

}

