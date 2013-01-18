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
 * SC_Helper_Purchase::setShipmentItemTempForSole()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_setShipmentItemTempForSoleTest extends SC_Helper_Purchase_TestBase {


  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
    $_SESSION['testResult'] = null;
  }

  /////////////////////////////////////////
  public function testSetShipmentItemTempForSole__いったん配送情報がクリアされたあと改めて指定のものが設定される() {
    $helper = new SC_Helper_Purchase_setShipmentItemTempForSoleMock();
    $cartSession = new SC_CartSession_setShipmentItemTempForSoleMock();
    $shipping_id = '1001';

    $helper->setShipmentItemTempForSole($cartSession, $shipping_id);

    $this->expected = array(
      'clearShipmentItemTemp' => TRUE,
      'shipmentItemTemp' => array(
        array('shipping_id'=>'1001', 'id'=>'1', 'quantity'=>'10'),
        array('shipping_id'=>'1001', 'id'=>'2', 'quantity'=>'5')
      )
    );
    $this->actual = $_SESSION['testResult'];

    $this->verify();
  }

  //////////////////////////////////////////

}

class SC_Helper_Purchase_setShipmentItemTempForSoleMock extends SC_Helper_Purchase {
  function clearShipmentItemTemp() {
    $_SESSION['testResult']['clearShipmentItemTemp'] = TRUE;
  }

  function setShipmentItemTemp($shipping_id, $id, $quantity) {
    $_SESSION['testResult']['shipmentItemTemp'][] = 
      array('shipping_id' => $shipping_id, 'id' => $id, 'quantity' => $quantity);
  }
}

class SC_CartSession_setShipmentItemTempForSoleMock extends SC_CartSession {
  function getCartList($key) {
    return array(
      array('id'=>'1', 'quantity'=>'10'),
      array('id'=>'2', 'quantity'=>'5'),
      array('id'=>'3', 'quantity'=>'0')
    );
  }
}


