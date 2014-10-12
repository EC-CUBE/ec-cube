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
 * SC_Utils::sfGetProductClassId()のテストクラス.
 * TODO del_flgは使わなくて良い？？
 * TODO classcategory_id1とclasscategory_id2を使わないと一意に指定できない。
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfGetProductClassIdTest extends Common_TestCase
{


  protected function setUp()
  {
    parent::setUp();
    $this->setUpProductsClass();
  }

  protected function tearDown()
  {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfGetProductClassId_存在する製品IDのみを指定した場合_カテゴリ0の対応する製品クラスが取得できる()
  {
    
    $this->expected = '2001';
    $this->actual = SC_Utils::sfGetProductClassId('2001');

    $this->verify('取得した製品クラス');
  }

  public function testSfGetProductClassId_存在する製品IDのみを指定してカテゴリ0の製品クラスが存在しなければ_nullが返る()
  {
    
    $this->expected = null;
    $this->actual = SC_Utils::sfGetProductClassId('1001');

    $this->verify('取得結果が空');
  }

  public function testSfGetProductClassId_存在する製品IDとカテゴリIDを指定した場合_対応する製品クラスが取得できる()
  {
    
    $this->expected = '1002';
    $this->actual = SC_Utils::sfGetProductClassId('1001', '2');

    $this->verify('取得した製品クラス');
  }

  public function testSfGetProductClassId_存在する製品IDと存在しないカテゴリIDを指定した場合_nullが返る()
  {
    
    $this->expected = null;
    $this->actual = SC_Utils::sfGetProductClassId('1001', '999');

    $this->verify('取得結果が空');
  }

  public function testSfGetProductClassId_存在しない製品IDを指定した場合_nullが返る()
  {
    $this->expected = null;
    $this->actual = SC_Utils::sfGetProductClassId('9999');

    $this->verify('取得結果が空');
  }

  //////////////////////////////////////////
  protected function setUpProductsClass()
  {
    $products_class = array(
      array(
        'product_class_id' => '2001',
        'product_id' => '2001',
        'product_code' => 'code2001',
        'price02' => '1000',
        'creator_id' => '1',
        'update_date' => 'CURRENT_TIMESTAMP'
      ),
      array(
        'product_class_id' => '1001',
        'product_id' => '1001',
        'product_code' => 'code1001',
        'price02' => '1000',
        'classcategory_id1' => '1',
        'creator_id' => '1',
        'update_date' => 'CURRENT_TIMESTAMP'
      ),
      array(
        'product_class_id' => '1002',
        'product_id' => '1001',
        'product_code' => 'code1002',
        'price02' => '1000',
        'classcategory_id1' => '2',
        'creator_id' => '1',
        'update_date' => 'CURRENT_TIMESTAMP'
      )
    );

    $this->objQuery->delete('dtb_products_class');
    foreach ($products_class as $item)
{
      $this->objQuery->insert('dtb_products_class', $item);
    }
  }
}

