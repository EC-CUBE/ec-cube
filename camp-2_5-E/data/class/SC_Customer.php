<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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

    var $customer_data;		// 会員情報

    function SC_Customer() {
    }

    function getCustomerDataFromEmailPass( $pass, $email, $mobile = false ) {
        // 小文字に変換
        $email = strtolower($email);
        $sql_mobile = $mobile ? ' OR email_mobile = ?' : '';
        $arrValues = array($email);
        if ($mobile) {
            $arrValues[] = $email;
        }
        // 本登録された会員のみ
        $sql = "SELECT * FROM dtb_customer WHERE (email = ?" . $sql_mobile . ") AND del_flg = 0 AND status = 2";
        $objQuery = new SC_Query();
        $result = $objQuery->getAll($sql, $arrValues);
        if (empty($result)) {
            return false;
        } else {
            $data = $result[0];
        }

        // パスワードが合っていれば顧客情報をcustomer_dataにセットしてtrueを返す
        if ( sha1($pass . ":" . AUTH_MAGIC) == $data['password'] ){
            $this->customer_data = $data;
            $this->startSession();
            return true;
        }
        return false;
    }

    /**
     * 携帯端末IDが一致する会員が存在するかどうかをチェックする。
     *
     * @return boolean 該当する会員が存在する場合は true、それ以外の場合
     *                 は false を返す。
     */
    function checkMobilePhoneId() {
        //docomo用にデータを取り出す。
		if(SC_MobileUserAgent::getCarrier() == 'docomo'){
			if($_SESSION['mobile']['phone_id'] == "" && strlen($_SESSION['mobile']['phone_id']) == 0)
			    $_SESSION['mobile']['phone_id'] = SC_MobileUserAgent::getId();
		}
		if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
            return false;
        }

        // 携帯端末IDが一致し、本登録された会員を検索する。
        $sql = 'SELECT count(*) FROM dtb_customer WHERE mobile_phone_id = ? AND del_flg = 0 AND status = 2';
        $objQuery = new SC_Query();
        $result = $objQuery->count("dtb_customer", "mobile_phone_id = ? AND del_flg = 0 AND status = 2", array($_SESSION['mobile']['phone_id']));
        return $result > 0;
    }

    /**
     * 携帯端末IDを使用して会員を検索し、パスワードの照合を行う。
     * パスワードが合っている場合は顧客情報を取得する。
     *
     * @param string $pass パスワード
     * @return boolean 該当する会員が存在し、パスワードが合っている場合は true、
     *                 それ以外の場合は false を返す。
     */
    function getCustomerDataFromMobilePhoneIdPass($pass) {
        //docomo用にデータを取り出す。
		if(SC_MobileUserAgent::getCarrier() == 'docomo'){
			if($_SESSION['mobile']['phone_id'] == "" && strlen($_SESSION['mobile']['phone_id']) == 0)
				$_SESSION['mobile']['phone_id'] = SC_MobileUserAgent::getId();
		}
		if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
            return false;
        }

        // 携帯端末IDが一致し、本登録された会員を検索する。
        $sql = 'SELECT * FROM dtb_customer WHERE mobile_phone_id = ? AND del_flg = 0 AND status = 2';
        $objQuery = new SC_Query();
        @list($data) = $objQuery->getAll($sql, array($_SESSION['mobile']['phone_id']));

        // パスワードが合っている場合は、顧客情報をcustomer_dataに格納してtrueを返す。
        if (sha1($pass . ':' . AUTH_MAGIC) == @$data['password']) {
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

        $objQuery = new SC_Query;
        $sqlval = array('mobile_phone_id' => $_SESSION['mobile']['phone_id']);
        $where = 'customer_id = ? AND del_flg = 0 AND status = 2';
        $objQuery->update('dtb_customer', $sqlval, $where, array($this->customer_data['customer_id']));

        $this->customer_data['mobile_phone_id'] = $_SESSION['mobile']['phone_id'];
    }

    // パスワードを確認せずにログイン
    function setLogin($email) {
        // 本登録された会員のみ
        $sql = "SELECT * FROM dtb_customer WHERE (email = ? OR email_mobile = ?) AND del_flg = 0 AND status = 2";
        $objQuery = new SC_Query();
        $result = $objQuery->getAll($sql, array($email, $email));
        $data = isset($result[0]) ? $result[0] : "";
        $this->customer_data = $data;
        $this->startSession();
    }

    // セッション情報を最新の情報に更新する
    function updateSession() {
        $sql = "SELECT * FROM dtb_customer WHERE customer_id = ? AND del_flg = 0";
        $customer_id = $this->getValue('customer_id');
        $objQuery = new SC_Query();
        $arrRet = $objQuery->getAll($sql, array($customer_id));
        $this->customer_data = isset($arrRet[0]) ? $arrRet[0] : "";
        $_SESSION['customer'] = $this->customer_data;
    }

    // ログイン情報をセッションに登録し、ログに書き込む
    function startSession() {
        SC_Utils_Ex::sfDomainSessionStart();
        $_SESSION['customer'] = $this->customer_data;
        // セッション情報の保存
        GC_Utils_Ex::gfPrintLog("access : user=".$this->customer_data['customer_id'] ."\t"."ip=". $this->getRemoteHost(), CUSTOMER_LOG_PATH );
    }

    // ログアウト　$_SESSION['customer']を解放し、ログに書き込む
    function EndSession() {
        // $_SESSION['customer']の解放
        unset($_SESSION['customer']);
        $objSiteSess = new SC_SiteSession();
        $objSiteSess->unsetUniqId();
        // ログに記録する
        GC_Utils_Ex::gfPrintLog("logout : user=".$this->customer_data['customer_id'] ."\t"."ip=". $this->getRemoteHost(), CUSTOMER_LOG_PATH );
    }

    // ログインに成功しているか判定する。
    function isLoginSuccess($dont_check_email_mobile = false) {
        // ログイン時のメールアドレスとDBのメールアドレスが一致している場合
        if(isset($_SESSION['customer']['customer_id'])
            && SC_Utils_Ex::sfIsInt($_SESSION['customer']['customer_id'])) {

            $objQuery = new SC_Query();
            $email = $objQuery->get("dtb_customer", "email", "customer_id = ?", array($_SESSION['customer']['customer_id']));
            if($email == $_SESSION['customer']['email']) {
                // モバイルサイトの場合は携帯のメールアドレスが登録されていることもチェックする。
                // ただし $dont_check_email_mobile が true の場合はチェックしない。
                if (defined('MOBILE_SITE') && !$dont_check_email_mobile) {
                    $email_mobile = $objQuery->get("dtb_customer", "email_mobile", "customer_id = ?", array($_SESSION['customer']['customer_id']));
                    return isset($email_mobile);
                }
                return true;
            }
        }
        return false;
    }

    // パラメータの取得
    function getValue($keyname) {
        return isset($_SESSION['customer'][$keyname]) ? $_SESSION['customer'][$keyname] : "";
    }

    // パラメータのセット
    function setValue($keyname, $val) {
        $_SESSION['customer'][$keyname] = $val;
    }

    // パラメータがNULLかどうかの判定
    function hasValue($keyname) {
        return isset($_SESSION['customer'][$keyname]);
    }

    // 誕生日月であるかどうかの判定
    function isBirthMonth() {
        if (isset($_SESSION['customer']['birth'])) {
            $arrRet = split("[- :/]", $_SESSION['customer']['birth']);
            $birth_month = intval($arrRet[1]);
            $now_month = intval(date("m"));

            if($birth_month == $now_month) {
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
            return "";
        }
    }
}
?>
