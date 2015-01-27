<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework;

use Eccube\Application;
use Eccube\Framework\Display;
use Eccube\Framework\MobileUserAgent;
use Eccube\Framework\Query;
use Eccube\Framework\SiteSession;
use Eccube\Framework\Helper\PurchaseHelper;
use Eccube\Framework\Helper\SessionHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

/*  [名称] Customer
 *  [概要] 会員管理クラス
 */
class Customer
{
    /** 会員情報 */
    public $customer_data;

    /**
     * @param string $email
     * @param string $pass
     */
    public function getCustomerDataFromEmailPass($pass, $email, $mobile = false)
    {
        // 小文字に変換
        $email = strtolower($email);
        $sql_mobile = $mobile ? ' OR email_mobile = ?' : '';
        $arrValues = array($email);
        if ($mobile) {
            $arrValues[] = $email;
        }
        // 本登録された会員のみ
        $sql = 'SELECT * FROM dtb_customer WHERE (email = ?' . $sql_mobile . ') AND del_flg = 0 AND status = 2';
        $objQuery = Application::alias('eccube.query');
        $result = $objQuery->getAll($sql, $arrValues);
        if (empty($result)) {
            return false;
        } else {
            $data = $result[0];
        }

        // パスワードが合っていれば会員情報をcustomer_dataにセットしてtrueを返す
        if (Utils::sfIsMatchHashPassword($pass, $data['password'], $data['salt'])) {
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
    public function checkMobilePhoneId()
    {
        //docomo用にデータを取り出す。
        if (MobileUserAgent::getCarrier() == 'docomo') {
            if ($_SESSION['mobile']['phone_id'] == '' && strlen($_SESSION['mobile']['phone_id']) == 0) {
                $_SESSION['mobile']['phone_id'] = MobileUserAgent::getId();
            }
        }
        if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
            return false;
        }

        // 携帯端末IDが一致し、本登録された会員を検索する。
        $objQuery = Application::alias('eccube.query');
        $exists = $objQuery->exists('dtb_customer', 'mobile_phone_id = ? AND del_flg = 0 AND status = 2', array($_SESSION['mobile']['phone_id']));

        return $exists;
    }

    /**
     * 携帯端末IDを使用して会員を検索し、パスワードの照合を行う。
     * パスワードが合っている場合は会員情報を取得する。
     *
     * @param  string  $pass パスワード
     * @return boolean 該当する会員が存在し、パスワードが合っている場合は true、
     *                 それ以外の場合は false を返す。
     */
    public function getCustomerDataFromMobilePhoneIdPass($pass)
    {
        //docomo用にデータを取り出す。
        if (MobileUserAgent::getCarrier() == 'docomo') {
            if ($_SESSION['mobile']['phone_id'] == '' && strlen($_SESSION['mobile']['phone_id']) == 0) {
                $_SESSION['mobile']['phone_id'] = MobileUserAgent::getId();
            }
        }
        if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
            return false;
        }

        // 携帯端末IDが一致し、本登録された会員を検索する。
        $sql = 'SELECT * FROM dtb_customer WHERE mobile_phone_id = ? AND del_flg = 0 AND status = 2';
        $objQuery = Application::alias('eccube.query');
        @list($data) = $objQuery->getAll($sql, array($_SESSION['mobile']['phone_id']));

        // パスワードが合っている場合は、会員情報をcustomer_dataに格納してtrueを返す。
        if (Utils::sfIsMatchHashPassword($pass, $data['password'], $data['salt'])) {
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
    public function updateMobilePhoneId()
    {
        if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
            return;
        }

        if ($this->customer_data['mobile_phone_id'] == $_SESSION['mobile']['phone_id']) {
            return;
        }

        $objQuery = Application::alias('eccube.query');
        $sqlval = array('mobile_phone_id' => $_SESSION['mobile']['phone_id']);
        $where = 'customer_id = ? AND del_flg = 0 AND status = 2';
        $objQuery->update('dtb_customer', $sqlval, $where, array($this->customer_data['customer_id']));

        $this->customer_data['mobile_phone_id'] = $_SESSION['mobile']['phone_id'];
    }

    // パスワードを確認せずにログイン
    public function setLogin($email)
    {
        // 本登録された会員のみ
        $sql = 'SELECT * FROM dtb_customer WHERE (email = ? OR email_mobile = ?) AND del_flg = 0 AND status = 2';
        $objQuery = Application::alias('eccube.query');
        $result = $objQuery->getAll($sql, array($email, $email));
        $data = isset($result[0]) ? $result[0] : '';
        $this->customer_data = $data;
        $this->startSession();
    }

    // セッション情報を最新の情報に更新する
    public function updateSession()
    {
        $sql = 'SELECT * FROM dtb_customer WHERE customer_id = ? AND del_flg = 0';
        $customer_id = $this->getValue('customer_id');
        $objQuery = Application::alias('eccube.query');
        $arrRet = $objQuery->getAll($sql, array($customer_id));
        $this->customer_data = isset($arrRet[0]) ? $arrRet[0] : '';
        $_SESSION['customer'] = $this->customer_data;
    }

    // ログイン情報をセッションに登録し、ログに書き込む
    public function startSession()
    {
        $_SESSION['customer'] = $this->customer_data;
        // セッション情報の保存
        GcUtils::gfPrintLog('access : user='.$this->customer_data['customer_id'] ."\t".'ip='. $this->getRemoteHost(), CUSTOMER_LOG_REALFILE, false);
    }

    // ログアウト　$_SESSION['customer']を解放し、ログに書き込む
    public function EndSession()
    {
        // セッション情報破棄の前にcustomer_idを保存
        $customer_id = $_SESSION['customer']['customer_id'];

        // $_SESSION['customer']の解放
        unset($_SESSION['customer']);
        // セッションの配送情報を全て破棄する
        PurchaseHelper::unsetAllShippingTemp(true);
        // トランザクショントークンの破棄
        SessionHelper::destroyToken();
        /* @var $objSiteSess SiteSession */
        $objSiteSess = Application::alias('eccube.site_session');
        $objSiteSess->unsetUniqId();

        // ログに記録する
        $log = sprintf("logout : user=%d\tip=%s",
            $customer_id, $this->getRemoteHost());
        GcUtils::gfPrintLog($log, CUSTOMER_LOG_REALFILE, false);
    }

    // ログインに成功しているか判定する。
    public function isLoginSuccess($dont_check_email_mobile = false)
    {
        // ログイン時のメールアドレスとDBのメールアドレスが一致している場合
        if (isset($_SESSION['customer']['customer_id'])
            && Utils::sfIsInt($_SESSION['customer']['customer_id'])
        ) {
            $objQuery = Application::alias('eccube.query');
            $email = $objQuery->get('email', 'dtb_customer', 'customer_id = ?', array($_SESSION['customer']['customer_id']));
            if ($email == $_SESSION['customer']['email']) {
                // モバイルサイトの場合は携帯のメールアドレスが登録されていることもチェックする。
                // ただし $dont_check_email_mobile が true の場合はチェックしない。
                if (Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE && !$dont_check_email_mobile) {
                    $email_mobile = $objQuery->get('email_mobile', 'dtb_customer', 'customer_id = ?', array($_SESSION['customer']['customer_id']));

                    return isset($email_mobile);
                }

                return true;
            }
        }

        return false;
    }

    // パラメーターの取得
    public function getValue($keyname)
    {
        // ポイントはリアルタイム表示
        if ($keyname == 'point') {
            $objQuery = Application::alias('eccube.query');
            $point = $objQuery->get('point', 'dtb_customer', 'customer_id = ?', array($_SESSION['customer']['customer_id']));
            $_SESSION['customer']['point'] = $point;

            return $point;
        } else {
            return isset($_SESSION['customer'][$keyname]) ? $_SESSION['customer'][$keyname] : '';
        }
    }

    // パラメーターのセット

    /**
     * @param string $keyname
     * @param string $val
     */
    public function setValue($keyname, $val)
    {
        $_SESSION['customer'][$keyname] = $val;
    }

    // パラメーターがNULLかどうかの判定

    /**
     * @param string $keyname
     */
    public function hasValue($keyname)
    {
        if (isset($_SESSION['customer'][$keyname])) {
            return !Utils::isBlank($_SESSION['customer'][$keyname]);
        }

        return false;
    }

    // 誕生日月であるかどうかの判定
    public function isBirthMonth()
    {
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
    public function getRemoteHost()
    {
        if (!empty($_SERVER['REMOTE_HOST'])) {
            return $_SERVER['REMOTE_HOST'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        } else {
            return '';
        }
    }

    //受注関連の会員情報を更新
    public function updateOrderSummary($customer_id)
    {
        $objQuery = Application::alias('eccube.query');

        $col = <<< __EOS__
            SUM( payment_total) AS buy_total,
            COUNT(order_id) AS buy_times,
            MAX( create_date) AS last_buy_date,
            MIN(create_date) AS first_buy_date
__EOS__;
        $table = 'dtb_order';
        $where = 'customer_id = ? AND del_flg = 0 AND status <> ?';
        $arrWhereVal = array($customer_id, ORDER_CANCEL);
        $arrOrderSummary = $objQuery->getRow($col, $table, $where, $arrWhereVal);

        $objQuery->update('dtb_customer', $arrOrderSummary, 'customer_id = ?', array($customer_id));
    }

    /**
     * ログインを実行する.
     *
     * ログインを実行し, 成功した場合はユーザー情報をセッションに格納し,
     * true を返す.
     * モバイル端末の場合は, 携帯端末IDを保存する.
     * ログインに失敗した場合は, false を返す.
     *
     * @param  string  $login_email ログインメールアドレス
     * @param  string  $login_pass  ログインパスワード
     * @return boolean|null ログインに成功した場合 true; 失敗した場合 false
     */
    public function doLogin($login_email, $login_pass)
    {
        switch (Application::alias('eccube.display')->detectDevice()) {
            case DEVICE_TYPE_MOBILE:
                if (!$this->getCustomerDataFromMobilePhoneIdPass($login_pass) &&
                    !$this->getCustomerDataFromEmailPass($login_pass, $login_email, true)
                ) {
                    return false;
                } else {
                    // Session Fixation対策
                    SessionHelper::regenerateSID();

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
                    // Session Fixation対策
                    SessionHelper::regenerateSID();

                    return true;
                }
                break;
        }
    }
}
