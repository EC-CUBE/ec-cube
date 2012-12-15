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
    $data = array(
      array(
        'shipping_id' => '00001',
        'order_id' => '00001',
        'shipping_name01' => '配送情報01',
        'shipping_date' => '2012-01-12'
      ),
      array(
        'shipping_id' => '00002',
        'order_id' => '00002',
        'shipping_name01' => '配送情報02',
        'shipping_date' => '2011-10-01'
      )
    );
    $this->objQuery->insert('dtb_shipping', $data[0]);
    $this->objQuery->insert('dtb_shipping', $data[1]);
  }
}

