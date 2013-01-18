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
 * SC_Helper_Purchase::sfGetRandomString()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfGetRandomStringTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  // ランダムな文字列取得なので、文字列長のみ確認します。
  public function testSfGetRandomString_文字列長未指定の場合_長さ1の文字列が取得できる() {
    $this->expected = 1;
    $this->actual = strlen(SC_Utils::sfGetRandomString());
    
    $this->verify('文字列長');
  }

  public function testSfGetRandomString_文字列長指定ありの場合_指定した長さの文字列が取得できる() {
    $this->expected = 10;
    $this->actual = strlen(SC_Utils::sfGetRandomString(10));
    
    $this->verify('文字列長');
  }

}

