<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/**
 *
 */
class SC_Utils_getTableTagTest extends Common_TestCase {

  protected function setUp() {
    parent::setUp();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  // TODO 要確認（現在は使われていないが、ソースコードの意味が不明確）
  public function testGetTableTag__配列の内容がHTMLに変換される() {
    $this->expected = 
      '<table>' .
      '<tr><th>名前</th><th>住所</th><th>電話番号</th></tr>' .
      '<tr><td>名前1</td><td>住所1</td><td>12345678901</td></tr>' .
      '<tr><td>名前2</td><td>住所2</td><td>12345678902</td></tr>' .
      '<tr><td>名前3</td><td>住所3</td><td>12345678903</td></tr>' .
      '</table>';
    $this->actual = SC_Utils::getTableTag(array(
      array('名前', '住所', '電話番号'),
      array('名前1', '住所1', '12345678901'),
      array('名前2', '住所2', '12345678902'),
      array('名前2', '住所3', '12345678903'),
    ));

    $this->verify('生成されたHTML');
  }

}

