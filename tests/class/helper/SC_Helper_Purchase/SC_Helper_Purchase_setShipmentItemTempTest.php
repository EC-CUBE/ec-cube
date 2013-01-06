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
 * SC_Helper_Purchase::setShipmentItemTemp()のテストクラス.
 * 【注意】dtb_baseinfoはインストール時に入るデータをそのまま使用
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_setShipmentItemTempTest extends SC_Helper_Purchase_TestBase {
  private $helper;

  protected function setUp() {
    parent::setUp();
    $this->setUpProductClass();
    $this->setUpProducts();

    $_SESSION['shipping']['1001']['shipment_item'] = array(
      '1001' => array('productsClass' => array('price02' => 9000))
    );
    $this->helper = new SC_Helper_Purchase();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSetShipmentItemTemp_製品情報が既に存在する場合_存在する情報が価格に反映される() {
    $this->helper->setShipmentItemTemp('1001', '1001', 10);

    $this->expected = array(
      'shipping_id' => '1001',
      'product_class_id' => '1001',
      'quantity' => 10,
      'price' => 9000,
      'total_inctax' => 94500.0,
      'productsClass' => array('price02' => 9000)
    );
    $this->actual = $_SESSION['shipping']['1001']['shipment_item']['1001'];

    $this->verify();
  }

  public function testSetShipmentItemTemp_製品情報が存在しない場合_DBから取得した値が反映される() {
    $this->helper->setShipmentItemTemp('1001', '1002', 10);

    $this->expected = array(
      'shipping_id' => '1001',
      'product_class_id' => '1002',
      'quantity' => 10,
      'price' => '2500',
      'total_inctax' => 26250.0
    );
    $result = $_SESSION['shipping']['1001']['shipment_item']['1002'];
    unset($result['productsClass']);
    $this->actual = $result;

    $this->verify();
  }

  //////////////////////////////////////////

}

