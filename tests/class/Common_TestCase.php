<?php

$HOME = realpath(dirname(__FILE__)) . "/../..";
require_once($HOME . "/tests/class/replace/SC_Display_Ex.php");
require_once($HOME . "/tests/class/replace/SC_Response_Ex.php");
require_once($HOME . "/tests/class/test/util/Test_Utils.php");
require_once($HOME . "/tests/class/test/util/User_Utils.php");
require_once($HOME . "/tests/require.php");

require_once($HOME . "/data/class/pages/LC_Page_Index.php");
/**
 * 全テストケースの基底クラスです。
 * SC_Queryのテスト以外は基本的にこのクラスを継承して作成してください。
 *
 */
class Common_TestCase extends PHPUnit_Framework_TestCase {

  /** SC_Query インスタンス */
  protected $objQuery;

  /** 期待値 */
  protected $expected;
  /** 実際の値 */
  protected $actual;

  protected function setUp() {
    $this->objQuery = SC_Query_Ex::getSingletonInstance();
    $this->objQuery->begin();
  }

  protected function tearDown() {
    $this->objQuery->rollback();
    $this->objQuery = null;
  }

  /**
   * 各テストfunctionの末尾で呼び出し、期待値と実際の値の比較を行います。
   * 呼び出す前に、$expectedに期待値を、$actualに実際の値を導入してください。
   */
  protected function verify($message = null) {
    $this->assertEquals($this->expected, $this->actual, $message);
  }

  //////////////////////////////////////////////////////////////////
  // 以下はテスト用のユーティリティを使うためのサンプルです。
  // 実際に動作させる場合にはコメントアウトを外して下さい。

  /**
   * actionExit()呼び出しを書き換えてexit()させない例です。
   */
  /**
  public function testExit() {
    $resp = new SC_Response_Ex();
    $resp->actionExit();

    $this->expected = TRUE;
    $this->actual = $resp->isExited();
    $this->verify('exitしたかどうか');
  }
  */

  /**
   * 端末種別をテストケースから自由に設定する例です。
   */
  /**
  public function testDeviceType() {
    $this->expected = array(DEVICE_TYPE_MOBILE, DEVICE_TYPE_SMARTPHONE);
    $this->actual = array();

    // 端末種別を設定
    User_Utils::setDeviceType(DEVICE_TYPE_MOBILE);
    $this->actual[0] = SC_Display_Ex::detectDevice();
    User_Utils::setDeviceType(DEVICE_TYPE_SMARTPHONE);
    $this->actual[1] = SC_Display_Ex::detectDevice();

    $this->verify('端末種別');
  }
  */

  /**
   * ログイン状態をテストケースから自由に切り替える例です。
   */
  /**
  public function testLoginState() {
    $this->expected = array(FALSE, TRUE);
    $this->actual = array();

    $objCustomer = new SC_Customer_Ex();
    User_Utils::setLoginState(FALSE);
    $this->actual[0] = $objCustomer->isLoginSuccess();
    User_Utils::setLoginState(TRUE, null, $this->objQuery);
    $this->actual[1] = $objCustomer->isLoginSuccess();

    $this->verify('ログイン状態');
  }
  */
}

