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
 * SC_Utils::clearCompliedTemplate()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_clearCompliedTemplateTest extends Common_TestCase {


  protected function setUp() {
    // parent::setUp();
  }

  protected function tearDown() {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  public function test__コンパイル済みのファイルを配置するディレクトリが空になる() {
    SC_Utils::clearCompliedTemplate();

    $this->expected = array();
    $this->actual = array();
    Test_Utils::array_append($this->actual, COMPILE_REALDIR);
    Test_Utils::array_append($this->actual, COMPILE_ADMIN_REALDIR);
    Test_Utils::array_append($this->actual, SMARTPHONE_COMPILE_REALDIR);
    Test_Utils::array_append($this->actual, MOBILE_COMPILE_REALDIR);

    $this->verify('コンパイル済みファイルの格納先の中身');
  }

  //////////////////////////////////////////

}

