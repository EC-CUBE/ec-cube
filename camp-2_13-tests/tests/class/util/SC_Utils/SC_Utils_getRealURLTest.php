<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
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
 * SC_Utils::getRealURL()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_getRealURLTest extends Common_TestCase
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
  // TODO ポート番号のためのコロンが必ず入ってしまうのはOK?
  public function testGetRealURL_親ディレクトリへの参照を含む場合_正しくパースできる()
  {
    $input = 'http://www.example.jp/aaa/../index.php';
    $this->expected = 'http://www.example.jp:/index.php';
    $this->actual = SC_Utils::getRealURL($input);

    $this->verify();
  }

  public function testGetRealURL_親ディレクトリへの参照を複数回含む場合_正しくパースできる()
  {
    $input = 'http://www.example.jp/aaa/bbb/../../ccc/ddd/../index.php';
    $this->expected = 'http://www.example.jp:/ccc/index.php';
    $this->actual = SC_Utils::getRealURL($input);

    $this->verify();
  }

  public function testGetRealURL_カレントディレクトリへの参照を含む場合_正しくパースできる()
  {
    $input = 'http://www.example.jp/aaa/./index.php';
    $this->expected = 'http://www.example.jp:/aaa/index.php';
    $this->actual = SC_Utils::getRealURL($input);

    $this->verify();
  }

  public function testGetRealURL_httpsの場合_正しくパースできる()
  {
    $input = 'https://www.example.jp/aaa/./index.php';
    $this->expected = 'https://www.example.jp:/aaa/index.php';
    $this->actual = SC_Utils::getRealURL($input);

    $this->verify();
  }
  //////////////////////////////////////////

}

