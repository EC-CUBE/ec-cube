<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/*  [名称] SC_Customer
 *  [概要] 会員管理クラス
 */
class SC_Customer {

    /** 会員情報 */
    var $customer_data;

    function getCustomerDataFromEmailPass($pass, $email, $mobile = false) {
        // 小文字に変換
        $email = strtolower($email);
        $sql_mobile = $mobile ? ' OR email_mobile = ?' : '';
        $arrValues = array($email);
        if ($mobile) {
            $arrValues[] = $email;
        }
        // 本登録された会員のみ
        $sql = 'SELECT * FROM dtb_customer WHERE (email = ?' . $sql_mobile . ') AND del_flg = 0 AND status = 2';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $result = $objQuery->getAll($sql, $arrValues);
        if (empty($result)) {
            return false;
        } else {
            $data = $result[0];
        }

        // パスワードが合っていれば会員情報をcustomer_dataにセットしてtrueを返す
        if (SC_Utils_Ex::sfIsMatchHashPassword($pass, $data['password'], $data['salt'])) {
            $this->customer_data = $data;
            $this->startSession();
            return true;
        }
        return false;
    }

    /**
     * 携帯端末IDが一致する会員が存在するかどうかをチェックする。
     * FIXME
     * @return boolean 該当する会員が存在する場合は true、それ以外の場合
     *                 は false を返す。
     */
    function checkMobilePhoneId() {
        //docomo用にデータを取り出す。
        if (SC_MobileUserAgent_Ex::getCarrier() == 'docomo') {
            if ($_SESSION['mobile']['phone_id'] == '' && strlen($_SESSION['mobile']['phone_id']) == 0) {
                $_SESSION['mobile']['phone_id'] = SC_MobileUserAgent_Ex::getId();
            }
        }
        if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
            return false;
        }

