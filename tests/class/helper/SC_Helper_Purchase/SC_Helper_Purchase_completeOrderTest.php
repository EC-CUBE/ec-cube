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
 * SC_Helper_Purchase::completeOrder()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_completeOrderTest extends SC_Helper_Purchase_TestBase {

  private $helper;

  protected function setUp() {
    parent::setUp();
    $this->setUpOrder();
    $this->setUpOrderTemp(); // order_temp_id = '1001'
    $this->setUpShipping();
    $this->setUpCustomer();

    $_SESSION['cartKey'] = '1';
    $_SESSION['site'] = array(
      'pre_page' => 'pre',
      'now_page' => 'now',
      'regist_success' => TRUE,
      'uniqid' => '1001'
    );

    $this->helper = new SC_Helper_Purchase_completeOrderMock();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  // 適切なfunctionが呼ばれていることのみ確認
  public function testCompleteOrder_顧客IDが指定されている場合_購入日が更新される() {
    $_SESSION['customer']['customer_id'] = '1002'; // 顧客ID
    $this->helper->completeOrder(ORDER_DELIV);

    $this->expected = array(
      'verifyChangeCart' => array(
        'uniqId' => '1001'
      ),
      'getOrderTemp' => array(
        'uniqId' => '1001'
      ),
      'registerOrderComplete' => array(
        'order_temp_id' => '1001',
        'status' => ORDER_DELIV,
        'cartKey' => '1'
      ),
      'registerShipmentItem' => array(
        array(
          'order_id' => '1001',
          'shipping_id' => '00001',
          'shipment_item' => '商品1'
        )
      ),
      'registerShipping' => array(
        'order_id' => '1001'
      ),
      'cleanupSession' => array(
        'order_id' => '1001',
        'cartKey' => '1'
      )
    );
    $this->actual = $_SESSION['testResult'];
    $this->verify('適切なfunctionが呼ばれている');
    $last_buy_date = $this->objQuery->get('last_buy_date', 'dtb_customer', 'customer_id = ?', '1002');
    $this->assertNotNull($last_buy_date, '最終購入日');
  }

  public function testCompleteOrder_顧客IDが指定されていない場合_特にエラーなく修了できる() {
    $this->helper->completeOrder(); // デフォルトのステータス(NEW)

    $this->expected = array(
      'verifyChangeCart' => array(
        'uniqId' => '1001'
      ),
      'getOrderTemp' => array(
        'uniqId' => '1001'
      ),
      'registerOrderComplete' => array(
        'order_temp_id' => '1001',
        'status' => ORDER_NEW,
        'cartKey' => '1'
      ),
      'registerShipmentItem' => array(
        array(
          'order_id' => '1001',
          'shipping_id' => '00001',
          'shipment_item' => '商品1'
        )
      ),
      'registerShipping' => array(
        'order_id' => '1001'
      ),
      'cleanupSession' => array(
        'order_id' => '1001',
        'cartKey' => '1'
      )
    );
    $this->actual = $_SESSION['testResult'];
    $this->verify('適切なfunctionが呼ばれている');
  }

  //////////////////////////////////////////

}

class SC_Helper_Purchase_completeOrderMock extends SC_Helper_Purchase{

  function verifyChangeCart($uniqId, $objCartSession){
    $_SESSION['testResult']['verifyChangeCart'] = array('uniqId'=>$uniqId);
  }

  function getOrderTemp($uniqId) {
    $_SESSION['testResult']['getOrderTemp'] = array('uniqId'=>$uniqId);
    return parent::getOrderTemp($uniqId);
  }

  function registerOrderComplete($orderTemp, $objCartSession, $cartKey) {
    $_SESSION['testResult']['registerOrderComplete'] = array(
      'order_temp_id' => $orderTemp['order_temp_id'],
      'status' => $orderTemp['status'],
      'cartKey' => $cartKey
    );
    return parent::registerOrderComplete($orderTemp, $objCartSession, $cartKey);
  }

  function registerShipmentItem($order_id, $shipping_id, $shipment_item) {
    $_SESSION['testResult']['registerShipmentItem'][] = array(
      'order_id' => $order_id,
      'shipping_id' => $shipping_id,
      'shipment_item' => $shipment_item
    );
  }

  function registerShipping($order_id, $shipping_temp) {
    $_SESSION['testResult']['registerShipping'] = array(
      'order_id' => $order_id
    );
  }

  function cleanupSession($order_id, $objCartSesion, $objCustomer, $cartKey) {
    $_SESSION['testResult']['cleanupSession'] = array(
      'order_id' => $order_id,
      'cartKey' => $cartKey
    );
  }

}

?>

