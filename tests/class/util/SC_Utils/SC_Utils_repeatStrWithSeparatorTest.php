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
 * SC_Helper_Purchase::repeatStrWithSeparator()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_repeatStrWithSeparatorTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testRepeatStrWithSeparator_反復回数が0回の場合_結果が0バイト文字列になる() {
    $this->expected = '';
    $this->actual = SC_Utils::repeatStrWithSeparator('ECサイト', 0, '#');

    $this->verify('連結済みの文字列');
  }

  public function testRepeatStrWithSeparator_反復回数が1回の場合_区切り文字が入らない() {
    $this->expected = 'ECサイト';
    $this->actual = SC_Utils::repeatStrWithSeparator('ECサイト', 1, '#');

    $this->verify('連結済みの文字列');
  }

  public function testRepeatStrWithSeparator_反復回数が2回以上の場合_区切り文字が入って出力される() {
    $this->expected = 'ECサイト#ECサイト#ECサイト#ECサイト#ECサイト';
    $this->actual = SC_Utils::repeatStrWithSeparator('ECサイト', 5, '#');

    $this->verify('連結済みの文字列');
  }

  public function testRepeatStrWithSeparator_区切り文字が未指定の場合_カンマ区切りとなる() {
    $this->expected = 'ECサイト,ECサイト,ECサイト';
    $this->actual = SC_Utils::repeatStrWithSeparator('ECサイト', 3);

    $this->verify('連結済みの文字列');
  }

}

