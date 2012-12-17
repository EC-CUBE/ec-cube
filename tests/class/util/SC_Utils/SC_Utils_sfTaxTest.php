<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/**
 *
 */
class SC_Utils_sfTaxTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfTax_四捨五入の場合_四捨五入の結果になる() {
    $this->expected = array(1, 2);
    $this->actual[0] = SC_Utils::sfTax(140, 1, 1); // 1:四捨五入
    $this->actual[1] = SC_Utils::sfTax(150, 1, 1); // 1:四捨五入

    $this->verify('税額');
  }

  public function testSfTax_切り捨ての場合_切り捨ての結果になる() {
    $this->expected = array(2, 3);
    $this->actual[0] = SC_Utils::sfTax(140, 2, 2); // 2:切り捨て
    $this->actual[1] = SC_Utils::sfTax(150, 2, 2); // 2:切り捨て

    $this->verify('税額');
  }

  public function testSfTax_切り上げの場合_切り上げの結果になる() {
    $this->expected = array(2, 2);
    $this->actual[0] = SC_Utils::sfTax(140, 1, 3); // 3:切り上げ
    $this->actual[1] = SC_Utils::sfTax(150, 1, 3); // 3:切り上げ

    $this->verify('税額');
  }

  public function testSfTax_それ以外の場合_切り上げの結果になる() {
    $this->expected = array(2, 2);
    $this->actual[0] = SC_Utils::sfTax(140, 1, 4);
    $this->actual[1] = SC_Utils::sfTax(150, 1, 4);

    $this->verify('税額');
  }

}

