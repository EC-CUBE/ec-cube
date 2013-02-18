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
 * SC_Utils::sfGetUnderChildrenArray()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfGetUnderChildrenArrayTest extends Common_TestCase
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
  public function testSfGetUnderChildrenArray__与えられた親IDを持つ要素だけが抽出される()
  {
    $input_array = array(
      array('parent_id' => '1001', 'child_id' => '1001001'),
      array('parent_id' => '1002', 'child_id' => '1002001'),
      array('parent_id' => '1002', 'child_id' => '1002002'),
      array('parent_id' => '1003', 'child_id' => '1003001'),
      array('parent_id' => '1004', 'child_id' => '1004001')
    );
    $this->expected = array('1002001', '1002002');
    $this->actual = SC_Utils::sfGetUnderChildrenArray(
      $input_array, 
      'parent_id',
      'child_id', 
      '1002'
    );
    $this->verify();
  }

  //////////////////////////////////////////

}

