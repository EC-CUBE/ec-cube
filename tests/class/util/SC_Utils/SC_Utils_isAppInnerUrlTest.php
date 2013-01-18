<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
// テスト用に定数を定義
define('HTTP_URL', 'http://sample.eccube.jp/');
define('HTTPS_URL', 'https://sample.eccube.jp/');
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
 * SC_Utils::isAppInnerUrl()のテストクラス.
 * TODO まとめて実行する場合は定数の変更ができないためNG
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_isAppInnerUrlTest extends Common_TestCase {


  protected function setUp() {
    // parent::setUp();
  }

  protected function tearDown() {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  /**
  public function testIsAppInnerUrl_非SSLかつアプリ内URLの場合_trueが返る() {
    $input = 'http://sample.eccube.jp/admin/';
    $this->expected = true;
    $this->actual = SC_Utils::isAppInnerUrl($input);

    $this->verify();
  }

  public function testIsAppInnerUrl_非SSLかつアプリ外URLの場合_falseが返る() {
    $input = 'http://outside.eccube.jp/admin/';
    $this->expected = false;
    $this->actual = SC_Utils::isAppInnerUrl($input);

    $this->verify();
  }

  public function testIsAppInnerUrl_SSLかつアプリ内URLの場合_trueが返る() {
    $input = 'https://sample.eccube.jp/admin/';
    $this->expected = true;
    $this->actual = SC_Utils::isAppInnerUrl($input);

    $this->verify();
  }

  public function testIsAppInnerUrl_SSLかつアプリ外URLの場合_falseが返る() {
    $input = 'https://outside.eccube.jp/admin/';
    $this->expected = false;
    $this->actual = SC_Utils::isAppInnerUrl($input);

    $this->verify();
  }
  */

  //////////////////////////////////////////

}

