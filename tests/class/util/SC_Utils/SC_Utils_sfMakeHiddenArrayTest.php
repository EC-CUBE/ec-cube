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
 * SC_Utils::sfMakeHiddenArray()のテストクラス.
 * ※ソースコード上で使われている箇所がなく詳細仕様が不明なので、ソースコードに合わせて作成
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfMakeHiddenArrayest extends Common_TestCase
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
  public function testSfMakeHiddenArray__多段配列が1次元配列に変換される()
  {
    $input_array = array(
      'vegetable' => '野菜',
      'fruit' => array(
        'apple' => 'りんご',
        'banana' => 'バナナ'
      ),    
      'drink' => array(
         'alcohol' => array(
           'beer' => 'ビール'
         ),
         'water' => '水'
      ),
      'rice' => '米'
    );
    $this->expected = array(
      'vegetable' => '野菜',
      'fruit[apple]' => 'りんご',
      'fruit[banana]' => 'バナナ',
      'drink[alcohol][beer]' => 'ビール',
      'drink[water]' => '水',
      'rice' => '米'
    );
    $this->actual = SC_Utils::sfMakeHiddenArray($input_array);
    $this->verify();
  }
  //////////////////////////////////////////
}

