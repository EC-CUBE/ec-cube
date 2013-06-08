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
 * SC_Utils::sfSwapArray()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfSwapArrayTest extends Common_TestCase
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
  public function testSfSwapArray_カラム名ありの指定の場合_キーに名称が入る()
  {
    $input_array = array(
      array('id' => '1001', 'name' => 'name1001'),
      array('id' => '1002', 'name' => 'name1002')
    );

    $this->expected = array(
      'id' => array('1001', '1002'),
      'name' => array('name1001', 'name1002')
    );
    $this->actual = SC_Utils::sfSwapArray($input_array);

    $this->verify();
  }

  public function testSfSwapArray_カラム名なしの指定の場合_キーに名称が入らない()
  {
    $input_array = array(
      array('id' => '1001', 'name' => 'name1001'),
      array('id' => '1002', 'name' => 'name1002')
    );

    $this->expected = array(
      array('1001', '1002'),
      array('name1001', 'name1002')
    );
    $this->actual = SC_Utils::sfSwapArray($input_array, false);

    $this->verify();
  }

  //////////////////////////////////////////
}

