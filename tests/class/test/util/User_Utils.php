<?php

/**
 * ユーザの状態をテストに合わせて変化させるユーティリティクラスです。
 *
 */
class User_Utils {

  /**
   * 端末種別を設定します。
   *
   * @static
   * @param deviceType  端末種別ID
   */
  public static function setDeviceType($deviceType) {
    SC_Display_Ex::setDummyDevice($deviceType);
  }

  /**
   * ログイン状態を設定します。
   *
   * @static
   * @param isLogin true:ログインしている、false:ログインしていない
   */
  public static function setLoginState($isLogin, $customer = null, $objQuery = null) {
    if (!$isLogin) {
      $_SESSION['customer']['customer_id'] = null;
      $_SESSION['customer']['email'] = null;
      return;
    }
    $customer = array_merge(self::getDefaultCustomer(), $customer);
    $_SESSION['customer']['customer_id'] = $customer['customer_id'];
    $_SESSION['customer']['email'] = $customer['email'];
    $objQuery->delete('dtb_customer', 'customer_id = ?', array($customer['customer_id']));
    $objQuery->insert('dtb_customer', $customer);
  }

  /**
   * ユーザ情報を外部から設定しなかった場合のデフォルト値を取得します。
   */
  private static function getDefaultCustomer() {
    $arrValue['customer_id'] = '999999998';
    $arrValue['name01'] = '苗字';
    $arrValue['name02'] = '名前';
    $arrValue['kana01'] = 'みょうじ';
    $arrValue['kana02'] = 'なまえ';
    $arrValue['email'] = 'sample@sample.co.jp';
    $arrValue['secret_key'] = 'aaaaaa';
    $arrValue['status'] = 2;
    $arrValue['create_date'] = 'CURRENT_TIMESTAMP';
    $arrValue['update_date'] = 'CURRENT_TIMESTAMP';

    return $arrValue;
  }
}

