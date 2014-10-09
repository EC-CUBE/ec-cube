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
 * SC_Utils::sfErrorHeader()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfErrorHeaderTest extends Common_TestCase
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
  public function testSfErrorHeader_printフラグがONの場合_指定したメッセージが出力される()
  {
    global $GLOBAL_ERR; 
    $this->expectOutputString($GLOBAL_ERR . '<div id="errorHeader">ERROR MESSAGE</div>');
    SC_Utils::sfErrorHeader('ERROR MESSAGE', TRUE);
  }

  public function testSfErrorHeader_printフラグがOFFの場合_指定したメッセージがグローバル変数に格納される()
  {
    global $GLOBAL_ERR;
    $this->expectOutputString('');
    $old_err = $GLOBAL_ERR;
    SC_Utils::sfErrorHeader('ERROR MESSAGE');
    $this->expected = $old_err . '<div id="errorHeader">ERROR MESSAGE</div>';
    $this->actual = $GLOBAL_ERR;

    $this->verify();
  }

  //////////////////////////////////////////
}

