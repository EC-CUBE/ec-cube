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
 * SC_Helper_Purchase::getOrderTemp()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_getOrderTempTest extends SC_Helper_Purchase_TestBase {


  protected function setUp() {
    parent::setUp();
    $this->setUpOrderTemp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testGetOrderTemp_存在しない受注IDを指定した場合_結果が空になる() {
    $order_id = '9999';

    $this->expected = null;
    $this->actual = SC_Helper_Purchase::getOrderTemp($order_id);

    $this->verify();
  }

  public function testGetOrderTemp_存在する受注IDを指定した場合_対応する結果が取得できる() {
    $order_temp_id = '1002';

    $this->expected = array(
      'order_temp_id' => '1002',
      'customer_id' => '1002',
      'order_name01' => '受注情報02'
    );
    $result = SC_Helper_Purchase::getOrderTemp($order_temp_id);
    $this->actual = Test_Utils::mapArray($result, array('order_temp_id', 'customer_id', 'order_name01'));

    $this->verify();
  }

  //////////////////////////////////////////

}

