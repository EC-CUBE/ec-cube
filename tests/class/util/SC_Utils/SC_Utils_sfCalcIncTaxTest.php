<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
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
 * SC_Helper_Purchase::sfCalcIncTax()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfCalcIncTaxTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfCalcIncTax_四捨五入の場合_四捨五入の結果になる() {
    $this->expected = array(141, 152);
    $this->actual[0] = SC_Utils::sfCalcIncTax(140, 1, 1); // 1:四捨五入
    $this->actual[1] = SC_Utils::sfCalcIncTax(150, 1, 1); // 1:四捨五入

    $this->verify('税込価格');
  }

  public function testSfCalcIncTax_切り捨ての場合_切り捨ての結果になる() {
    $this->expected = array(142, 153);
    $this->actual[0] = SC_Utils::sfCalcIncTax(140, 2, 2); // 2:切り捨て
    $this->actual[1] = SC_Utils::sfCalcIncTax(150, 2, 2); // 2:切り捨て

    $this->verify('税込価格');
  }

  public function testSfCalcIncTax_切り上げの場合_切り上げの結果になる() {
    $this->expected = array(142, 152);
    $this->actual[0] = SC_Utils::sfCalcIncTax(140, 1, 3); // 3:切り上げ
    $this->actual[1] = SC_Utils::sfCalcIncTax(150, 1, 3); // 3:切り上げ

    $this->verify('税込価格');
  }

  public function testSfCalcIncTax_それ以外の場合_切り上げの結果になる() {
    $this->expected = array(142, 152);
    $this->actual[0] = SC_Utils::sfCalcIncTax(140, 1, 4);
    $this->actual[1] = SC_Utils::sfCalcIncTax(150, 1, 4);

    $this->verify('税込価格');
  }

}

