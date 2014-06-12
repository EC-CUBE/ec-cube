<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_TestBase.php");
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
 * SC_Helper_Purchase::getShippings()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_getShippingsTest extends SC_Helper_Purchase_TestBase
{


  protected function setUp()
  {
    parent::setUp();
    $this->setUpShipmentItem();
    $this->setUpShippingOnDb();
    $this->setUpOrderDetail();
  }

  protected function tearDown()
  {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testGetShippings_存在しない受注IDを指定した場合_結果が空になる()
  {
    $order_id = '100'; // 存在しないID

    $this->expected = array();
    $helper = new SC_Helper_Purchase();
    $this->actual = $helper->getShippings($order_id);

    $this->verify('配送情報');
  }

  public function testGetShippings_存在する受注IDを指定した場合_結果が取得できる()
  {
    $order_id = '1001';

    $this->expected['count'] = 1;
    $this->expected['first'] = array(
      'order_id' => '1001',
      'shipping_id' => '1',
      'shipping_name01' => '配送情報01',
      'shipping_date' => '2012-01-12 00:00:00'
    );
    $this->expected['shipment_item_count'] = 2;

    $helper = new SC_Helper_Purchase();
    $result = $helper->getShippings($order_id);
    $this->actual['count'] = count($result);
    // shipping_idごとの配列になっているのでshipping_idで抽出
    $this->actual['first'] = Test_Utils::mapArray($result['1'], array(
      'order_id', 'shipping_id', 'shipping_name01', 'shipping_date'));
    $this->actual['shipment_item_count'] = count($result['1']['shipment_item']);
    $this->verify('配送情報');
  }

  public function testGetShippings_商品取得フラグをOFFにした場合_結果に商品情報が含まれない()
  {
    $order_id = '1001';

    $this->expected['count'] = 1;
    $this->expected['first'] = array(
      'order_id' => '1001',
      'shipping_id' => '1',
      'shipping_name01' => '配送情報01',
      'shipping_date' => '2012-01-12 00:00:00'
    );
    $this->expected['shipment_item_count'] = 0;

    $helper = new SC_Helper_Purchase();
    $result = $helper->getShippings($order_id, false);
    $this->actual['count'] = count($result);
    // shipping_idごとの配列になっているのでshipping_idで抽出
    $this->actual['first'] = Test_Utils::mapArray($result['1'], array(
      'order_id', 'shipping_id', 'shipping_name01', 'shipping_date'));
    $this->actual['shipment_item_count'] = count($result['1']['shipment_item']);
    $this->verify('配送情報');
  }

  //////////////////////////////////////////
}

