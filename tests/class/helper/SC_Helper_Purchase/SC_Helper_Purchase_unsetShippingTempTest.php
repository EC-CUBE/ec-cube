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
 * SC_Helper_Purchase::unsetShippingTemp()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_unsetShippingTempTest extends SC_Helper_Purchase_TestBase {

  protected function setUp() {
    parent::setUp();

    // 空にするだけなので適当な値を設定
    $_SESSION['shipping'] = 'temp01';
    $_SESSION['multiple_temp'] = 'temp02';
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testUnsetShippingTemp__配送情報がすべて破棄される() {
    SC_Helper_Purchase::unsetShippingTemp(TRUE);

    $this->expected = array('shipping'=>TRUE, 'multiple_temp'=>TRUE);
    $this->actual['shipping'] = empty($_SESSION['shipping']);
    $this->actual['multiple_temp'] = empty($_SESSION['multiple_temp']);

    $this->verify('セッション情報が空かどうか');
  }

}

