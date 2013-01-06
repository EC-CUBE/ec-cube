<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
 * SC_Helper_Purchaseのテストの基底クラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_TestBase extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  /**
   * セッションに配送情報を設定します。
   */
  protected function setUpShipping($shipping) {
    if (!$shipping) {
      $shipping = $this->getSingleShipping(); 
    }

    $_SESSION['shipping'] = $shipping;
  }

  protected function getSingleShipping() {
    return array(
      '00001' => array(
        'shipment_id' => '00001',
        'shipment_item' => '商品1',
        'shipping_pref' => '東京都')
    );
  }

  protected function getMultipleShipping() {
    return array(
      '00001' => array(
        'shipment_id' => '00001',
        'shipment_item' => array('商品1'),
        'shipping_pref' => '東京都'),
      '00002' => array(
        'shipment_id' => '00002',
        'shipment_item' => array('商品2'),
        'shipping_pref' => '沖縄県'),
      '00003' => array(
        'shipment_id' => '00003',
        'shipment_item' => array(),
        'shipping_pref' => '埼玉県')
    );
  }

  /**
   * DBに配送情報を設定します。
   */
  protected function setUpShippingOnDb() {
    $shippings = array(
      array(
        'update_date' => '2000-01-01 00:00:00',
        'shipping_id' => '1',
        'order_id' => '1',
        'shipping_name01' => '配送情報01',
        'shipping_date' => '2012-01-12'
      ),
      array(
        'update_date' => '2000-01-01 00:00:00',
        'shipping_id' => '2',
        'order_id' => '2',
        'shipping_name01' => '配送情報02',
        'shipping_date' => '2011-10-01'
      ),
      array(
        'update_date' => '2000-01-01 00:00:00',
        'shipping_id' => '1002',
        'order_id' => '1002',
        'shipping_time' => '午後',
        'time_id' => '1'
      )
    );

    $this->objQuery->delete('dtb_shipping');
    foreach ($shippings as $key => $item) {
      $this->objQuery->insert('dtb_shipping', $item);
    }
  }

  /**
   * DBに配送商品情報を設定します。
   */
  protected function setUpShipmentItem() {
      $shipping_items = array(
        array(
          'shipping_id' => '1',
          'product_class_id' => '1001',
          'order_id' => '1',
          'product_name' => '商品名01',
          'price' => '1500'
        ),
        array(
          'shipping_id' => '1',
          'product_class_id' => '1002',
          'order_id' => '1',
          'product_name' => '商品名02',
          'price' => '2400'
        )
      );

    $this->objQuery->delete('dtb_shipment_item');
    foreach ($shipping_items as $key => $item) {
      $this->objQuery->insert('dtb_shipment_item', $item);
    }
    $this->setUpProductClass();
  }

  /**
   * DBに商品クラス情報を設定します.
   */
  protected function setUpProductClass() {
      $product_class = array(
        array(
          'update_date' => '2000-01-01 00:00:00',
          'product_class_id' => '1001',
          'product_id' => '1001',
          'product_type_id' => '1',
          'product_code' => 'code1001',
          'classcategory_id1' => '1001',
          'classcategory_id2' => '1002',
          'price01' => '1500',
          'price02' => '1500',
          'creator_id' => '1',
          'del_flg' => '0'
        ),
        array(
          'update_date' => '2000-01-01 00:00:00',
          'product_class_id' => '1002',
          'product_id' => '1002',
          'product_type_id' => '2',
          'price02' => '2500',
          'creator_id' => '1',
          'del_flg' => '0'
        )
      );

    $this->objQuery->delete('dtb_products_class');
    foreach ($product_class as $key => $item) {
      $this->objQuery->insert('dtb_products_class', $item);
    }
    $this->setUpClassCategory();
    $this->setUpProducts();
  }

  /**
   * DBに製品カテゴリ情報を登録します.
   */
  protected function setUpClassCategory() {
    $class_category = array(
      array(
        'update_date' => '2000-01-01 00:00:00',
        'classcategory_id' => '1001',
        'class_id' => '1',
        'creator_id' => '1',
        'name' => 'cat1001'
      ),
      array(
        'update_date' => '2000-01-01 00:00:00',
        'classcategory_id' => '1002',
        'class_id' => '1',
        'creator_id' => '1',
        'name' => 'cat1002'
      )
    );

    $this->objQuery->delete('dtb_classcategory');
    foreach ($class_category as $key => $item) {
      $this->objQuery->insert('dtb_classcategory', $item);
    }
  }

  /** 
   * DBに製品情報を登録します.
   */
 protected function setUpProducts() {
   $products = array(
     array(
       'update_date' => '2000-01-01 00:00:00',
       'product_id' => '1001',
       'name' => '製品名1001',
       'del_flg' => '0',
       'creator_id' => '1',
       'status' => '1'
     ),
     array(
       'update_date' => '2000-01-01 00:00:00',
       'product_id' => '1002',
       'name' => '製品名1002',
       'del_flg' => '0',
       'creator_id' => '1',
       'status' => '2'
     )
   );

   $this->objQuery->delete('dtb_products');
   foreach ($products as $key => $item) {
     $this->objQuery->insert('dtb_products', $item);
   }
 }

 /**
  * DBに支払方法の情報を登録します.
  */
 protected function setUpPaymentOptions() {
   $payment_options = array(
     array(
       'deliv_id' => '2001',
       'payment_id' => '2001',
       'rank' => '1'
     ),
     array(
       'deliv_id' => '1001',
       'payment_id' => '1001',
       'rank' => '2'
     ),
     array(
       'deliv_id' => '1001',
       'payment_id' => '1002',
       'rank' => '1'
     ),
     array(
       'deliv_id' => '1003',
       'payment_id' => '3001',
       'rank' => '1'
     ),
     array(
       'deliv_id' => '1003',
       'payment_id' => '3002',
       'rank' => '2'
     ),
     array(
       'deliv_id' => '1003',
       'payment_id' => '3003',
       'rank' => '3'
     ),
     array(
       'deliv_id' => '1003',
       'payment_id' => '3004',
       'rank' => '4'
     ),
     array(
       'deliv_id' => '1003',
       'payment_id' => '3005',
       'rank' => '5'
     )
   );

   $this->objQuery->delete('dtb_payment_options');
   foreach ($payment_options as $key => $item) {
     $this->objQuery->insert('dtb_payment_options', $item);
   }
 }
 
 /**
  * DBに配送業者の情報を登録します.
  */
 protected function setUpDeliv() {
   $deliv = array(
     array(  // 削除フラグON
       'deliv_id' => '2001',
       'product_type_id' => '1001',
       'name' => '配送業者del',
       'rank' => '1',
       'creator_id' => '1',
       'del_flg' => '1',
       'update_date' => '2000-01-01 00:00:00'
     ),
     array(
       'deliv_id' => '1001',
       'product_type_id' => '1001',
       'name' => '配送業者01',
       'creator_id' => '1',
       'rank' => '2',
       'update_date' => '2000-01-01 00:00:00'
     ),
     array(
       'deliv_id' => '1002',
       'product_type_id' => '1001',
       'name' => '配送業者02',
       'creator_id' => '1',
       'rank' => '3',
       'update_date' => '2000-01-01 00:00:00'
     ),
     array( // 商品種別違い
       'deliv_id' => '1004',
       'product_type_id' => '2001',
       'name' => '配送業者21',
       'creator_id' => '1',
       'rank' => '4',
       'update_date' => '2000-01-01 00:00:00'
     ),
   );

   $this->objQuery->delete('dtb_deliv');
   foreach ($deliv as $key => $item) {
     $this->objQuery->insert('dtb_deliv', $item);
   }
 }

 /**
  * DBにお届け時間の情報を登録します.
  */
 protected function setUpDelivTime() {
   $deliv_time = array(
     array(
       'deliv_id' => '1002',
       'time_id' => '1',
       'deliv_time' => '午前'
     ),
     array(
       'deliv_id' => '1001',
       'time_id' => '2',
       'deliv_time' => '午後'
     ),
     array(
       'deliv_id' => '1001',
       'time_id' => '1',
       'deliv_time' => '午前'
     ),
   );

   $this->objQuery->delete('dtb_delivtime');
   foreach ($deliv_time as $key => $item) {
     $this->objQuery->insert('dtb_delivtime', $item);
   }
 }

 /**
  * DBに支払方法の情報を登録します.
  */
 protected function setUpPayment() {
   $payment = array(
     array(
       'update_date' => '2000-01-01 00:00:00',
       'payment_id' => '1001',
       'creator_id' => '1',
       'payment_method' => '支払方法1001'
     ),
     array(
       'update_date' => '2000-01-01 00:00:00',
       'payment_id' => '1002',
       'creator_id' => '1',
       'payment_method' => '支払方法1002',
       'del_flg' => '1'
     ),
     array(
       'update_date' => '2000-01-01 00:00:00',
       'payment_id' => '1003',
       'creator_id' => '1',
       'payment_method' => '支払方法1003'
     ),
     array(
       'update_date' => '2000-01-01 00:00:00',
       'payment_id' => '3001',
       'creator_id' => '1',
       'payment_method' => '支払方法3001',
       'del_flg' => '1'
     ),
     array(
       'update_date' => '2000-01-01 00:00:00',
       'payment_id' => '3002',
       'creator_id' => '1',
       'payment_method' => '支払方法3002'
     ),
     array(
       'update_date' => '2000-01-01 00:00:00',
       'payment_id' => '3003',
       'creator_id' => '1',
       'payment_method' => '支払方法3003',
       'rule_max' => 10000
     ),
     array(
       'update_date' => '2000-01-01 00:00:00',
       'payment_id' => '3004',
       'creator_id' => '1',
       'payment_method' => '支払方法3004',
       'upper_rule' => 20000
     ),
     array(
       'update_date' => '2000-01-01 00:00:00',
       'payment_id' => '3005',
       'creator_id' => '1',
       'payment_method' => '支払方法3005',
       'rule_max' => 12000,
       'upper_rule' => 21000
     )
   );

   $this->objQuery->delete('dtb_payment');
   foreach ($payment as $key => $item) {
     $this->objQuery->insert('dtb_payment', $item);
   }
 }

 /**
  * DBに受注情報を設定します.
  */
  protected function setUpOrder() {
    $order = array(
      array(
        'update_date' => '2000-01-01 00:00:00',
        'order_id' => '1001',
        'customer_id' => '1001',
        'order_name01' => '受注情報01',
        'status' => '3',
        'payment_date' => '2032-12-31 01:20:30', // 日付が変わっても良いように、遠い未来に設定
        'use_point' => '10',
        'add_point' => '20'
      ),
      array(
        'update_date' => '2000-01-01 00:00:00',
        'order_id' => '1002',
        'customer_id' => '1002',
        'order_name01' => '受注情報02',
        'status' => '5',
        'payment_id' => '1002',
        'payment_method' => '支払方法1001',
        'deliv_id' => '1002',
        'use_point' => '10',
        'add_point' => '20'
      )
    );

    $this->objQuery->delete('dtb_order');
    foreach ($order as $item) {
      $this->objQuery->insert('dtb_order', $item);
    }
  }

 /**
  * DBに受注一時情報を設定します.
  */
  protected function setUpOrderTemp() {
    $order = array(
      array(
        'update_date' => '2000-01-01 00:00:00',
        'order_temp_id' => '1001',
        'customer_id' => '1001',
        'order_name01' => '受注情報01',
        'order_id' => '1001'
      ),
      array(
        'update_date' => '2000-01-01 00:00:00',
        'order_temp_id' => '1002',
        'customer_id' => '1002',
        'order_name01' => '受注情報02',
        'payment_id' => '1002',
        'payment_method' => '支払方法1001'
      )
    );

    $this->objQuery->delete('dtb_order_temp');
    foreach ($order as $item) {
      $this->objQuery->insert('dtb_order_temp', $item);
    }
  }

 /**
  * DBに受注詳細を設定します.
  */
 protected function setUpOrderDetail() {
   $order_detail = array(
     array(
       'order_detail_id' => '1001',
       'order_id' => '1001',
       'product_id' => '1002',
       'product_class_id' => '1002',
       'product_code' => 'pc1002',
       'product_name' => '製品名1002',
       'classcategory_name1' => 'cat10021',
       'classcategory_name2' => 'cat10022',
       'price' => 3000,
       'quantity' => 10,
       'point_rate' => 5,
       'tax_rate' => 5,
       'tax_rule' => 0
     ),
     array(
       'order_detail_id' => '1002',
       'order_id' => '1001',
       'product_id' => '1001',
       'product_class_id' => '1001',
       'product_code' => 'pc1001',
       'product_name' => '製品名1001',
       'classcategory_name1' => 'cat10011',
       'classcategory_name2' => 'cat10012',
       'price' => 4000,
       'quantity' => 15,
       'point_rate' => 6,
       'tax_rate' => 3,
       'tax_rule' => 1
     ),
     array(
       'order_detail_id' => '1003',
       'order_id' => '1002',
       'product_id' => '1001',
       'product_class_id' => '1001',
       'product_name' => '製品名1003'
     )
   );

   $this->objQuery->delete('dtb_order_detail');
   foreach ($order_detail as $item) {
     $this->objQuery->insert('dtb_order_detail', $item);
   }
 }

 /**
  * DBに顧客情報を設定します。
  */
 protected function setUpCustomer() {
   $customer = array(
     array(
       'customer_id' => '1001',
       'name01' => '苗字',
       'name02' => '名前',
       'kana01' => 'みょうじ',
       'kana02' => 'なまえ',
       'email' => 'test@example.com',
       'secret_key' => 'hoge',
       'point' => '100',
       'update_date' => '2000-01-01 00:00:00'
     ),
     array(
       'customer_id' => '1002',
       'name01' => '苗字2',
       'name02' => '名前2',
       'kana01' => 'みょうじ2',
       'kana02' => 'なまえ2',
       'email' => 'test2@example.com',
       'secret_key' => 'hoge2',
       'point' => '200',
       'update_date' => '2000-01-01 00:00:00'
     )
   );

   $this->objQuery->delete('dtb_customer');
   foreach ($customer as $item) {
     $this->objQuery->insert('dtb_customer', $item);
   }
 }

}

