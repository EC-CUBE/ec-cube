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
 * SC_Utils::sfTermMonth()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfTermMonthTest extends Common_TestCase
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
  public function testSfTermMonth_締め日が月末より早い場合_設定した締め日の通りになる()
  {
    $this->expected = array('2012/9/16', '2012/10/15 23:59:59');
    $this->actual = SC_Utils::sfTermMonth(2012, 10, 15);

    $this->verify();
  }

  public function testSfTermMonth_該当月の末日が締め日より早い場合_末日に合わせられる()
  {
    $this->expected = array('2012/9/1', '2012/9/30 23:59:59');
    $this->actual = SC_Utils::sfTermMonth(2012, 9, 31);

    $this->verify();
  }

  public function testSfTermMonth_前月の末日が締め日より早い場合_末日に合わせられる()
  {
    $this->expected = array('2012/10/1', '2012/10/31 23:59:59');
    $this->actual = SC_Utils::sfTermMonth(2012, 10, 31);

    $this->verify();
  }

  public function testSfTermMonth_年をまたぐ場合_前月が前年十二月になる()
  {
    $this->expected = array('2012/12/16', '2013/1/15 23:59:59');
    $this->actual = SC_Utils::sfTermMonth(2013, 1, 15);

    $this->verify();
  }

  //////////////////////////////////////////

}

