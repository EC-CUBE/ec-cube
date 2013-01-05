<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_TestBase.php");
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
 * SC_Helper_Purchase::isUsePoint()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_isUsePointTest extends SC_Helper_Purchase_TestBase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testIsUsePoint_ステータスがnullの場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Helper_Purchase::isUsePoint(null);

    $this->verify('ポイントを使用するかどうか');
  }

  public function testIsUsePoint_ステータスがキャンセルの場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Helper_Purchase::isUsePoint(ORDER_CANCEL);

    $this->verify('ポイントを使用するかどうか');
  }

  // TODO 要確認：本当にキャンセルのとき以外はすべてTRUEで良いのか、現在の使われ方の都合か
  public function testIsUsePoint_ステータスがキャンセル以外の場合_TRUEが返る() {
    $this->expected = TRUE;
    $this->actual = SC_Helper_Purchase::isUsePoint(ORDER_NEW);

    $this->verify('ポイント加算するかどうか');
  }

}

