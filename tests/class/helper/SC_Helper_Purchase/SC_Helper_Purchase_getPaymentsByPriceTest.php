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
 * SC_Helper_Purchase::getPaymentsByPrice()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_getPaymentsByPriceTest extends SC_Helper_Purchase_TestBase {


  protected function setUp() {
    parent::setUp();
    $this->setUpPayment();
    $this->setUpPaymentOptions();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testGetPaymentsByPrice_購入金額がすべての上限を上回る場合_上限の設定がないものだけ取得できる() {
    $deliv_id = '1003';
    $total = 21001;
    $helper = new SC_Helper_Purchase();

    $this->expected['count'] = 2;
    $this->expected['first'] = array(
      'payment_id' => '3002',
      'payment_method' => '支払方法3002',
      'rule_max' => null,
      'upper_rule' => null,
      'note' => null,
      'payment_image' => null,
      'charge' => null
    );
    $this->expected['second_id'] = '3003';

    $result = $helper->getPaymentsByPrice($total, $deliv_id);
    $this->actual['count'] = count($result);
    $this->actual['first'] = $result[0];
    $this->actual['second_id'] = $result[1]['payment_id'];

    $this->verify();
  }

  public function testGetPaymentsByPrice_購入金額が一部の上限を上回る場合_上限に引っかからないものだけ取得できる() {
    $deliv_id = '1003';
    $total = 20500;
    $helper = new SC_Helper_Purchase();

    $this->expected = array('3002', '3003', '3005');

    $result = $helper->getPaymentsByPrice($total, $deliv_id);
    $this->actual = Test_Utils::mapCols($result, 'payment_id');

    $this->verify();
  }

  public function testGetPaymentsByPrice_購入金額が一部の下限を下回る場合_下限に引っかからないものだけ取得できる() {
    $deliv_id = '1003';
    $total = 11000;
    $helper = new SC_Helper_Purchase();

    $this->expected = array('3002', '3003', '3004');

    $result = $helper->getPaymentsByPrice($total, $deliv_id);
    $this->actual = Test_Utils::mapCols($result, 'payment_id');

    $this->verify();
  }

  public function testGetPaymentsByPrice_購入金額がすべての下限を下回る場合_下限の設定がないものだけ取得できる() {
    $deliv_id = '1003';
    $total = 9999;
    $helper = new SC_Helper_Purchase();

    $this->expected = array('3002', '3004');

    $result = $helper->getPaymentsByPrice($total, $deliv_id);
    $this->actual = Test_Utils::mapCols($result, 'payment_id');

    $this->verify();
  }


  //////////////////////////////////////////

}

