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
 * SC_Utils::getSaveImagePath()のテストクラス.
 *
 *
 * @author Yangsin
 * @version $Id: SC_Utils_getSaveImagePathTest.php 22960 2013-07-08 14:56:17Z Seasoft $
 */
class SC_Utils_getSaveImagePathTest extends Common_TestCase
{


  protected function setUp()
  {
    parent::setUp();
  }

  protected function tearDown()
  {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testGetSaveImagePath_存在する画像ファイル場合_IMAGE_SAVE_REALDIRをつけたパスを返す()
  {
    $input = 'nabe130.jpg';

    $this->expected = IMAGE_SAVE_REALDIR . $input;
    $this->actual = SC_Utils::getSaveImagePath($input);

    $this->verify();
  }

  public function testGetSaveImagePath_存在しない画像ファイル場合_NO_IMAGE_REALFILEを返す()
  {
    $input = 'nothing.jpg';

    $this->expected = NO_IMAGE_REALFILE;
    $this->actual = SC_Utils::getSaveImagePath($input);

    $this->verify();
  }

  public function testGetSaveImagePath_入力値が空の場合_NO_IMAGE_REALFILEを返す()
  {
    $input = '';

    $this->expected = NO_IMAGE_REALFILE;
    $this->actual = SC_Utils::getSaveImagePath($input);

    $this->verify();
  }

  //////////////////////////////////////////
}

