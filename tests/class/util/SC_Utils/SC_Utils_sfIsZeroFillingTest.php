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
 * SC_Helper_Purchase::sfIsZeroFilling()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfIsZeroFillingTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfIsZeroFilling_桁数が1の場合_FALSEを返す() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsZeroFilling('0');

    $this->verify('ゼロ詰めされているかどうか');
  }

  public function testSfIsZeroFilling_桁数が2以上で0埋めされていない場合_FALSEを返す() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsZeroFilling('12');

    $this->verify('ゼロ詰めされているかどうか');
  }

  public function testSfIsZeroFilling_桁数が2以上で0埋めされている場合_TRUEを返す() {
    $this->expected = TRUE;
    $this->actual = SC_Utils::sfIsZeroFilling('01');

    $this->verify('ゼロ詰めされているかどうか');
  }

}

