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
 * SC_Helper_Purchase::sfGetCommaList()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfGetCommaListTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfGetCommaList_配列が空の場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfGetCommaList(array());

    $this->verify('連結済みの文字列');
  }

  public function testSfGetCommaList_スペースフラグが立っている場合_スペース付きで連結される() {
    $this->expected = 'りんご, ミカン, バナナ';
    $this->actual = SC_Utils::sfGetCommaList(
      array('りんご', 'ミカン', 'バナナ'),
      TRUE,
      array());

    $this->verify('連結済みの文字列');
  }

  public function testSfGetCommaList_スペースフラグが倒れている場合_スペース付きで連結される() {
    $this->expected = 'りんご,ミカン,バナナ';
    $this->actual = SC_Utils::sfGetCommaList(
      array('りんご', 'ミカン', 'バナナ'),
      FALSE,
      array());

    $this->verify('連結済みの文字列');
  }

  // TODO 要確認：arrpopの役割
  public function testSfGetCommaList_除外リストが指定されている場合_スペース付きで連結される() {
    $this->expected = 'りんご, バナナ';
    $this->actual = SC_Utils::sfGetCommaList(
      array('りんご', 'ミカン', 'バナナ'),
      TRUE,
      array('梨', 'ミカン', '柿'));

    $this->verify('連結済みの文字列');
  }

}