        // 携帯端末IDが一致し、本登録された会員を検索する。
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $exists = $objQuery->exists('dtb_customer', 'mobile_phone_id = ? AND del_flg = 0 AND status = 2', array($_SESSION['mobile']['phone_id']));
        return $exists;
    }

    /**
     * 携帯端末IDを使用して会員を検索し、パスワードの照合を行う。
     * パスワードが合っている場合は会員情報を取得する。
     *
     * @param string $pass パスワード
     * @return boolean 該当する会員が存在し、パスワードが合っている場合は true、
     *                 それ以外の場合は false を返す。
     */
    function getCustomerDataFromMobilePhoneIdPass($pass) {
        //docomo用にデータを取り出す。
        if (SC_MobileUserAgent_Ex::getCarrier() == 'docomo') {
            if ($_SESSION['mobile']['phone_id'] == '' && strlen($_SESSION['mobile']['phone_id']) == 0) {
                $_SESSION['mobile']['phone_id'] = SC_MobileUserAgent_Ex::getId();
            }
        }
        if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
            return false;
        }

        // 携帯端末IDが一致し、本登録された会員を検索する。
        $sql = 'SELECT * FROM dtb_customer WHERE mobile_phone_id = ? AND del_flg = 0 AND status = 2';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        @list($data) = $objQuery->getAll($sql, array($_SESSION['mobile']['phone_id']));

        // パスワードが合っている場合は、会員情報をcustomer_dataに格納してtrueを返す。
        if (SC_Utils_Ex::sfIsMatchHashPassword($pass, $data['password'], $data['salt'])) {
            $this->customer_data = $data;
            $this->startSession();
            return true;
        }
        return false;
    }

    /**
     * 携帯端末IDを登録する。
     *
     * @return void
     */
    function updateMobilePhoneId() {
        if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
            return;
        }

        if ($this->customer_data['mobile_phone_id'] == $_SESSION['mobile']['phone_id']) {
            return;
        }

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sqlval = array('mobile_phone_id' => $_SESSION['mobile']['phone_id']);
        $where = 'customer_id = ? AND del_flg = 0 AND status = 2';
        $objQuery->update('dtb_customer', $sqlval, $where, array($this->customer_data['customer_id']));

        $this->customer_data['mobile_phone_id'] = $_SESSION['mobile']['phone_id'];
    }

    // パスワードを確認せずにログイン
    function setLogin($email) {
        // 本登録された会員のみ
        $sql = 'SELECT * FROM dtb_customer WHERE (email = ? OR email_mobile = ?) AND del_flg = 0 AND status = 2';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $result = $objQuery->getAll($sql, array($email, $email));
        $data = isset($result[0]) ? $result[0] : '';
        $this->customer_data = $data;
        $this->startSession();
    }

    // セッション情報を最新の情報に更新する
    function updateSession() {
        $sql = 'SELECT * FROM dtb_customer WHERE customer_id = ? AND del_flg = 0';
        $customer_id = $this->getValue('customer_id');
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrRet = $objQuery->getAll($sql, array($customer_id));
        $this->customer_data = isset($arrRet[0]) ? $arrRet[0] : '';
        $_SESSION['customer'] = $this->customer_data;
    }

    // ログイン情報をセッションに登録し、ログに書き込む
    function startSession() {
        $_SESSION['customer'] = $this->customer_data;
        // セッション情報の保存
        GC_Utils_Ex::gfPrintLog('access : user='.$this->customer_data['customer_id'] ."\t".'ip='. $this->getRemoteHost(), CUSTOMER_LOG_REALFILE, false);
    }

    // ログアウト　$_SESSION['customer']を解放し、ログに書き込む
    function EndSession() {
        // セッション情報破棄の前にcustomer_idを保存
        $customer_id = $_SESSION['customer']['customer_id'];

        // $_SESSION['customer']の解放
        unset($_SESSION['customer']);
        // セッションの配送情報を全て破棄する
        SC_Helper_Purchase_Ex::unsetAllShippingTemp(true);
        // トランザクショントークンの破棄
        SC_Helper_Session_Ex::destroyToken();
        $objSiteSess = new SC_SiteSession_Ex();
        $objSiteSess->unsetUniqId();

        // ログに記録する
        $log = sprintf("logout : user=%d\tip=%s",
            $customer_id, $this->getRemoteHost());
        GC_Utils_Ex::gfPrintLog($log, CUSTOMER_LOG_REALFILE, false);
    }

    // ログインに成功しているか判定する。
    function isLoginSuccess($dont_check_email_mobile = false) {
        // ログイン時のメールアドレスとDBのメールアドレスが一致している場合
        if (isset($_SESSION['customer']['customer_id'])
            && SC_Utils_Ex::sfIsInt($_SESSION['customer']['customer_id'])
        ) {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $email = $objQuery->get('email', 'dtb_customer', 'customer_id = ?', array($_SESSION['customer']['customer_id']));
            if ($email == $_SESSION['customer']['email']) {
                // モバイルサイトの場合は携帯のメールアドレスが登録されていることもチェックする。
                // ただし $dont_check_email_mobile が true の場合はチェックしない。
                if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE && !$dont_check_email_mobile) {
                    $email_mobile = $objQuery->get('email_mobile', 'dtb_customer', 'customer_id = ?', array($_SESSION['customer']['customer_id']));
                    return isset($email_mobile);
                }
                return true;
            }
        }
        return false;
    }

    // パラメーターの取得
    function getValue($keyname) {
        // ポイントはリアルタイム表示
        if ($keyname == 'point') {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $point = $objQuery->get('point', 'dtb_customer', 'customer_id = ?', array($_SESSION['customer']['customer_id']));
            $_SESSION['customer']['point'] = $point;
            return $point;
        } else {
            return isset($_SESSION['customer'][$keyname]) ? $_SESSION['customer'][$keyname] : '';
        }
    }

    // パラメーターのセット
    function setValue($keyname, $val) {
        $_SESSION['customer'][$keyname] = $val;
    }

    // パラメーターがNULLかどうかの判定
    function hasValue($keyname) {
        if (isset($_SESSION['customer'][$keyname])) {
            return !SC_Utils_Ex::isBlank($_SESSION['customer'][$keyname]);
        }
        return false;
    }

    // 誕生日月であるかどうかの判定
    function isBirthMonth() {
        if (isset($_SESSION['customer']['birth'])) {
            $arrRet = preg_split('|[- :/]|', $_SESSION['customer']['birth']);
            $birth_month = intval($arrRet[1]);
            $now_month = intval(date('m'));

            if ($birth_month == $now_month) {
                return true;
            }
        }
        return false;
    }

    /**
     * $_SERVER['REMOTE_HOST'] または $_SERVER['REMOTE_ADDR'] を返す.
     *
     * $_SERVER['REMOTE_HOST'] が取得できない場合は $_SERVER['REMOTE_ADDR']
     * を返す.
     *
     * @return string $_SERVER['REMOTE_HOST'] 又は $_SERVER['REMOTE_ADDR']の文字列
     */
    function getRemoteHost() {

        if (!empty($_SERVER['REMOTE_HOST'])) {
            return $_SERVER['REMOTE_HOST'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        } else {
            return '';
        }
    }

    //受注関連の会員情報を更新
    function updateOrderSummary($customer_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrOrderSummary =  $objQuery->getRow('SUM( payment_total) as buy_total, COUNT(order_id) as buy_times,MAX( create_date) as last_buy_date, MIN(create_date) as first_buy_date','dtb_order','customer_id = ? AND del_flg = 0 AND status <> ?',array($customer_id,ORDER_CANCEL));
        $objQuery->update('dtb_customer',$arrOrderSummary,'customer_id = ?',array($customer_id));
    }

    /**
     * ログインを実行する.
     *
     * ログインを実行し, 成功した場合はユーザー情報をセッションに格納し,
     * true を返す.
     * モバイル端末の場合は, 携帯端末IDを保存する.
     * ログインに失敗した場合は, false を返す.
     *
     * @param string $login_email ログインメールアドレス
     * @param string $login_pass ログインパスワード
     * @return boolean ログインに成功した場合 true; 失敗した場合 false
     */
    function doLogin($login_email, $login_pass) {
        switch (SC_Display_Ex::detectDevice()) {
            case DEVICE_TYPE_MOBILE:
                if (!$this->getCustomerDataFromMobilePhoneIdPass($login_pass) &&
                    !$this->getCustomerDataFromEmailPass($login_pass, $login_email, true)
                ) {
                    return false;
                } else {
                    $this->updateMobilePhoneId();
                    return true;
                }
                break;

            case DEVICE_TYPE_SMARTPHONE:
            case DEVICE_TYPE_PC:
            default:
                if (!$this->getCustomerDataFromEmailPass($login_pass, $login_email)) {
                    return false;
                } else {
                    return true;
                }
                break;
        }
    }
}
