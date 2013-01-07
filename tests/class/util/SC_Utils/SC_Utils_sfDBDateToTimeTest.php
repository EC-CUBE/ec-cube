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
 * SC_Utils::sfDBDatetoTime()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfDBDatetoTimeTest extends Common_TestCase {


  protected function setUp() {
    // parent::setUp();
  }

  protected function tearDown() {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfDbDatetoTime_時刻にマイクロ秒が含まれている場合_マイクロ秒を無視して変換できる() {
    
    $this->expected = mktime(10, 20, 30, 10, 31, 2012);
    $this->actual = SC_Utils::sfDbDatetoTime('2012-10-31 10:20:30.000002');

    $this->verify();
  }

  public function testSfDbDatetoTime_時刻にマイクロ秒が含まれていない場合_そのまま変換できる() {
    
    $this->expected = mktime(10, 20, 30, 10, 31, 2012);
    $this->actual = SC_Utils::sfDbDatetoTime('2012-10-31 10:20:30');

    $this->verify();
  }

  //////////////////////////////////////////

}

