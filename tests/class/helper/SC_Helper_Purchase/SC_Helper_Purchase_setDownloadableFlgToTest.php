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
 * SC_Helper_Purchase::setDownloadableFlgTo()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_setDownloadableFlgToTest extends SC_Helper_Purchase_TestBase {


  protected function setUp() {
    // parent::setUp();
  }

  protected function tearDown() {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSetDownloadableFlgTo_販売価格が0円の場合_フラグがONになる() {
    $input = array(
      '1001' => array('price' => 0)
    );
    
    $this->expected = true;
    SC_Helper_Purchase::setDownloadableFlgTo($input);
    $this->actual = $input['1001']['is_downloadable'];

    $this->verify('ダウンロード可能フラグ設定結果');
  }

  public function testSetDownloadableFlgTo_ダウンロード期限内かつ入金日ありの場合_フラグがONになる() {
    $input = array(
      '1001' => array('price' => 1000, 'effective' => '1', 'payment_date' => '2012-12-12')
    );
    
    $this->expected = true;
    SC_Helper_Purchase::setDownloadableFlgTo($input);
    $this->actual = $input['1001']['is_downloadable'];

    $this->verify('ダウンロード可能フラグ設定結果');
  }

  public function testSetDownloadableFlgTo_ダウンロード期限内かつ入金日なしの場合_フラグがOFFになる() {
    $input = array(
      '1001' => array('price' => 1000, 'effective' => '1', 'payment_date' => null)
    );
    
    $this->expected = false;
    SC_Helper_Purchase::setDownloadableFlgTo($input);
    $this->actual = $input['1001']['is_downloadable'];

    $this->verify('ダウンロード可能フラグ設定結果');
  }

  public function testSetDownloadableFlgTo_ダウンロード期限外かつ入金日ありの場合_フラグがOFFになる() {
    $input = array(
      '1001' => array('price' => 1000, 'effective' => '0', 'payment_date' => '2012-12-12')
    );
    
    $this->expected = false;
    SC_Helper_Purchase::setDownloadableFlgTo($input);
    $this->actual = $input['1001']['is_downloadable'];

    $this->verify('ダウンロード可能フラグ設定結果');
  }

  public function testSetDownloadableFlgTo_ダウンロード期限外かつ入金日なしの場合_フラグがOFFになる() {
    $input = array(
      '1001' => array('price' => 1000, 'effective' => '0', 'payment_date' => null)
    );
    
    $this->expected = false;
    SC_Helper_Purchase::setDownloadableFlgTo($input);
    $this->actual = $input['1001']['is_downloadable'];

    $this->verify('ダウンロード可能フラグ設定結果');
  }


  //////////////////////////////////////////

}

