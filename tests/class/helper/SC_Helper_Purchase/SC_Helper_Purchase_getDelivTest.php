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
 * SC_Helper_Purchaset::getDeliv()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_getDelivTest extends SC_Helper_Purchase_TestBase {


  protected function setUp() {
    parent::setUp();
    $this->setUpDeliv();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testGetDeliv_存在しない商品種別IDを指定した場合_結果が空になる() {
    $product_type_id = '9999'; // 存在しないID

    $this->expected = array();
    $helper = new SC_Helper_Purchase();
    $this->actual = $helper->getDeliv($product_type_id);

    $this->verify('配送業者');
  }

  public function testGetDeliv_存在する商品種別IDを指定した場合_結果が正しい順序で取得できる() {
    $product_type_id = '1001';
  
    $this->expected['count'] = 2;
    $this->expected['first'] = array(
      'deliv_id' => '1002',
      'product_type_id' => '1001',
      'name' => '配送業者02',
      'rank' => '3'
    );
    $this->expected['second'] = array(
      'deliv_id' => '1001',
      'product_type_id' => '1001',
      'name' => '配送業者01',
      'rank' => '2'
    );

    $helper = new SC_Helper_Purchase();
    $result = $helper->getDeliv($product_type_id);
    $this->actual['count'] = count($result);
    $cols = array('deliv_id', 'product_type_id', 'name', 'rank');
    $this->actual['first'] = Test_Utils::mapArray($result[0], $cols);
    $this->actual['second'] = Test_Utils::mapArray($result[1], $cols);

    $this->verify('配送業者');
  }

  //////////////////////////////////////////

}

