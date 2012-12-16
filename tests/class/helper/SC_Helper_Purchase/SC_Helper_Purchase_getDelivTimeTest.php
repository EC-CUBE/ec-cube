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
 * SC_Helper_Purchaset::getDelivTime()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_getDelivTimeTest extends SC_Helper_Purchase_TestBase {


  protected function setUp() {
    parent::setUp();
    $this->setUpDelivTime();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testGetDelivTime_存在しない配送業者IDを指定した場合_結果が空になる() {
    $deliv_id = '100'; // 存在しないID

    $this->expected = array();
    $helper = new SC_Helper_Purchase();
    $this->actual = $helper->getDelivTime($deliv_id);

    $this->verify('お届け時間');
  }

  public function testGetDelivTime_存在する配送業者IDを指定した場合_結果が正しい順序で取得できる() {
    $deliv_id = '1001';
  
    $this->expected = array(
      '1' => '午前',
      '2' => '午後'
    );

    $helper = new SC_Helper_Purchase();
    $this->actual = $helper->getDelivTime($deliv_id);
    
    $this->verify('お届け時間');
  }

  //////////////////////////////////////////

}

