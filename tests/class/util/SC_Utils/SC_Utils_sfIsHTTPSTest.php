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
 * SC_Utils::sfIsHTTPS()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfIsHTTPSTest extends Common_TestCase {


  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfIsHTTPS_環境変数にSSLを示す値が入っている場合_trueが返る() {
    $_SERVER['HTTPS'] = 'on';
    $this->expected = true;
    $this->actual = SC_Utils::sfIsHTTPS();

    $this->verify();
  }

  public function testSfIsHTTPS_環境変数に非SSLを示す値が入っている場合_falseが返る() {
    $_SERVER['HTTPS'] = 'off';
    $this->expected = false;
    $this->actual = SC_Utils::sfIsHTTPS();

    $this->verify();
  }

  public function testSfIsHTTPS_環境変数に値が入っていない場合_falseが返る() {
    unset($_SERVER['HTTPS']);
    $this->expected = false;
    $this->actual = SC_Utils::sfIsHTTPS();

    $this->verify();
  }

  //////////////////////////////////////////

}

