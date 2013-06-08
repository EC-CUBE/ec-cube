<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_TestBase.php");
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
 * SC_Helper_Purchase::copyFromOrder()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_copyFromOrderTest extends SC_Helper_Purchase_TestBase
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
  public function testCopyFromOrder_すべてデフォルト設定にした場合_デフォルトの接頭辞・キーでコピーされる()
  {
     $dest = array();
     $src = array(
       'order_name01' => '姓',
       'order_name02' => '名',
       'order_kana01' => 'セイ',
       'order_kana02' => 'メイ',
       'order_sex' => '1',
       'order_zip01' => '012',
       'order_zip02' => '1234',
       'order_pref' => '北海道',
       'order_addr01' => '住所01',
       'order_addr02' => '住所02',
       'order_tel01' => '01',
       'order_tel02' => '1234',
       'order_tel03' => '5678',
       'order_fax01' => '02',
       'order_fax02' => '2345',
       'order_fax03' => '6789'
     );

     $this->expected = array(
       'shipping_name01' => '姓',
       'shipping_name02' => '名',
       'shipping_kana01' => 'セイ',
       'shipping_kana02' => 'メイ',
       'shipping_sex' => '1',
       'shipping_zip01' => '012',
       'shipping_zip02' => '1234',
       'shipping_pref' => '北海道',
       'shipping_addr01' => '住所01',
       'shipping_addr02' => '住所02',
       'shipping_tel01' => '01',
       'shipping_tel02' => '1234',
       'shipping_tel03' => '5678',
       'shipping_fax01' => '02',
       'shipping_fax02' => '2345',
       'shipping_fax03' => '6789'
     );
     $helper = new SC_Helper_Purchase();
     $helper->copyFromOrder($dest, $src);
     $this->actual = $dest;

     $this->verify();
  }

  public function testCopyFromOrder_接頭辞・キーを設定した場合_指定の値でコピーされる()
  {
     $dest = array();
     $src = array(
       'input_name01' => '姓',
       'input_name02' => '名',
       'input_zip01' => '012' // キーに含まれないもの
     );

     $this->expected = array(
       'output_name01' => '姓',
       'output_name02' => '名'
     );
     $helper = new SC_Helper_Purchase();
     $helper->copyFromOrder($dest, $src, 'output', 'input', array('name01', 'name02'));
     $this->actual = $dest;

     $this->verify();
  }
  //////////////////////////////////////////
}

