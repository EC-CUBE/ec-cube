<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/**
 *
 */
class SC_Helper_Purchase_BaseTest extends Common_TestCase {

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

  protected function setUpShippingDb($shipping) {
    
  }
}

