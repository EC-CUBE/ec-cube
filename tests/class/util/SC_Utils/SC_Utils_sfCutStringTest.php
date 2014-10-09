<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
 * SC_Utils::sfCutString()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfCutStringTest extends Common_TestCase
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
  public function testSfCutString_マルチバイト指定で指定長より2文字以上長い場合_指定長でカットされる()
  {
    $input = 'あいうえおABC、こんにちは。';
    $this->expected = 'あいうえおABC、こんにち...';
    $this->actual = SC_Utils::sfCutString($input, 13, false);

    $this->verify();
  }

  public function testSfCutString_マルチバイト指定で指定長より1文字長い場合_カットされない()
  {
    $input = 'あいうえおABC、こんにちは';
    $this->expected = 'あいうえおABC、こんにちは';
    $this->actual = SC_Utils::sfCutString($input, 13, false);

    $this->verify();
  }

  public function testSfCutString_マルチバイト指定で指定長以内の場合_カットされない()
  {
    $input = 'あいうえおABC、こんにち';
    $this->expected = 'あいうえおABC、こんにち';
    $this->actual = SC_Utils::sfCutString($input, 13, false);

    $this->verify();
  }

  public function testSfCutString_1バイト指定で指定長より3文字以上長い場合_指定長でカットされる()
  {
    $input = 'hello, world!!';
    $this->expected = 'hello, worl...';
    $this->actual = SC_Utils::sfCutString($input, 11);

    $this->verify();
  }

  public function testSfCutString_1バイト指定で指定長より2文字長い場合_カットされない()
  {
    $input = 'hello, world!';
    $this->expected = 'hello, world!';
    $this->actual = SC_Utils::sfCutString($input, 11);

    $this->verify();
  }

  public function testSfCutString_1バイト指定で指定長より1文字長い場合_カットされない()
  {
    $input = 'hello, world';
    $this->expected = 'hello, world';
    $this->actual = SC_Utils::sfCutString($input, 11);

    $this->verify();
  }

  public function testSfCutString_1バイト指定で指定長以内の場合_カットされない()
  {
    $input = 'hello, worl';
    $this->expected = 'hello, worl';
    $this->actual = SC_Utils::sfCutString($input, 11);

    $this->verify();
  }

  // [までの場合
  public function testSfCutString_絵文字を含んでカットされる場合_中途半端な絵文字がカットされる1()
  {
    $input = "hello[emoji:135], world.";
    $this->expected = 'hello...';
    $this->actual = SC_Utils::sfCutString($input, 6);

    $this->verify();
  }

  // ]の直前までの場合
  public function testSfCutString_絵文字を含んでカットされる場合_中途半端な絵文字がカットされる2()
  {
    $input = "hello[emoji:135], world.";
    $this->expected = 'hello...';
    $this->actual = SC_Utils::sfCutString($input, 15);

    $this->verify();
  }

  // 最初の絵文字の途中
  public function testSfCutString_複数の絵文字を含んでいてカットされる場合_中途半端な絵文字がカットされる1()
  {
    $input = "hello[emoji:100][emoji:20], world![emoji:10]";
    $this->expected = 'hello...';
    $this->actual = SC_Utils::sfCutString($input, 10);

    $this->verify();
  }

  // 2つめの絵文字の途中
  public function testSfCutString_複数の絵文字を含んでいてカットされる場合_中途半端な絵文字がカットされる2()
  {
    $input = "hello[emoji:100][emoji:20], world![emoji:10]";
    $this->expected = 'hello[emoji:100]...';
    $this->actual = SC_Utils::sfCutString($input, 20);

    $this->verify();
  }

  // 3つめの絵文字の途中
  public function testSfCutString_複数の絵文字を含んでいてカットされる場合_中途半端な絵文字がカットされる3()
  {
    $input = "hello[emoji:100][emoji:20], world![emoji:10]";
    $this->expected = 'hello[emoji:100][emoji:20], wo...';
    $this->actual = SC_Utils::sfCutString($input, 30);
    
    $this->verify();
  }

  // TODO 要確認 三点リーダ付けない場合は、lenと比較した方が良いのでは？
  public function testSfCutString_三点リーダ付加指定がない場合_付加されない()
  {
    $input = 'hello, world';
    $this->expected = 'hello';
    $this->actual = SC_Utils::sfCutString($input, 5, true, false);

    $this->verify();
  }

  //////////////////////////////////////////
}

