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
 * SC_Helper_Purchase::getPaymentsByPaymentsId()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_getPaymentsByPaymentsIdTest extends SC_Helper_Purchase_TestBase {


  protected function setUp() {
    parent::setUp();
    $this->setUpPayment();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testGetPaymentsByPaymentsId_存在しない支払IDを指定した場合_結果が空になる() {
    $payment_id = '9999'; // 存在しないID
  
    $this->expected = null;
    $helper = new SC_Helper_Purchase();
    $this->actual = $helper->getPaymentsByPaymentsId($payment_id);

    $this->verify('支払方法');
  }

  public function testGetPaymentsByPaymentsId_存在する支払IDを指定した場合_対応する支払方法の情報が取得できる() {
    $payment_id = '1001';
  
    $this->expected = array(
      'payment_id' => '1001',
      'payment_method' => '支払方法1001'
    );

    $helper = new SC_Helper_Purchase();
    $this->actual = $helper->getPaymentsByPaymentsId($payment_id);
    $this->actual = Test_Utils::mapArray($this->actual, array('payment_id', 'payment_method'));

    $this->verify('支払方法');
  }

  public function testGetPaymentsByPaymentsId_削除されている支払IDを指定した場合_結果が空になる() {
    $payment_id = '1002'; // 削除済みのID
  
    $this->expected = null;
    $helper = new SC_Helper_Purchase();
    $this->actual = $helper->getPaymentsByPaymentsId($payment_id);

    $this->verify('支払方法');
  }

  //////////////////////////////////////////

}

