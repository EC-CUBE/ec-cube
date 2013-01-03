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
 * SC_Utils::sfPrintR()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfPrintRTest extends Common_TestCase {


  protected function setUp() {
    // parent::setUp();
  }

  protected function tearDown() {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfPrintR__指定したオブジェクトの情報が出力される() {
    $output = '<div style="font-size: 12px;color: #00FF00;">' . "\n"
      . '<strong>**デバッグ中**</strong><br />' . "\n"
      . '<pre>' . "\n"
      . 'array(1) {' . "\n"
      . '  ["test"]=>' . "\n"
      . '  string(4) "TEST"' . "\n"
      . '}' . "\n"
      . '</pre>' . "\n"
      . '<strong>**デバッグ中**</strong></div>' . "\n";

    $this->expectOutputString($output);
    SC_Utils::sfPrintR(array('test'=>'TEST'));
  }

  //////////////////////////////////////////

}

