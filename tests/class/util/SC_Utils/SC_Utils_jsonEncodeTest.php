<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
 * SC_Utils::jsonEncode()のテストクラス.
 * 環境によるfunctionの変更まではカバーできないため、簡単な出力のみテスト.
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_jsonEncodeTest extends Common_TestCase
{


  protected function setUp()
  {
    // parent::setUp();
  }

  protected function tearDown()
  {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  public function testJsonEncode__JSON形式にエンコードされた文字列が返る()
  {
    $input = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);
    $this->expected = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
    $this->actual = SC_Utils::jsonEncode($input);

    $this->verify();
  }

  //////////////////////////////////////////
}

