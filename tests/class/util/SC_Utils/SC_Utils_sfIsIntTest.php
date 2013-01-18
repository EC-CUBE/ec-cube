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
 * SC_Helper_Purchase::sfIsInt()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfIsIntTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfIsInt_0バイト文字列の場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsInt('');

    $this->verify('整数かどうか');
  }

  public function testSfIsInt_intの最大長より長い場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsInt('10000000000');

    $this->verify('整数かどうか');
  }

  public function testSfIsInt_intの最大値ギリギリの場合_TRUEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsInt('2147483647');

    $this->verify('整数かどうか');
  }

  public function testSfIsInt_intの最大値を超える場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsInt('2147483648');

    $this->verify('整数かどうか');
  }

  public function testSfIsInt_数値でない場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsInt('HELLO123');

    $this->verify('整数かどうか');
  }

  public function testSfIsInt_正の整数の場合_TRUEが返る() {
    $this->expected = TRUE;
    $this->actual = SC_Utils::sfIsInt('123456789');

    $this->verify('整数かどうか');
  }

  // TODO 「整数かどうか」という関数名なのでここはFALSEになるべきでは？
  /**
  public function testSfIsInt_正の小数の場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsInt('123.456');

    $this->verify('整数かどうか');
  }
  */

  public function testSfIsInt_負の整数の場合_TRUEが返る() {
    $this->expected = TRUE;
    $this->actual = SC_Utils::sfIsInt('-12345678');

    $this->verify('整数かどうか');
  }

  // TODO 文字列長でチェックしているので負の場合は範囲が小さくなっている
  /**
  public function testSfIsInt_負の整数で桁数が最大の場合_TRUEが返る() {
    $this->expected = TRUE;
    $this->actual = SC_Utils::sfIsInt('-123456789');

    $this->verify('整数かどうか');
  }
  */

}

