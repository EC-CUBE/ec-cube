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
 * SC_Utils::sfArrKeyValue()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfArrKeyValueTest extends Common_TestCase
{

  var $arrList;
  var $keyname;
  var $valuename;

  protected function setUp()
  {
    // parent::setUp();

    $this->arrList = array(
      array('testkey' => '1011', 'testvalue' => '2001', 'key' => '3001'),
      array('testkey' => '2022', 'testvalue' => '2002', 'key' => '3002'),
      array('testkey' => '3033', 'testvalue' => '2003', 'key' => '3003'),
      array('testkey' => '4044', 'testvalue' => '2004', 'key' => '3004')
    );
    $this->keyname = 'testkey';
    $this->valuename = 'testvalue';
  }

  protected function tearDown()
  {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfArrKeyValue_最大長が配列より短い場合_最大長でカットされる()
  {
    $len_max = 3;

    $this->expected = array(
      '1011' => '2001',
      '2022' => '2002',
      '3033' => '2003'
    );
    $this->actual = SC_Utils::sfArrKeyValue($this->arrList, $this->keyname, $this->valuename, $len_max);

    $this->verify();
  }

  public function testSfArrKeyValue_最大長が指定されていない場合_全要素が出力される()
  {
    $this->expected = array(
      '1011' => '2001',
      '2022' => '2002',
      '3033' => '2003',
      '4044' => '2004'
    );
    $this->actual = SC_Utils::sfArrKeyValue($this->arrList, $this->keyname, $this->valuename, $len_max);

    $this->verify();
  }

  public function testSfArrKeyValue_キーサイズが短い場合_キーサイズでカットされる()
  {
    $len_max = 5;
    $keysize = 1;

    $this->expected = array(
      '1...' => '2001',
      '2...' => '2002',
      '3...' => '2003',
      '4...' => '2004'
    );
    $this->actual = SC_Utils::sfArrKeyValue($this->arrList, $this->keyname, $this->valuename, $len_max, $keysize);

    $this->verify();
  }

  //////////////////////////////////////////

}

