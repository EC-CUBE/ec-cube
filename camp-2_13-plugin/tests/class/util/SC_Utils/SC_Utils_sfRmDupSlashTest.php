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
 * SC_Utils::sfRmDupSlash()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfRmDupSlashTest extends Common_TestCase
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
  public function testSfRmDupSlash_非SSLの場合_スキーマ部分以外の重複スラッシュが1つになる()
  {
    $input = 'http://www.example.co.jp///aaa//bb/co.php';
    $this->expected = 'http://www.example.co.jp/aaa/bb/co.php';
    $this->actual = SC_Utils::sfRmDupSlash($input);

    $this->verify();
  }

  public function testSfRmDupSlash_SSLの場合_スキーマ部分以外の重複スラッシュが1つになる()
  {
    $input = 'https://www.example.co.jp///aaa//bb/co.php';
    $this->expected = 'https://www.example.co.jp/aaa/bb/co.php';
    $this->actual = SC_Utils::sfRmDupSlash($input);

    $this->verify();
  }

  public function testSfRmDupSlash_上記以外の場合_すべての重複スラッシュが1つになる()
  {
    $input = 'hoge//www.example.co.jp///aaa//bb/co.php';
    $this->expected = 'hoge/www.example.co.jp/aaa/bb/co.php';
    $this->actual = SC_Utils::sfRmDupSlash($input);

    $this->verify();
  }

  //////////////////////////////////////////

}

