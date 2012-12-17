<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_TestBase.php");
/**
 *
 */
class SC_Helper_Purchase_unsetOneShippingTempTest extends SC_Helper_Purchase_TestBase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testUnsetOneShippingTemp__指定したIDの配送情報のみが破棄される() {
    $this->setUpShipping($this->getMultipleShipping());

    SC_Helper_Purchase::unsetOneShippingTemp('00002');

    $this->expected = array(
      '00001' => array(
        'shipment_id' => '00001',
        'shipment_item' => array('商品1'),
        'shipping_pref' => '東京都'),
      '00003' => array(
        'shipment_id' => '00003',
        'shipment_item' => array(),
        'shipping_pref' => '埼玉県')
    );
    $this->actual = $_SESSION['shipping'];

    $this->verify('配送情報');
  }

}

