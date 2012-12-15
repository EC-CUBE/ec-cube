<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_TestiBase.php");
/**
 *
 */
class SC_Helper_Purchase_getShippingPrefTest extends SC_Helper_Purchase_TestBase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  // TODO 要確認：引数の名前がおかしい（is_multipleではないはず）
  public function testGetShippingPref_保有フラグがOFFの場合_全配送情報を取得する() {
    $this->setUpShipping($this->getMultipleShipping());

    $this->expected = array('東京都', '沖縄県', '埼玉県');
    $this->actual = SC_Helper_Purchase::getShippingPref();

    $this->verify('配送先の都道府県');
  }

  public function testGetShippingPref_保有フラグがONの場合_商品のある配送情報のみ取得する() {
    $this->setUpShipping($this->getMultipleShipping());

    $this->expected = array('東京都', '沖縄県');
    $this->actual = SC_Helper_Purchase::getShippingPref(TRUE);

    $this->verify('配送先の都道府県');
  }

}

