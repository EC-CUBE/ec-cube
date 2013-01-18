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
 * SC_Helper_Purchase::saveShippingTemp()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_saveShippingTempTest extends SC_Helper_Purchase_TestBase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSaveShippingTemp_元々存在しない配送先IDの場合_新規に配列が作られる() {
    $this->setUpShipping($this->getMultipleShipping());

    SC_Helper_Purchase::saveShippingTemp(
      array('shipment_item' => '商品4', 'shipping_pref' => '大阪府')
    );

    $this->expected = array(
      'count'=>4,                // 配送情報全体の件数 
      'shipping_id'=>0,
      'shipment_item'=>null,
      'shipping_pref'=>'大阪府'
    );
    $this->actual['count'] = count($_SESSION['shipping']);
    $this->actual['shipping_id'] = $_SESSION['shipping']['0']['shipping_id'];
    $this->actual['shipment_item'] = $_SESSION['shipping']['0']['shipment_item'];
    $this->actual['shipping_pref'] = $_SESSION['shipping']['0']['shipping_pref'];

    $this->verify('登録した配送情報');
  }

  public function testSaveShippingTemp_元々存在する配送先IDの場合_情報がマージされる() {
    $this->setUpShipping($this->getMultipleShipping());

    SC_Helper_Purchase::saveShippingTemp(
      array('shipment_item' => '商品4', 'shipping_pref' => '大阪府'),
      '00001'
    );

    $this->expected = array(
      'count'=>3,                // 配送情報全体の件数 
      'shipping_id'=>'00001',
      'shipment_item'=>array('商品1'),
      'shipping_pref'=>'大阪府'
    );
    $this->actual['count'] = count($_SESSION['shipping']);
    $this->actual['shipping_id'] = $_SESSION['shipping']['00001']['shipping_id'];
    $this->actual['shipment_item'] = $_SESSION['shipping']['00001']['shipment_item'];
    $this->actual['shipping_pref'] = $_SESSION['shipping']['00001']['shipping_pref'];
    
    $this->verify('更新した配送情報');
  }

}

