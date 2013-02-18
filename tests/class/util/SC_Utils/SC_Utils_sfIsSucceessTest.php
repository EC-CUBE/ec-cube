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
 * SC_Utils::sfIsSuccess()のテストクラス.
 * TODO exitするケースはテスト不可
 * TODO HTTPSのケースは未テスト(config.phpでhttpsのURLが指定されていないため)
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfIsSuccessTest extends Common_TestCase
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
  public function testSfIsSuccess_認証に失敗している場合_falseが返る()
  {
    $objSess = new SC_Session_Mock();
    $objSess->is_success = SUCCESS + 1;

    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsSuccess($objSess, FALSE);

    $this->verify('認証可否');
  }

  public function testSfIsSuccess_認証成功でリファラがない場合_trueが返る()
  {
    $objSess = new SC_Session_Mock();
    $objSess->is_success = SUCCESS;

    $this->expected = TRUE;
    $this->actual = SC_Utils::sfIsSuccess($objSess);

    $this->verify('認証可否');
  }

  // TODO 正規のドメインであることは確認しているが、管理画面からというのはチェックしていないのでは？
  public function testSfIsSuccess_認証成功でリファラが正しい場合_trueが返る()
  {
    $objSess = new SC_Session_Mock();
    $objSess->is_success = SUCCESS;
    $_SERVER['HTTP_REFERER'] = 'http://test.local/hoge/fuga';

    $this->expected = TRUE;
    $this->actual = SC_Utils::sfIsSuccess($objSess, FALSE);

    $this->verify('認証可否');
  }

  public function testSfIsSuccess_認証成功でリファラが不正な場合_falseが返る()
  {
    $objSess = new SC_Session_Mock();
    $objSess->is_success = SUCCESS;
    $_SERVER['HTTP_REFERER'] = 'http://test.jp.local/hoge/fuga';

    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsSuccess($objSess, FALSE);

    $this->verify('認証可否');
  }

  //////////////////////////////////////////

}

class SC_Session_Mock extends SC_Session
{

  public $is_success;

  function IsSuccess()
  {
    return $this->is_success;
  }

}

