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
 * SC_Utils::sfIsInternalDomain()のテストクラス.
 * HTTP_URL='http://test.local' という前提でテスト.
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfIsInternalDomainTest extends Common_TestCase {


  protected function setUp() {
    // parent::setUp();
  }

  protected function tearDown() {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  public function testsfIsInternalDomain_ドメインが一致する場合_trueが返る() {
    $url = 'http://test.local/html/index.php';
    $this->expected = TRUE;
    $this->actual = SC_Utils::sfIsInternalDomain($url);

    $this->verify($url);
  }

  public function testsfIsInternalDomain_アンカーを含むURLの場合_trueが返る() {
    $url = 'http://test.local/html/index.php#hoge';
    $this->expected = TRUE;
    $this->actual = SC_Utils::sfIsInternalDomain($url);

    $this->verify($url);
  }

  public function testsfIsInternalDomain_ドメインが一致しない場合_falseが返る() {
    $url = 'http://test.local.jp/html/index.php';
    $this->expected = TRUE;
    $this->actual = SC_Utils::sfIsInternalDomain($url);

    $this->verify($url);
  }

  //////////////////////////////////////////

}

