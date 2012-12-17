<?php
require_once(realpath(dirname(__FILE__)) . "/../../../data/class/SC_Display.php");
/**
 * テスト用にSC_Displayクラスを変更してユーザエージェントを自在に設定できるようにしたクラスです。
 */
class SC_Display_Ex extends SC_Display {

  /** テスト用に設定した端末種別 */
  static $dummyDevice = DEVICE_TYPE_PC;

  /**
   * 予めテスト用に設定された端末種別を取得します。
   * @static
   * @param     $reset  boolean
   * @return    integer 端末種別ID
   */
  public static function detectDevice($reset = FALSE) {
    return self::$dummyDevice;
  }

  /**
   * テスト用に端末種別を設定します。
   *
   * @static
   * @param     $deviceType 端末種別ID
   */
  public static function setDummyDevice($deviceType) {
    self::$dummyDevice = $deviceType;
  }
}


