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
 * SC_Utils::sfGetClassCatCount()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfGetClassCatCountTest extends Common_TestCase
{


  protected function setUp()
  {
    parent::setUp();
    $this->setUpClassCat();
  }

  protected function tearDown()
  {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfGetClassCatCount__規格分類の件数がIDごとに取得できる()
  {
    
    $this->expected = array(
      '1001' => '2',
      '1002' => '1'
    );
    $this->actual = SC_Utils::sfGetClassCatCount();

    $this->verify('規格分類の件数');
  }

  //////////////////////////////////////////

  protected function setUpClassCat()
  {
    $classes = array(
      array(
        'class_id' => '1001',
        'name' => '味',
        'creator_id' => '1',
        'update_date' => 'CURRENT_TIMESTAMP',
        'del_flg' => '0'
      ),
      array(
        'class_id' => '1002',
        'name' => '大きさ',
        'creator_id' => '1',
        'update_date' => 'CURRENT_TIMESTAMP',
        'del_flg' => '0'
      ),
      // 削除フラグが立っているので検索されない
      array(
        'class_id' => '1003',
        'name' => '匂い',
        'creator_id' => '1',
        'update_date' => 'CURRENT_TIMESTAMP',
        'del_flg' => '1'
      )
    );
    $this->objQuery->delete('dtb_class');
    foreach ($classes as $item) {
      $this->objQuery->insert('dtb_class', $item);
    }

    $class_categories = array(
      array(
        'classcategory_id' => '1011',
        'class_id' => '1001',
        'creator_id' => '1',
        'update_date' => 'CURRENT_TIMESTAMP'
      ),
      // 削除フラグが立っているので検索されない
      array(
        'classcategory_id' => '1012',
        'class_id' => '1001',
        'creator_id' => '1',
        'update_date' => 'CURRENT_TIMESTAMP',
        'del_flg' => '1'
      ),
      array(
        'classcategory_id' => '1013',
        'class_id' => '1001',
        'creator_id' => '1',
        'update_date' => 'CURRENT_TIMESTAMP'
      ),
      array(
        'classcategory_id' => '1021',
        'class_id' => '1002',
        'creator_id' => '1',
        'update_date' => 'CURRENT_TIMESTAMP'
      ),
      // dtb_classでdel_flgが立っているので検索されない
      array(
        'classcategory_id' => '1031',
        'class_id' => '1003',
        'creator_id' => '1',
        'update_date' => 'CURRENT_TIMESTAMP'
      )
    );
    $this->objQuery->delete('dtb_classcategory');
    foreach ($class_categories as $item) {
      $this->objQuery->insert('dtb_classcategory', $item);
    }
  }
}

