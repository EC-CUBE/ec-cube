<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/**
 *
 */
class SC_Utils_sfGetCommaListTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfGetCommaList_配列が空の場合_FALSEが返る() {
    $this->expected = FALSE;
    $this->actual = SC_Utils::sfGetCommaList(array());

    $this->verify('連結済みの文字列');
  }

  public function testSfGetCommaList_スペースフラグが立っている場合_スペース付きで連結される() {
    $this->expected = 'りんご, ミカン, バナナ';
    $this->actual = SC_Utils::sfGetCommaList(
      array('りんご', 'ミカン', 'バナナ'),
      TRUE,
      array());

    $this->verify('連結済みの文字列');
  }

  public function testSfGetCommaList_スペースフラグが倒れている場合_スペース付きで連結される() {
    $this->expected = 'りんご,ミカン,バナナ';
    $this->actual = SC_Utils::sfGetCommaList(
      array('りんご', 'ミカン', 'バナナ'),
      FALSE,
      array());

    $this->verify('連結済みの文字列');
  }

  // TODO 要確認：arrpopの役割
  public function testSfGetCommaList_除外リストが指定されている場合_スペース付きで連結される() {
    $this->expected = 'りんご, バナナ';
    $this->actual = SC_Utils::sfGetCommaList(
      array('りんご', 'ミカン', 'バナナ'),
      TRUE,
      array('梨', 'ミカン', '柿'));

    $this->verify('連結済みの文字列');
  }

}

