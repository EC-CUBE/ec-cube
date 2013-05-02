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
 * SC_Helper_Purchase::registerOrder()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_registerOrderTest extends SC_Helper_Purchase_TestBase {

  private $helper;

  protected function setUp() {
    parent::setUp();
    $this->setUpOrder();
    $this->helper = new SC_Helper_Purchase_registerOrderMock();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testRegisterOrder_既に受注IDが存在する場合_情報が更新される() {
    $order_id = '1001';
    $arrParams = array(
      'status' => '1',
      'add_point' => 10,
      'use_point' => 20,
      'order_name01' => '受注情報01_更新'
    );

    $this->helper->registerOrder($order_id, $arrParams);

    $this->expected = array(
      'sfUpdateOrderStatus' => array(
        'order_id' => '1001',
        'status' => '1',
        'add_point' => 10,
        'use_point' => 20
      ),
      'sfUpdateOrderNameCol' => '1001',
      'count' => '2',
      'content' => array(
        'order_id' => '1001',
        'customer_id' => '1001',
        'status' => '1',
        'add_point' => '10',
        'use_point' => '20',
        'order_name01' => '受注情報01_更新'
      )
    );
    $this->actual = $_SESSION['testResult'];
    $this->actual['count'] = $this->objQuery->count('dtb_order');
    $result = $this->objQuery->select(
      'order_id, customer_id, status, order_name01, add_point, use_point',
      'dtb_order',
      'order_id = ?',
      array($order_id)
    );
    $this->actual['content'] = $result[0];

    $this->verify();
  }

  public function testRegisterOrder_存在しない受注IDを指定した場合_新規に登録される() {
    $order_id = '1003';
    $arrParams = array(
      'customer_id' => '1003',
      'status' => '2',
      'add_point' => 100,
      'use_point' => 200,
      'order_name01' => '受注情報03'
    );

    $this->helper->registerOrder($order_id, $arrParams);

    $this->expected = array(
      'sfUpdateOrderStatus' => array(
        'order_id' => '1003',
        'status' => '2',
        'add_point' => 100,
        'use_point' => 200
      ),
      'sfUpdateOrderNameCol' => '1003',
      'count' => '3',
      'content' => array(
        'order_id' => '1003',
        'customer_id' => '1003',
        'status' => null,         // ここではsfUpdateOrderStatusをモックにしているので更新されない
        'add_point' => '100',
        'use_point' => '200',
        'order_name01' => '受注情報03'
      )
    );
    $this->actual = $_SESSION['testResult'];
    $this->actual['count'] = $this->objQuery->count('dtb_order');
    $result = $this->objQuery->select(
      'order_id, customer_id, status, order_name01, add_point, use_point',
      'dtb_order',
      'order_id = ?',
      array($order_id)
    );
    $this->actual['content'] = $result[0];

    $this->verify();
  }

  public function testRegisterOrder_受注IDが未指定の場合_新たにIDが発行される() {
    $order_id = '';
    $arrParams = array( // 顧客IDも未指定
      'status' => '2',
      'add_point' => 100,
      'use_point' => 200,
      'order_name01' => '受注情報03'
    );

    // SEQの値を取得
    $new_order_id = $this->helper->getNextOrderID() + 1;

    $this->helper->registerOrder($order_id, $arrParams);

    $this->expected = array(
      'sfUpdateOrderStatus' => array(
        'order_id' => $new_order_id,
        'status' => '2',
        'add_point' => 100,
        'use_point' => 200
      ),
      'sfUpdateOrderNameCol' => $new_order_id,
      'count' => '3',
      'content' => array(
        'order_id' => $new_order_id,
        'customer_id' => '0',
        'status' => null,         // ここではsfUpdateOrderStatusをモックにしているので更新されない
        'add_point' => '100',
        'use_point' => '200',
        'order_name01' => '受注情報03'
      )
    );
    $this->actual = $_SESSION['testResult'];
    $this->actual['count'] = $this->objQuery->count('dtb_order');
    $result = $this->objQuery->select(
      'order_id, customer_id, status, order_name01, add_point, use_point',
      'dtb_order',
      'order_id = ?',
      array($new_order_id)
    );
    $this->actual['content'] = $result[0];

    $this->verify();

  }

  //////////////////////////////////////////

}

class SC_Helper_Purchase_registerOrderMock extends SC_Helper_Purchase {
  function sfUpdateOrderStatus($order_id, $status, $add_point, $use_point, $values) {
    $_SESSION['testResult']['sfUpdateOrderStatus'] = array(
      'order_id' => $order_id,
      'status' => $status,
      'add_point' => $add_point,
      'use_point' => $use_point,
    );
  }

  function sfUpdateOrderNameCol($order_id) {
   $_SESSION['testResult']['sfUpdateOrderNameCol'] = $order_id;
  }
}

