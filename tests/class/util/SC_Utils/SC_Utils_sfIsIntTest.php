<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/**
 *
 */
class SC_Utils_sfIsIntTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfIsInt_0バイト文字列の場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsInt('');

    $this->verify('整数かどうか');
  }

  public function testSfIsInt_intの最大長より長い場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsInt('10000000000');

    $this->verify('整数かどうか');
  }

  // TODO 要確認
  public function testSfIsInt_intの最大値ギリギリの場合_TRUEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsInt('2147483647');

    $this->verify('整数かどうか');
  }

  // TODO 要確認
  public function testSfIsInt_intの最大値を超える場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsInt('2147483648');

    $this->verify('整数かどうか');
  }

  public function testSfIsInt_数値でない場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsInt('HELLO123');

    $this->verify('整数かどうか');
  }

  public function testSfIsInt_正の整数の場合_TRUEが返る() {
    $this->expected = TRUE;
    $this->actual = SC_Utils::sfIsInt('123456789');

    $this->verify('整数かどうか');
  }

  // TODO 要確認
  public function testSfIsInt_正の小数の場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsInt('123.456');

    $this->verify('整数かどうか');
  }

  public function testSfIsInt_負の整数の場合_TRUEが返る() {
    $this->expected = TRUE;
    $this->actual = SC_Utils::sfIsInt('-12345678');

    $this->verify('整数かどうか');
  }

  // TODO 要確認
  public function testSfIsInt_負の整数で桁数が最大の場合_TRUEが返る() {
    $this->expected = TRUE;
    $this->actual = SC_Utils::sfIsInt('-123456789');

    $this->verify('整数かどうか');
  }

}

