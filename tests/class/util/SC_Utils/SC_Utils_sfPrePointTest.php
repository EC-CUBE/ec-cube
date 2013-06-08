<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
// テスト用にデフォルトの丸め方法を指定
define('POINT_RULE', 1); // 四捨五入
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
 * SC_Utils::sfPrePoint()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfPrePointTest extends Common_TestCase
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
  public function testSfPrePoint_四捨五入の設定の場合_四捨五入された値が返る()
  {
    $rule = 1; // 四捨五入

    $this->expected = 10;
    $this->actual = SC_Utils::sfPrePoint(100, 9.5, $rule);

    $this->verify();
  }

  public function testSfPrePoint_切り捨ての設定の場合_切り捨てされた値が返る()
  {
    $rule = 2; // 切り捨て 

    $this->expected = 9;
    $this->actual = SC_Utils::sfPrePoint(100, 9.5, $rule);

    $this->verify();
  }

  public function testSfPrePoint_切り上げの設定の場合_切り上げされた値が返る()
  {
    $rule = 3; // 切り上げ

    $this->expected = 10;
    $this->actual = SC_Utils::sfPrePoint(100, 9.4, $rule);

    $this->verify();
  }

  public function testSfPrePoint_存在しない選択肢の場合_切り上げされた値が返る()
  {
    $rule = 4; // 存在しない選択肢

    $this->expected = 10;
    $this->actual = SC_Utils::sfPrePoint(100, 9.4, $rule);

    $this->verify();
  }

  public function testSfPrePoint_丸め方法の指定がない場合_定数で指定された値が使われる()
  {
    $this->expected = array(9, 9);
    $this->actual = array(
      SC_Utils::sfPrePoint(100, 9.4),
      SC_Utils::sfPrePoint(100, 9.5)
    );

    $this->verify();
  }

  //////////////////////////////////////////
}

