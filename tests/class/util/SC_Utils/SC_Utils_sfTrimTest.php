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
 * SC_Helper_Purchase::sfTrim()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfTrimTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfTrim_文頭と途中にホワイトスペースがある場合_文頭だけが除去できる() {
    $this->expected = 'あ　い うえ' . chr(0x0D) . 'お';
    // 0x0A=CR, 0x0d=LF
    $this->actual = SC_Utils::sfTrim(chr(0x0A) . chr(0x0D) . ' 　あ　い うえ' . chr(0x0D) . 'お');

    $this->verify('トリム結果');
  }
  
  public function testSfTrim_途中と文末にホワイトスペースがある場合_文末だけが除去できる() {
    $this->expected = 'あ　い うえ' . chr(0x0D) . 'お';
    // 0x0A=CR, 0x0d=LF
    $this->actual = SC_Utils::sfTrim('あ　い うえ' .chr(0x0D) . 'お 　' . chr(0x0A) . chr(0x0D));

    $this->verify('トリム結果');
  }

}

