<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/**
 *
 */
class SC_Utils_sfIsZeroFillingTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfIsZeroFilling_桁数が1の場合_FALSEを返す() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsZeroFilling('0');

    $this->verify('ゼロ詰めされているかどうか');
  }

  public function testSfIsZeroFilling_桁数が2以上で0埋めされていない場合_FALSEを返す() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfIsZeroFilling('12');

    $this->verify('ゼロ詰めされているかどうか');
  }

  public function testSfIsZeroFilling_桁数が2以上で0埋めされている場合_TRUEを返す() {
    $this->expected = TRUE;
    $this->actual = SC_Utils::sfIsZeroFilling('01');

    $this->verify('ゼロ詰めされているかどうか');
  }

}

