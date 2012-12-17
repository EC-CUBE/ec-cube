<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_TestBase.php");
/**
 *
 */
class SC_Helper_Purchase_getShippingTempTest extends SC_Helper_Purchase_TestBase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testGetShippingTemp_保有フラグがOFFの場合_セッション情報を取得する() {
    $this->setUpShipping($this->getMultipleShipping());

    $this->expected = $this->getMultipleShipping();
    $this->actual = SC_Helper_Purchase::getShippingTemp();

    $this->verify('配送情報');
  }

  public function testGetShippingTemp_保有フラグがONの場合_商品のある情報のみ取得する() {
    $this->setUpShipping($this->getMultipleShipping());

    $this->expected = array(
      '00001' => array(
        'shipment_id' => '00001',
        'shipment_item' => array('商品1'),
        'shipping_pref' => '東京都'),
      '00002' => array(
        'shipment_id' => '00002',
        'shipment_item' => array('商品2'),
        'shipping_pref' => '沖縄県')
    );
    $this->actual = SC_Helper_Purchase::getShippingTemp(TRUE);

    $this->verify('配送情報');
  }

}

