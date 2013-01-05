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
 * SC_Helper_Purchase::clearShipmentItemTemp()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_clearShipmentItemTempTest extends SC_Helper_Purchase_TestBase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testClearShipmentItem_配送先ID未指定の場合_全ての配送商品がクリアされる() {
    $this->setUpShipping($this->getMultipleShipping());

    $helper = new SC_Helper_Purchase();
    $helper->clearShipmentItemTemp(); // default:null

    $this->expected = array('00001'=>null, '00002'=>null, '00003'=>null);
    $this->actual['00001'] = $_SESSION['shipping']['00001']['shipment_item'];
    $this->actual['00002'] = $_SESSION['shipping']['00002']['shipment_item'];
    $this->actual['00003'] = $_SESSION['shipping']['00003']['shipment_item'];

    $this->verify('配送商品');
  }

  public function testClearShipmentItem_配送先ID指定の場合_指定したIDの配送商品がクリアされる() {
    $this->setUpShipping($this->getMultipleShipping());

    $helper = new SC_Helper_Purchase();
    $helper->clearShipmentItemTemp('00001');

    $this->expected = array('00001'=>null, '00002'=>array('商品2'), '00003'=>array());
    $this->actual['00001'] = $_SESSION['shipping']['00001']['shipment_item'];
    $this->actual['00002'] = $_SESSION['shipping']['00002']['shipment_item'];
    $this->actual['00003'] = $_SESSION['shipping']['00003']['shipment_item'];

    $this->verify('配送商品');
  }

  public function testClearShipmentItem_存在しないIDを指定した場合_何も変更されない() {
    $this->setUpShipping($this->getMultipleShipping());

    $helper = new SC_Helper_Purchase();
    $helper->clearShipmentItemTemp('00004');

    $this->expected = array('00001'=>array('商品1'), '00002'=>array('商品2'), '00003'=>array());
    $this->actual['00001'] = $_SESSION['shipping']['00001']['shipment_item'];
    $this->actual['00002'] = $_SESSION['shipping']['00002']['shipment_item'];
    $this->actual['00003'] = $_SESSION['shipping']['00003']['shipment_item'];

    $this->verify('配送商品');
  }

  public function testClearShipmentItem_商品情報が配列でない場合_何も変更されない() {
    $this->setUpShipping($this->getMultipleShipping());
    // 内容を配列でないように変更
    $_SESSION['shipping']['00001'] = 'temp';

    $helper = new SC_Helper_Purchase();
    $helper->clearShipmentItemTemp('00001');

    // '00001'は配列でないので全体を取得
    $this->expected = array('00001'=>'temp', '00002'=>array('商品2'), '00003'=>array());
    $this->actual['00001'] = $_SESSION['shipping']['00001'];
    $this->actual['00002'] = $_SESSION['shipping']['00002']['shipment_item'];
    $this->actual['00003'] = $_SESSION['shipping']['00003']['shipment_item'];

    $this->verify('配送商品');
  }

}

