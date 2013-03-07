<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
// このテスト専用の定数の設定
define('AUTH_TYPE', 'HMAC');
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
 * SC_Utils::sfGetHashString()のテストクラス.
 * 暗号化結果の詳細までは見られないため、変換がされていることだけを確かめます。
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfGetHashString_authTypeHmacTest extends Common_TestCase
{


  protected function setUp()
  {
    parent::setUp();
  }

  protected function tearDown()
  {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfGetHashString_暗号化ありの設定になっている場合_文字列が変換される()
  {
    $input = 'hello, world';
    $salt = 'sample';

    $output = SC_Utils::sfGetHashString($input, $salt);

    $this->assertNotEquals('入力前後の文字列', $input, $output);
  }

  public function testSfGetHashString_暗号化ありの設定になっていてsaltがない場合_文字列が変換される()
  {
    $input = 'hello, world';
    $output = SC_Utils::sfGetHashString($input);

    $this->assertNotEquals('入力前後の文字列', $input, $output);
  }

}

