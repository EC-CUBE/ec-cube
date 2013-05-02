<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
// テスト用に背景色を設定
define('ERR_COLOR', 'blue');
require_once($HOME . "/tests/class/Common_TestCase.php");
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
 * SC_Utils::sfGetEnabled()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfGetEnabledTest extends Common_TestCase {


  protected function setUp() {
    // parent::setUp();
  }

  protected function tearDown() {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  // TODO 要確認 実際には使われていない?
  public function testSfGetEnabled_falseを指定した場合_無効化するための文字列が返る() {
    
    $this->expected = ' disabled="disabled"';
    $this->actual = SC_Utils::sfGetEnabled(false);

    $this->verify();
  }

  public function testSfGetEnabled_trueを指定した場合_0バイト文字列が返る() {
    
    $this->expected = '';
    $this->actual = SC_Utils::sfGetEnabled(true);

    $this->verify();
  }

  //////////////////////////////////////////

}

