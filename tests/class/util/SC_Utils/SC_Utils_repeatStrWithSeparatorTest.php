<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/**
 *
 */
class SC_Utils_repeatStrWithSeparatorTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testRepeatStrWithSeparator_反復回数が0回の場合_結果が0バイト文字列になる() {
    $this->expected = '';
    $this->actual = SC_Utils::repeatStrWithSeparator('ECサイト', 0, '#');

    $this->verify('連結済みの文字列');
  }

  public function testRepeatStrWithSeparator_反復回数が1回の場合_区切り文字が入らない() {
    $this->expected = 'ECサイト';
    $this->actual = SC_Utils::repeatStrWithSeparator('ECサイト', 1, '#');

    $this->verify('連結済みの文字列');
  }

  public function testRepeatStrWithSeparator_反復回数が2回以上の場合_区切り文字が入って出力される() {
    $this->expected = 'ECサイト#ECサイト#ECサイト#ECサイト#ECサイト';
    $this->actual = SC_Utils::repeatStrWithSeparator('ECサイト', 5, '#');

    $this->verify('連結済みの文字列');
  }

  public function testRepeatStrWithSeparator_区切り文字が未指定の場合_カンマ区切りとなる() {
    $this->expected = 'ECサイト,ECサイト,ECサイト';
    $this->actual = SC_Utils::repeatStrWithSeparator('ECサイト', 3);

    $this->verify('連結済みの文字列');
  }

}

