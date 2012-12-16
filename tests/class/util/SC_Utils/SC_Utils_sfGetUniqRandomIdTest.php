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
 * SC_Utils::sfGetUniqRandomId()のテストクラス.
 * ランダムな生成結果をすべてテストすることはできないため,
 * 文字数とランダム性のみをチェックする.
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfGetUniqRandomIdTest extends Common_TestCase {


  protected function setUp() {
    // parent::setUp();
  }

  protected function tearDown() {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  public function test_prefix指定が無い場合_21文字のランダムな文字列が生成される() {
    $output1 = SC_Utils::sfGetUniqRandomId();
    $output2 = SC_Utils::sfGetUniqRandomId();

    $this->assertEquals(21, strlen($output1), '文字列1の長さ');
    $this->assertEquals(21, strlen($output2), '文字列2の長さ');
    $this->assertNotEquals($output1, $output2, '生成結果がランダムになる');
  }

  public function test_prefix指定がある場合_prefixのあとに21文字のランダムな文字列が生成される() {
    $output1 = SC_Utils::sfGetUniqRandomId('hello');
    $output2 = SC_Utils::sfGetUniqRandomId('hello');

    $this->assertEquals(26, strlen($output1), '文字列1の長さ');
    $this->assertEquals('hello', substr($output1, 0, 5), 'prefix1');
    $this->assertEquals(26, strlen($output2), '文字列2の長さ');
    $this->assertEquals('hello', substr($output2, 0, 5), 'prefix2');
    $this->assertNotEquals($output1, $output2, '生成結果がランダムになる');
  }

  //////////////////////////////////////////

}

