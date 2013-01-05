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
 * SC_Utils::sfGetSearchPageMax()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfGetSearchPageMaxTest extends Common_TestCase {


  protected function setUp() {
    // parent::setUp();
  }

  protected function tearDown() {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfGetSearchPageMax_正の整数が指定されている場合_指定の値が返る() {
    
    $this->expected = 100;
    $this->actual = SC_Utils::sfGetSearchPageMax(100);

    $this->verify();
  }

  public function testSfGetSearchPageMax_正の小数が指定されている場合_整数に変換される() {
    
    $this->expected = 99;
    $this->actual = SC_Utils::sfGetSearchPageMax(99.5);

    $this->verify();
  }

  public function testSfGetSearchPageMax_負の数が指定されている場合_デフォルト値が返る() {
    
    $this->expected = SEARCH_PMAX;
    $this->actual = SC_Utils::sfGetSearchPageMax(-50);

    $this->verify();
  }

  public function testSfGetSearchPageMax_指定がない場合_デフォルト値が返る() {
    
    $this->expected = SEARCH_PMAX;
    $this->actual = SC_Utils::sfGetSearchPageMax();

    $this->verify();
  }

  //////////////////////////////////////////

}

