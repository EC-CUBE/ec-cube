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
 * SC_Utils::isBlank()のテストクラス.
 * 元々test/class/以下にあったテストを移行しています.
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_isBlankTest extends Common_TestCase {


  protected function setUp() {
    // parent::setUp();
  }

  protected function tearDown() {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  public function testIsBlank_0バイト文字列の場合_trueが返る() {
    $input = '';
    $this->assertTrue(SC_Utils::isBlank($input), $input);
  }

  public function testIsBlank_全角スペースの場合_trueが返る() {
    $input = '　';
    $this->assertTrue(SC_Utils::isBlank($input), $input);
  }

  public function testIsBlank_greedy指定なしで全角スペースの場合_falseが返る() {
    $input = '　';
    $this->assertFalse(SC_Utils::isBlank($input, false), $input);
  }

  public function testIsBlank_空の配列の場合_trueが返る() {
    $input = array();
    $this->assertTrue(SC_Utils::isBlank($input), $input);
  }

  public function testIsBlank_ネストした配列の場合_trueが返る() {
    $input = array(array(array()));
    $this->assertTrue(SC_Utils::isBlank($input), $input);
  }

  public function testIsBlank_greedy指定なしでネストした配列の場合_falseが返る() {
    $input = array(array(array()));
    $this->assertFalse(SC_Utils::isBlank($input, false), $input);
  }

  public function testIsBlank_空でない配列の場合_falseが返る() {
    $input = array(array(array('1')));
    $this->assertFalse(SC_Utils::isBlank($input), $input);
  }

  public function testIsBlank_greedy指定なしで空でない配列の場合_falseが返る() {
    $input = array(array(array('1')));
    $this->assertFalse(SC_Utils::isBlank($input, false), $input);
  }

  public function testIsBlank_全角スペースと空白の組み合わせの場合_trueが返る() {
    $input = "　\n　";
    $this->assertTrue(SC_Utils::isBlank($input), $input);
  }

  public function testIsBlank_greedy指定なしで全角スペースと空白の組み合わせの場合_falseが返る() {
    $input = "　\n　";
    $this->assertFalse(SC_Utils::isBlank($input, false), $input);
  }

  public function testIsBlank_全角スペースと非空白の組み合わせの場合_falseが返る() {
    $input = '　A　';
    $this->assertFalse(SC_Utils::isBlank($input), $input);
  }

  public function testIsBlank_greedy指定なしで全角スペースと非空白の組み合わせの場合_falseが返る() {
    $input = '　A　';
    $this->assertFalse(SC_Utils::isBlank($input, false), $input);
  }

  public function testIsBlank_数値のゼロを入力した場合_falseが返る() {
    $input = 0;
    $this->assertFalse(SC_Utils::isBlank($input), $input);
  }

  public function testIsBlank_値が空の配列を入力した場合_trueが返る() {
    $input = array("");
    $this->assertTrue(SC_Utils::isBlank($input), $input);
  }

  public function testIsBlank_すべてのホワイトスペースを並べた場合_trueが返る() {
    $input = " \t　\n\r\x0B\0";
    $this->assertTrue(SC_Utils::isBlank($input), $input);
  }

  public function testIsBlank_通常の文字が含まれている場合_falseが返る() {
    $input = " AB \n\t";
    $this->assertFalse(SC_Utils::isBlank($input), $input);
  }

  //////////////////////////////////////////

}

