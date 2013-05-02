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
 * SC_Utils::sfGetTimestamp()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfGetTimestampTest extends Common_TestCase {


  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfGetTimestamp_年が設定されていない場合_0バイト文字列が返る() {
    $year = '';
    $month = '10';
    $day = '23';

    $this->expected = '';
    $this->actual = SC_Utils::sfGetTimestamp($year, $month, $day);
    $this->verify();
  }

  public function testSfGetTimestamp_月が設定されていない場合_0バイト文字列が返る() {
    $year = '2012';
    $month = '';
    $day = '13';

    $this->expected = '';
    $this->actual = SC_Utils::sfGetTimestamp($year, $month, $day);
    $this->verify();
  }

  public function testSfGetTimestamp_日が設定されていない場合_0バイト文字列が返る() {
    $year = '1999';
    $month = '09';
    $day = '';

    $this->expected = '';
    $this->actual = SC_Utils::sfGetTimestamp($year, $month, $day);
    $this->verify();
  }

  public function testSfGetTimestamp_年月日すべて設定されている場合_連結された文字列が返る() {
    $year = '1999';
    $month = '09';
    $day = '23';

    $this->expected = '1999-09-23 00:00:00';
    $this->actual = SC_Utils::sfGetTimestamp($year, $month, $day);

    $this->verify();
  }

  public function testSfGetTimestamp_最終時刻フラグがONの場合_時刻が深夜のものになる() {
    $year = '1999';
    $month = '09';
    $day = '23';

    $this->expected = '1999-09-23 23:59:59';
    $this->actual = SC_Utils::sfGetTimestamp($year, $month, $day, true);

    $this->verify();
  }

  //////////////////////////////////////////

}

