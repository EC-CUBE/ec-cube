<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/**
 *
 */
class SC_Utils_sfTrimTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfTrim_文頭と途中にホワイトスペースがある場合_文頭だけが除去できる() {
    $this->expected = 'あ　い うえ' . chr(0x0D) . 'お';
    // 0x0A=CR, 0x0d=LF
    $this->actual = SC_Utils::sfTrim(chr(0x0A) . chr(0x0D) . ' 　あ　い うえ' . chr(0x0D) . 'お');

    $this->verify('トリム結果');
  }
  
  public function testSfTrim_途中と文末にホワイトスペースがある場合_文末だけが除去できる() {
    $this->expected = 'あ　い うえ' . chr(0x0D) . 'お';
    // 0x0A=CR, 0x0d=LF
    $this->actual = SC_Utils::sfTrim('あ　い うえ' .chr(0x0D) . 'お 　' . chr(0x0A) . chr(0x0D));

    $this->verify('トリム結果');
  }

}

