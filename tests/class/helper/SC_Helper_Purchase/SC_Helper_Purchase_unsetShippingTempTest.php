<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_BaseTest.php");
/**
 *
 */
class SC_Helper_Purchase_unsetShippingTempTest extends SC_Helper_Purchase_BaseTest {

  protected function setUp() {
    parent::setUp();

    // 空にするだけなので適当な値を設定
    $_SESSION['shipping'] = 'temp01';
    $_SESSION['multiple_temp'] = 'temp02';
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testUnsetShippingTemp__配送情報がすべて破棄される() {
    SC_Helper_Purchase::unsetShippingTemp(TRUE);

    $this->expected = array('shipping'=>TRUE, 'multiple_temp'=>TRUE);
    $this->actual['shipping'] = empty($_SESSION['shipping']);
    $this->actual['multiple_temp'] = empty($_SESSION['multiple_temp']);

    $this->verify('セッション情報が空かどうか');
  }

}

