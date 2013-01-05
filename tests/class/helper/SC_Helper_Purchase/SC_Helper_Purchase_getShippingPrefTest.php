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
 * SC_Helper_Purchase::getShippingPref()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_getShippingPrefTest extends SC_Helper_Purchase_TestBase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  // TODO 要確認：引数の名前がおかしい（is_multipleではないはず）
  public function testGetShippingPref_保有フラグがOFFの場合_全配送情報を取得する() {
    $this->setUpShipping($this->getMultipleShipping());

    $this->expected = array('東京都', '沖縄県', '埼玉県');
    $this->actual = SC_Helper_Purchase::getShippingPref();

    $this->verify('配送先の都道府県');
  }

  public function testGetShippingPref_保有フラグがONの場合_商品のある配送情報のみ取得する() {
    $this->setUpShipping($this->getMultipleShipping());

    $this->expected = array('東京都', '沖縄県');
    $this->actual = SC_Helper_Purchase::getShippingPref(TRUE);

    $this->verify('配送先の都道府県');
  }

}

