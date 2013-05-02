<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_TestBase.php");
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

/**
 * SC_Helper_Purchase::getShipmentItems()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_getShipmentItemsTest extends SC_Helper_Purchase_TestBase {

  protected function setUp() {
    parent::setUp();
    $this->setUpShipmentItem();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testGetShipmentItems_存在しない受注IDを指定した場合_結果が空になる() {
    $order_id = '100'; // 存在しないID
    $shipping_id = '1';

    $this->expected = array();
    $this->actual = SC_Helper_Purchase::getShipmentItems($order_id, $shipping_id);

    $this->verify('配送情報');
  }

  public function testGetShipmentItems_存在しない配送先IDを指定した場合_結果が空になる() {
    $order_id = '1';
    $shipping_id = '100'; // 存在しないID

    $this->expected = array();
    $this->actual = SC_Helper_Purchase::getShipmentItems($order_id, $shipping_id);

    $this->verify('配送情報');
  }

  public function testGetShipmentItems_存在する受注IDと配送先IDを指定した場合_結果が取得できる() {
    $order_id = '1';
    $shipping_id = '1';
    
    $this->expected['count'] = 2;
    $this->expected['first'] = array(
      'order_id' => '1',
      'shipping_id' => '1',
      'product_class_id' => '1001',
      'product_name' => '商品名01',
      'price' => '1500',
      'productsClass' => array('product_class_id' => '1001', 'product_id' => '1001')
    );
    $this->expected['second'] = array(
      'order_id' => '1',
      'shipping_id' => '1',
      'product_class_id' => '1002',
      'product_name' => '商品名02',
      'price' => '2400',
      'productsClass' => array('product_class_id' => '1002', 'product_id' => '1002')
    );

    $result = SC_Helper_Purchase::getShipmentItems($order_id, $shipping_id);
    $this->actual['count'] = count($result);
    $this->actual['first'] = Test_Utils::mapArray($result[0], array(
      'order_id', 'shipping_id', 'product_class_id', 'product_name', 'price', 'productsClass'));
    $this->actual['first']['productsClass'] = Test_Utils::mapArray($this->actual['first']['productsClass'], array('product_class_id', 'product_id'));
    $this->actual['second'] = Test_Utils::mapArray($result[1], array(
      'order_id', 'shipping_id', 'product_class_id', 'product_name', 'price', 'productsClass'));
    $this->actual['second']['productsClass'] = Test_Utils::mapArray($this->actual['second']['productsClass'], array('product_class_id', 'product_id'));
    $this->verify('配送情報');
  }

  public function testGetShipmentItems_詳細フラグをOFFにした場合_結果に詳細情報が含まれない() {
    $order_id = '1';
    $shipping_id = '1';
    
    $this->expected['count'] = 2;
    $this->expected['first'] = array(
      'order_id' => '1',
      'shipping_id' => '1',
      'product_class_id' => '1001',
      'product_name' => '商品名01',
      'price' => '1500',
      'productsClass' => null
    );
    $this->expected['second'] = array(
      'order_id' => '1',
      'shipping_id' => '1',
      'product_class_id' => '1002',
      'product_name' => '商品名02',
      'price' => '2400',
      'productsClass' => null
    );

    $result = SC_Helper_Purchase::getShipmentItems($order_id, $shipping_id, false);
    $this->actual['count'] = count($result);
    $this->actual['first'] = Test_Utils::mapArray($result[0], array(
      'order_id', 'shipping_id', 'product_class_id', 'product_name', 'price', 'productsClass'));
    $this->actual['second'] = Test_Utils::mapArray($result[1], array(
      'order_id', 'shipping_id', 'product_class_id', 'product_name', 'price', 'productsClass'));
    $this->verify('配送情報');

  }

  //////////////////////////////////////////

}

