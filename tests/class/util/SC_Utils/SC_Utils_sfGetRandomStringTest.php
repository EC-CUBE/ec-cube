<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/**
 *
 */
class SC_Utils_sfGetRandomStringTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  // ランダムな文字列取得なので、文字列長のみ確認します。
  public function testSfGetRandomString_文字列長未指定の場合_長さ1の文字列が取得できる() {
    $this->expected = 1;
    $this->actual = strlen(SC_Utils::sfGetRandomString());
    
    $this->verify('文字列長');
  }

  public function testSfGetRandomString_文字列長指定ありの場合_指定した長さの文字列が取得できる() {
    $this->expected = 10;
    $this->actual = strlen(SC_Utils::sfGetRandomString(10));
    
    $this->verify('文字列長');
  }

}

