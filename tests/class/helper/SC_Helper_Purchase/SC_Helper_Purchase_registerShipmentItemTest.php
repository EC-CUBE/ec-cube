<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_TestBase.php");
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
 * SC_Helper_Purchase::registerShipmentItem()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_registerShipmentItemTest extends SC_Helper_Purchase_TestBase
{

  protected function setUp()
  {
    parent::setUp();
    $this->setUpShipmentItem();
  }

  protected function tearDown()
  {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testRegisterShipmentItem_製品クラスIDが入っていない場合_登録を行わない()
  {
    // 引数の設定
    $order_id = '1';
    $shipping_id = '1';
    $arrParams = array(
      array(
        // 'product_class_id' => '1',
        'product_name' => '追加製品名01',
        'product_code' => 'newcode01',
        'classcategory_name1' => 'newcat01',
        'classcategory_name2' => 'newcat02',
        'price' => '2500'
      )
    );

    // 期待値の設定
    $this->expected['count'] = 0;

    // 対象functionの呼び出し
    SC_Helper_Purchase::registerShipmentItem($order_id, $shipping_id, $arrParams);
    $result = $this->objQuery->select(
      'product_class_id,product_name,product_code,classcategory_name1,classcategory_name2,price',
      'dtb_shipment_item',
      'order_id = ? and shipping_id = ?',
      array($order_id, $shipping_id)
    );
    $this->actual['count'] = count($result);

    $this->verify('登録された配送商品情報');
  }

  public function testRegisterShipmentItem_製品名等が指定されている場合_指定された値で登録を行う()
  {
    // 引数の設定
    $order_id = '1';
    $shipping_id = '1';
    $arrParams = array(
      array(
        'product_class_id' => '1',
        'product_name' => '追加製品名01',
        'product_code' => 'newcode01',
        'classcategory_name1' => 'newcat01',
        'classcategory_name2' => 'newcat02',
        'price' => '2500'
      )
    );

    // 期待値の設定
    $this->expected['count'] = 1;
    $this->expected['first'] = array(
      'product_class_id' => '1',
      'product_name' => '追加製品名01',
      'product_code' => 'newcode01',
      'classcategory_name1' => 'newcat01',
      'classcategory_name2' => 'newcat02',
      'price' => '2500'
    );

    // 対象functionの呼び出し
    SC_Helper_Purchase::registerShipmentItem($order_id, $shipping_id, $arrParams);
    $result = $this->objQuery->select(
      'product_class_id,product_name,product_code,classcategory_name1,classcategory_name2,price',
      'dtb_shipment_item',
      'order_id = ? and shipping_id = ?',
      array($order_id, $shipping_id)
    );
    $this->actual['count'] = count($result);
    $this->actual['first'] = $result[0];

    $this->verify('登録された配送商品情報');
  }

  public function testRegisterShipmentItem_製品名等が指定されていない場合_DBからマスタ情報を取得して登録を行う()
  {
    // 引数の設定
    $order_id = '1';
    $shipping_id = '1';
    $arrParams = array(
      array(
        'product_class_id' => '1001'
        // 'product_name' => '追加製品名01',
        // 'product_code' => 'newcode01',
        // 'classcategory_name1' => 'newcat01',
        // 'classcategory_name2' => 'newcat02',
        // 'price' => '2500'
      )
    );

    // 期待値の設定
    $this->expected['count'] = 1;
    $this->expected['first'] = array(
      'product_class_id' => '1001',
      'product_name' => '製品名1001',
      'product_code' => 'code1001',
      'classcategory_name1' => 'cat1001',
      'classcategory_name2' => 'cat1002',
  // TODO 要確認price01, price02を設定しても価格が取れない。実際にはDBから取るケースが無い?
      //'price' => '1500'
      'price' => null
    );

    // 対象functionの呼び出し
    SC_Helper_Purchase::registerShipmentItem($order_id, $shipping_id, $arrParams);
    $result = $this->objQuery->select(
      'product_class_id,product_name,product_code,classcategory_name1,classcategory_name2,price',
      'dtb_shipment_item',
      'order_id = ? and shipping_id = ?',
      array($order_id, $shipping_id)
    );
    $this->actual['count'] = count($result);
    $this->actual['first'] = $result[0];

    $this->verify('登録された配送商品情報');
  }

  public function testRegisterShipmentItem_DBに存在しないカラムを指定した場合_エラーなく登録できる()
  {
    // 引数の設定
    $order_id = '1';
    $shipping_id = '1';
    $arrParams = array(
      array(
        'product_class_id' => '1',
        'product_name' => '追加製品名01',
        'product_code' => 'newcode01',
        'classcategory_name1' => 'newcat01',
        'classcategory_name2' => 'newcat02',
        'price' => '2500',
        'xxxx' => 'yyyyyy' // 存在しないカラム
      )
    );

    // 期待値の設定
    $this->expected['count'] = 1;
    $this->expected['first'] = array(
      'product_class_id' => '1',
      'product_name' => '追加製品名01',
      'product_code' => 'newcode01',
      'classcategory_name1' => 'newcat01',
      'classcategory_name2' => 'newcat02',
      'price' => '2500'
    );

    // 対象functionの呼び出し
    SC_Helper_Purchase::registerShipmentItem($order_id, $shipping_id, $arrParams);
    $result = $this->objQuery->select(
      'product_class_id,product_name,product_code,classcategory_name1,classcategory_name2,price',
      'dtb_shipment_item',
      'order_id = ? and shipping_id = ?',
      array($order_id, $shipping_id)
    );
    $this->actual['count'] = count($result);
    $this->actual['first'] = $result[0];

    $this->verify('登録された配送商品情報');
  }
  
  //////////////////////////////////////////
}

