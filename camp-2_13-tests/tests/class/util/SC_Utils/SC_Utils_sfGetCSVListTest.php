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
 * SC_Utils::sfGetCSVList()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfGetCSVListTest extends Common_TestCase
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
  public function testSfGetCSVList_配列が空の場合_falseが返る()
  {
    
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfGetCSVList(array());

    $this->verify();
  }

  public function testSfGetCSVList_配列に要素が存在する場合_CSV形式に変換した文字列が返る()
  {
    
    $this->expected = '"1つ目の要素","カンマを,含む要素","3つ目の要素"' . "\r\n";
    $this->actual = SC_Utils::sfGetCSVList(
      array('1つ目の要素', 'カンマを,含む要素', '3つ目の要素')
    );

    $this->verify('CSVフォーマットの文字列');
  }

  //////////////////////////////////////////

}

