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
 * SC_Helper_Purchase::getShippingTemp()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_getShippingTempTest extends SC_Helper_Purchase_TestBase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testGetShippingTemp_保有フラグがOFFの場合_セッション情報を取得する() {
    $this->setUpShipping($this->getMultipleShipping());

    $this->expected = $this->getMultipleShipping();
    $this->actual = SC_Helper_Purchase::getShippingTemp();

    $this->verify('配送情報');
  }

  public function testGetShippingTemp_保有フラグがONの場合_商品のある情報のみ取得する() {
    $this->setUpShipping($this->getMultipleShipping());

    $this->expected = array(
      '00001' => array(
        'shipment_id' => '00001',
        'shipment_item' => array('商品1'),
        'shipping_pref' => '東京都'),
      '00002' => array(
        'shipment_id' => '00002',
        'shipment_item' => array('商品2'),
        'shipping_pref' => '沖縄県')
    );
    $this->actual = SC_Helper_Purchase::getShippingTemp(TRUE);

    $this->verify('配送情報');
  }

}

