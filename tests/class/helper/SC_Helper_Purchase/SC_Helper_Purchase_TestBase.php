<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/**
 *
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
      $shipping = getSingleShipping(); 
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
        'shipping_id' => '1',
        'order_id' => '1',
        'shipping_name01' => '配送情報01',
        'shipping_date' => '2012-01-12'
      ),
      array(
        'shipping_id' => '2',
        'order_id' => '2',
        'shipping_name01' => '配送情報02',
        'shipping_date' => '2011-10-01'
      )
    );

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
          'product_class_id' => '1001',
          'product_id' => '1001',
          'product_code' => 'code1001',
          'classcategory_id1' => '1001',
          'classcategory_id2' => '1002',
          'price01' => '1500',
          'price02' => '1500',
          'del_flg' => '0'
        ),
        array(
          'product_class_id' => '1002',
          'product_id' => '1002',
          'del_flg' => '0'
        )
      );

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
        'classcategory_id' => '1001',
        'name' => 'cat1001'
      ),
      array(
        'classcategory_id' => '1002',
        'name' => 'cat1002'
      )
    );

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
       'product_id' => '1001',
       'name' => '製品名1001'
     ),
     array(
       'product_id' => '1002',
       'name' => '製品名1002'
     )
   );

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
     )
   );

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
       'del_flg' => '1'
     ),
     array(
       'deliv_id' => '1001',
       'product_type_id' => '1001',
       'name' => '配送業者01',
       'rank' => '2'
     ),
     array(
       'deliv_id' => '1002',
       'product_type_id' => '1001',
       'name' => '配送業者02',
       'rank' => '3'
     ),
     array( // 商品種別違い
       'deliv_id' => '1004',
       'product_type_id' => '2001',
       'name' => '配送業者21',
       'rank' => '4'
     ),
   );

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
       'payment_id' => '1001',
       'payment_method' => '支払方法1001'
     ),
     array(
       'payment_id' => '1002',
       'payment_method' => '支払方法1002',
       'del_flg' => '1'
     ),
     array(
       'payment_id' => '1003',
       'payment_method' => '支払方法1003'
     )
   );

   foreach ($payment as $key => $item) {
     $this->objQuery->insert('dtb_payment', $item);
   }
 }
}

