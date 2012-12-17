<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_TestBase.php");
/**
 *
 */
class SC_Helper_Purchase_isUsePointTest extends SC_Helper_Purchase_TestBase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testIsUsePoint_ステータスがnullの場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Helper_Purchase::isUsePoint(null);

    $this->verify('ポイントを使用するかどうか');
  }

  public function testIsUsePoint_ステータスがキャンセルの場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Helper_Purchase::isUsePoint(ORDER_CANCEL);

    $this->verify('ポイントを使用するかどうか');
  }

  // TODO 要確認：本当にキャンセルのとき以外はすべてTRUEで良いのか、現在の使われ方の都合か
  public function testIsUsePoint_ステータスがキャンセル以外の場合_TRUEが返る() {
    $this->expected = TRUE;
    $this->actual = SC_Helper_Purchase::isUsePoint(ORDER_NEW);

    $this->verify('ポイント加算するかどうか');
  }

}

