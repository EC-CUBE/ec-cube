<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_TestBase.php");
/**
 *
 */
class SC_Helper_Purchase_isAddPointTest extends SC_Helper_Purchase_TestBase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testIsAddPoint_新規注文の場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Helper_Purchase::isAddPoint(ORDER_NEW);

    $this->verify('ポイント加算するかどうか');
  }

  public function testIsAddPoint_入金待ちの場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Helper_Purchase::isAddPoint(ORDER_PAY_WAIT);

    $this->verify('ポイント加算するかどうか');
  }

  public function testIsAddPoint_入金済みの場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Helper_Purchase::isAddPoint(ORDER_PRE_END);

    $this->verify('ポイント加算するかどうか');
  }

  public function testIsAddPoint_キャンセルの場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Helper_Purchase::isAddPoint(ORDER_CANCEL);

    $this->verify('ポイント加算するかどうか');
  }

  public function testIsAddPoint_取り寄せ中の場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Helper_Purchase::isAddPoint(ORDER_BACK_ORDER);

    $this->verify('ポイント加算するかどうか');
  }

  public function testIsAddPoint_発送済みの場合_TRUEが返る() {
    $this->expected = TRUE;
    $this->actual = SC_Helper_Purchase::isAddPoint(ORDER_DELIV);

    $this->verify('ポイント加算するかどうか');
  }

  public function testIsAddPoint_その他の場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Helper_Purchase::isAddPoint(ORDER_PENDING);

    $this->verify('ポイント加算するかどうか');
  }

}

