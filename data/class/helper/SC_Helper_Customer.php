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

/**
 * 会員情報の登録・編集・検索ヘルパークラス.
 *
 *
 * @package Helper
 * @author Hirokazu Fukuda
 * @version $Id$
 */
class SC_Helper_Customer {

    /**
     * 会員情報の登録・編集処理を行う.
     *
     * @param array $array 登録するデータの配列（SC_FormParamのgetDbArrayの戻り値）
     * @param array $customer_id nullの場合はinsert, 存在する場合はupdate
     * @access public
     * @return integer 登録編集したユーザーのcustomer_id
     */
    function sfEditCustomerData($array, $customer_id = null) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        $array['update_date'] = 'CURRENT_TIMESTAMP';    // 更新日

        // salt値の生成(insert時)または取得(update時)。
        if (is_numeric($customer_id)) {
            $salt = $objQuery->get('salt', 'dtb_customer', 'customer_id = ? ', array($customer_id));

            // 旧バージョン(2.11未満)からの移行を考慮
            if (empty($salt)) $old_version_flag = true;
        } else {
            $salt = SC_Utils_Ex::sfGetRandomString(10);
            $array['salt'] = $salt;
        }
        //-- パスワードの更新がある場合は暗号化
        if ($array['password'] == DEFAULT_PASSWORD or $array['password'] == '') {
            //更新しない
            unset($array['password']);
        } else {
            // 旧バージョン(2.11未満)からの移行を考慮
            if ($old_version_flag) {
                $is_password_updated = true;
                $salt = SC_Utils_Ex::sfGetRandomString(10);
                $array['salt'] = $salt;
            }

            $array['password'] = SC_Utils_Ex::sfGetHashString($array['password'], $salt);
        }
        //-- 秘密の質問の更新がある場合は暗号化
        if ($array['reminder_answer'] == DEFAULT_PASSWORD or $array['reminder_answer'] == '') {
            //更新しない
            unset($array['reminder_answer']);

            // 旧バージョン(2.11未満)からの移行を考慮
            if ($old_version_flag && $is_password_updated) {
                // パスワードが更新される場合は、平文になっている秘密の質問を暗号化する
                $reminder_answer = $objQuery->get('reminder_answer', 'dtb_customer', 'customer_id = ? ', array($customer_id));
                $array['reminder_answer'] = SC_Utils_Ex::sfGetHashString($reminder_answer, $salt);
            }
        } else {
            // 旧バージョン(2.11未満)からの移行を考慮
            if ($old_version_flag && !$is_password_updated) {
                // パスワードが更新されない場合は、平文のままにする
                unset($array['salt']);
            } else {
                $array['reminder_answer'] = SC_Utils_Ex::sfGetHashString($array['reminder_answer'], $salt);
            }
        }

        //-- 編集登録実行
        if (is_numeric($customer_id)) {
            // 編集
            $objQuery->update('dtb_customer', $array, 'customer_id = ? ', array($customer_id));
        } else {
            // 新規登録

            // 会員ID
            $customer_id = $objQuery->nextVal('dtb_customer_customer_id');
            $array['customer_id'] = $customer_id;
            // 作成日
            if (is_null($array['create_date'])) {
                $array['create_date'] = 'CURRENT_TIMESTAMP';
            }
            $objQuery->insert('dtb_customer', $array);
        }

        $objQuery->commit();
        return $customer_id;
    }

    /**
     * 注文番号、利用ポイント、加算ポイントから最終ポイントを取得する.
     *
     * @param integer $order_id 注文番号
     * @param integer $use_point 利用ポイント
     * @param integer $add_point 加算ポイント
     * @return array 最終ポイントの配列
     */
    function sfGetCustomerPoint($order_id, $use_point, $add_point) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrRet = $objQuery->select('customer_id', 'dtb_order', 'order_id = ?', array($order_id));
        $customer_id = $arrRet[0]['customer_id'];
        if ($customer_id != '' && $customer_id >= 1) {
            if (USE_POINT !== false) {
                $arrRet = $objQuery->select('point', 'dtb_customer', 'customer_id = ?', array($customer_id));
                $point = $arrRet[0]['point'];
                $total_point = $arrRet[0]['point'] - $use_point + $add_point;
            } else {
                $total_point = 0;
                $point = 0;
            }
        } else {
            $total_point = '';
            $point = '';
        }
        return array($point, $total_point);
    }

    /**
     * emailアドレスから、登録済み会員や退会済み会員をチェックする
     *
     * XXX SC_CheckError からしか呼び出されず, 本クラスの中で SC_CheckError を呼び出している
     *
     * @param string $email  メールアドレス
     * @return integer  0:登録可能     1:登録済み   2:再登録制限期間内削除ユーザー  3:自分のアドレス
     */
    function sfCheckRegisterUserFromEmail($email) {
        $objCustomer = new SC_Customer_Ex();
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // ログインしている場合、すでに登録している自分のemailの場合
        if ($objCustomer->isLoginSuccess(true)
            && SC_Helper_Customer_Ex::sfCustomerEmailDuplicationCheck($objCustomer->getValue('customer_id'), $email)) {
            // 自分のアドレス
            return 3;
        }

        $arrRet = $objQuery->select('email, update_date, del_flg',
            'dtb_customer',
            'email = ? OR email_mobile = ? ORDER BY del_flg',
            array($email, $email));

        if (count($arrRet) > 0) {
            // 会員である場合
            if ($arrRet[0]['del_flg'] != '1') {
                // 登録済み
                return 1;
            } else {
                // 退会した会員である場合
                $leave_time = SC_Utils_Ex::sfDBDatetoTime($arrRet[0]['update_date']);
                $now_time   = time();
                $pass_time  = $now_time - $leave_time;
                // 退会から何時間-経過しているか判定する。
                $limit_time = ENTRY_LIMIT_HOUR * 3600;
                if ($pass_time < $limit_time) {
                    // 再登録制限期間内削除ユーザー
                    return 2;
                }
            }
        }

        // 登録可能
        return 0;
    }

    /**
     * ログイン時メールアドレス重複チェック.
     *
     * 会員の保持する email, mobile_email が, 引数 $email と一致するかチェックする
     *
     * @param integer $customer_id チェック対象会員の会員ID
     * @param string $email チェック対象のメールアドレス
     * @return boolean メールアドレスが重複する場合 true
     */
    function sfCustomerEmailDuplicationCheck($customer_id, $email) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $arrResults = $objQuery->getRow('email, email_mobile',
                                        'dtb_customer', 'customer_id = ?',
                                        array($customer_id));
        $return
            =  strlen($arrResults['email']) >= 1 && $email === $arrResults['email']
            || strlen($arrResults['email_mobile']) >= 1 &&  $email === $arrResults['email_mobile']
        ;
        return $return;
    }

    /**
     * customer_idから会員情報を取得する
     *
     * @param mixed $customer_id
     * @param mixed $mask_flg
     * @access public
     * @return array 会員情報の配列を返す
     */
    function sfGetCustomerData($customer_id, $mask_flg = true) {
        $objQuery       =& SC_Query_Ex::getSingletonInstance();

        // 会員情報DB取得
        $ret        = $objQuery->select('*','dtb_customer','customer_id=?', array($customer_id));
        $arrForm    = $ret[0];

        // 確認項目に複製
        $arrForm['email02'] = $arrForm['email'];
        $arrForm['email_mobile02'] = $arrForm['email_mobile'];

        // 誕生日を年月日に分ける
        if (isset($arrForm['birth'])) {
            $birth = explode(' ', $arrForm['birth']);
            list($arrForm['year'], $arrForm['month'], $arrForm['day']) = explode('-',$birth[0]);
        }

        if ($mask_flg) {
            $arrForm['password']          = DEFAULT_PASSWORD;
            $arrForm['password02']        = DEFAULT_PASSWORD;
            $arrForm['reminder_answer']   = DEFAULT_PASSWORD;
        }
        return $arrForm;
    }

    /**
     * 会員ID指定またはwhere条件指定での会員情報取得(単一行データ)
     *
     * TODO: sfGetCustomerDataと統合したい
     *
     * @param integer $customer_id 会員ID (指定無しでも構わないが、Where条件を入れる事)
     * @param string $add_where 追加WHERE条件
     * @param array $arrAddVal 追加WHEREパラメーター
     * @access public
     * @return array 対象会員データ
     */
    function sfGetCustomerDataFromId($customer_id, $add_where = '', $arrAddVal = array()) {
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        if ($add_where == '') {
            $where = 'customer_id = ?';
            $arrData = $objQuery->getRow('*', 'dtb_customer', $where, array($customer_id));
        } else {
            $where = $add_where;
            if (SC_Utils_Ex::sfIsInt($customer_id)) {
                $where .= ' AND customer_id = ?';
                $arrAddVal[] = $customer_id;
            }
            $arrData = $objQuery->getRow('*', 'dtb_customer', $where, $arrAddVal);
        }
        return $arrData;
    }

    /**
     * 重複しない会員登録キーを発行する。
     *
     * @access public
     * @return string 会員登録キーの文字列
     */
    function sfGetUniqSecretKey() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        do {
            $uniqid = SC_Utils_Ex::sfGetUniqRandomId('r');
            $exists = $objQuery->exists('dtb_customer', 'secret_key = ?', array($uniqid));
        } while ($exists);
        return $uniqid;
    }

    /**
     * 会員登録キーから会員IDを取得する.
     *
     * @param string $uniqid 会員登録キー
     * @param boolean $check_status 本会員のみを対象とするか
     * @access public
     * @return integer 会員ID
     */
    function sfGetCustomerId($uniqid, $check_status = false) {
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $where      = 'secret_key = ?';

        if ($check_status) {
            $where .= ' AND status = 1 AND del_flg = 0';
        }

        return $objQuery->get('customer_id', 'dtb_customer', $where, array($uniqid));
    }

    /**
     * 会員登録時フォーム初期化
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param boolean $isAdmin true:管理者画面 false:会員向け
     * @access public
     * @return void
     */
    function sfCustomerEntryParam(&$objFormParam, $isAdmin = false) {
        SC_Helper_Customer_Ex::sfCustomerCommonParam($objFormParam);
        SC_Helper_Customer_Ex::sfCustomerRegisterParam($objFormParam, $isAdmin);
        if ($isAdmin) {
            $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_ID'), 'customer_id', INT_LEN, 'n', array('NUM_CHECK'));
            $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_EMAIL_MOBILE'), 'email_mobile', null, 'a', array('NO_SPTAB', 'EMAIL_CHECK', 'SPTAB_CHECK' ,'EMAIL_CHAR_CHECK', 'MOBILE_EMAIL_CHECK'));
            $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_STATUS'), 'status', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
            $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_NOTE'), 'note', LTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
            $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_POINT'), 'point', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK'), 0);
        }

        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
            // 登録確認画面の「戻る」ボタンのためのパラメーター
            $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_RETURN'), 'return', '', '', array(), '', false);
        }
    }

    /**
     * 会員情報変更フォーム初期化
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return void
     */
    function sfCustomerMypageParam(&$objFormParam) {
        SC_Helper_Customer_Ex::sfCustomerCommonParam($objFormParam);
        SC_Helper_Customer_Ex::sfCustomerRegisterParam($objFormParam, false, true);
        if (SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE) {
            $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_EMAIL_MOBILE'), 'email_mobile', null, 'a', array('NO_SPTAB', 'EMAIL_CHECK', 'SPTAB_CHECK' ,'EMAIL_CHAR_CHECK', 'MOBILE_EMAIL_CHECK'));
            $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_EMAIL_MOBILE_CONFIRM'), 'email_mobile02', null, 'a', array('NO_SPTAB', 'EMAIL_CHECK','SPTAB_CHECK' , 'EMAIL_CHAR_CHECK', 'MOBILE_EMAIL_CHECK'), '', false);
        } else {
            $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_EMAIL_MOBILE'), 'email_mobile', null, 'a', array('EXIST_CHECK', 'NO_SPTAB', 'EMAIL_CHECK', 'SPTAB_CHECK' ,'EMAIL_CHAR_CHECK', 'MOBILE_EMAIL_CHECK'));
            $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_EMAIL'), 'email', null, 'a', array('NO_SPTAB', 'EMAIL_CHECK', 'SPTAB_CHECK' ,'EMAIL_CHAR_CHECK'));
        }
    }

    /**
     * 会員共通
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return void
     */
    function sfCustomerCommonParam(&$objFormParam) {
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_LASTNAME'), 'name01', STEXT_LEN, 'aKV', array('EXIST_CHECK', 'NO_SPTAB', 'SPTAB_CHECK' ,'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_FIRSTNAME'), 'name02', STEXT_LEN, 'aKV', array('EXIST_CHECK', 'NO_SPTAB', 'SPTAB_CHECK' , 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_LASTKANA'), 'kana01', STEXT_LEN, 'CKV', array('EXIST_CHECK', 'NO_SPTAB', 'SPTAB_CHECK' ,'MAX_LENGTH_CHECK', 'KANA_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_FIRSTKANA'), 'kana02', STEXT_LEN, 'CKV', array('EXIST_CHECK', 'NO_SPTAB', 'SPTAB_CHECK' ,'MAX_LENGTH_CHECK', 'KANA_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ZIP1'), 'zip01', ZIP01_LEN, 'n', array('EXIST_CHECK', 'SPTAB_CHECK' ,'NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ZIP2'), 'zip02', ZIP02_LEN, 'n', array('EXIST_CHECK', 'SPTAB_CHECK' ,'NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PREF'), 'pref', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ADDR1'), 'addr01', MTEXT_LEN, 'aKV', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ADDR2'), 'addr02', MTEXT_LEN, 'aKV', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TEL1'), 'tel01', TEL_ITEM_LEN, 'n', array('EXIST_CHECK', 'SPTAB_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TEL2'), 'tel02', TEL_ITEM_LEN, 'n', array('EXIST_CHECK', 'SPTAB_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TEL3'), 'tel03', TEL_ITEM_LEN, 'n', array('EXIST_CHECK', 'SPTAB_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_FAX1'), 'fax01', TEL_ITEM_LEN, 'n', array('SPTAB_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_FAX2'), 'fax02', TEL_ITEM_LEN, 'n', array('SPTAB_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_FAX3'), 'fax03', TEL_ITEM_LEN, 'n', array('SPTAB_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * 会員登録共通
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param boolean $isAdmin true:管理者画面 false:会員向け
     * @param boolean $is_mypage マイページの場合 true
     * @return void
     */
    function sfCustomerRegisterParam(&$objFormParam, $isAdmin = false, $is_mypage = false) {
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PASSWORD'), 'password', STEXT_LEN, 'a', array('EXIST_CHECK', 'SPTAB_CHECK', 'ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_REMINDER_ANSWER'), 'reminder_answer', STEXT_LEN, 'aKV', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_REMINDER'), 'reminder', STEXT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_SEX'), 'sex', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_JOB'), 'job', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_YEAR'), 'year', 4, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_MONTH'), 'month', 2, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_DAY'), 'day', 2, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_MAILMAGAZINE'), 'mailmaga_flg', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));

        if (SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE) {
            $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_EMAIL'), 'email', null, 'a', array('NO_SPTAB', 'EXIST_CHECK', 'EMAIL_CHECK', 'SPTAB_CHECK' ,'EMAIL_CHAR_CHECK'));
            $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PASSWORD_CONFIRM'), 'password02', STEXT_LEN, 'a', array('EXIST_CHECK', 'SPTAB_CHECK' ,'ALNUM_CHECK'), '', false);
            if (!$isAdmin) {
                $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_EMAIL_CONFIRM'), 'email02', null, 'a', array('NO_SPTAB', 'EXIST_CHECK', 'EMAIL_CHECK','SPTAB_CHECK' , 'EMAIL_CHAR_CHECK'), '', false);
            }
        } else {
            if (!$is_mypage) {
                $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_EMAIL'), 'email', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'NO_SPTAB' ,'EMAIL_CHAR_CHECK', 'MOBILE_EMAIL_CHECK'));
            }
        }
    }

    /**
     * 会員登録エラーチェック
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return array エラーの配列
     */
    function sfCustomerEntryErrorCheck(&$objFormParam) {
        $objErr = SC_Helper_Customer_Ex::sfCustomerCommonErrorCheck($objFormParam);
        $objErr = SC_Helper_Customer_Ex::sfCustomerRegisterErrorCheck($objErr);

        /*
         * sfCustomerRegisterErrorCheck() では, ログイン中の場合は重複チェック
         * されないので, 再度チェックを行う
         */
        $objCustomer = new SC_Customer_Ex();
        if ($objCustomer->isLoginSuccess(true)
            && SC_Helper_Customer_Ex::sfCustomerEmailDuplicationCheck($objCustomer->getValue('customer_id'), $objFormParam->getValue('email'))) {
            $objErr->arrErr['email'] .= SC_I18n_Ex::t('SC_Helper_Customer_001');
        }
        if ($objCustomer->isLoginSuccess(true)
            && SC_Helper_Customer_Ex::sfCustomerEmailDuplicationCheck($objCustomer->getValue('customer_id'), $objFormParam->getValue('email_mobile'))) {
            $objErr->arrErr['email_mobile'] .= SC_I18n_Ex::t('SC_Helper_Customer_001');
        }

        return $objErr->arrErr;
    }

    /**
     * 会員情報変更エラーチェック
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param boolean $isAdmin 管理画面チェック時:true
     * @access public
     * @return array エラーの配列
     */
    function sfCustomerMypageErrorCheck(&$objFormParam, $isAdmin = false) {

        $objFormParam->toLower('email_mobile');
        $objFormParam->toLower('email_mobile02');

        $objErr = SC_Helper_Customer_Ex::sfCustomerCommonErrorCheck($objFormParam);
        $objErr = SC_Helper_Customer_Ex::sfCustomerRegisterErrorCheck($objErr, $isAdmin);

        if (isset($objErr->arrErr['password'])
            && $objFormParam->getValue('password') == DEFAULT_PASSWORD) {
            unset($objErr->arrErr['password']);
            unset($objErr->arrErr['password02']);
        }
        if (isset($objErr->arrErr['reminder_answer'])
                && $objFormParam->getValue('reminder_answer') == DEFAULT_PASSWORD) {
            unset($objErr->arrErr['reminder_answer']);
        }
        return $objErr->arrErr;
    }

    /**
     * 会員エラーチェック共通
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access private
     * @return array エラー情報の配列
     */
    function sfCustomerCommonErrorCheck(&$objFormParam) {
        $objFormParam->convParam();
        $objFormParam->toLower('email');
        $objFormParam->toLower('email02');
        $arrParams = $objFormParam->getHashArray();

        // 入力データを渡す。
        $objErr = new SC_CheckError_Ex($arrParams);
        $objErr->arrErr = $objFormParam->checkError();

        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_TEL_NUMBER'), 'tel01', 'tel02', 'tel03'),array('TEL_CHECK'));
        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_FAX_NUMBER'), 'fax01', 'fax02', 'fax03') ,array('TEL_CHECK'));
        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_ZIP'), 'zip01', 'zip02'), array('ALL_EXIST_CHECK'));

        return $objErr;
    }

    /**
     * 会員登録編集共通の相関チェック
     *
     * @param SC_CheckError $objErr SC_CheckError インスタンス
     * @param boolean $isAdmin 管理画面チェック時:true
     * @return SC_CheckError $objErr エラー情報
     */
    function sfCustomerRegisterErrorCheck(&$objErr, $isAdmin = false) {
        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_BIRTHDAY'), 'year', 'month', 'day'), array('CHECK_BIRTHDAY'));

        if (SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE) {
            if (!$isAdmin) {
                $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_PASSWORD'), 'password', PASSWORD_MIN_LEN, PASSWORD_MAX_LEN) ,array('SPTAB_CHECK', 'NUM_RANGE_CHECK'));
                $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_EMAIL'), SC_I18n_Ex::t('PARAM_LABEL_EMAIL_CONFIRM'), 'email', 'email02') ,array('EQUAL_CHECK'));
            }
            $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_PASSWORD'), SC_I18n_Ex::t('PARAM_LABEL_PASSWORD_CONFIRM'), 'password', 'password02') ,array('EQUAL_CHECK'));
        }

        if (!$isAdmin) {
            // 現会員の判定 → 現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
            $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_EMAIL'), 'email'), array('CHECK_REGIST_CUSTOMER_EMAIL'));
            $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_EMAIL_MOBILE'), 'email_mobile'), array('CHECK_REGIST_CUSTOMER_EMAIL', 'MOBILE_EMAIL_CHECK'));
        }
        return $objErr;
    }

    /**
     * 会員検索パラメーター（管理画面用）
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return void
     */
    function sfSetSearchParam(&$objFormParam) {
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_ID'), 'search_customer_id', ID_MAX_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_NAME'), 'search_name', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_KANA'), 'search_kana', STEXT_LEN, 'CKV', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'KANABLANK_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PREF'), 'search_pref', INT_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BD_START_YEAR'), 'search_b_start_year', 4, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BD_START_MONTH'), 'search_b_start_month', 2, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BD_START_DAY'), 'search_b_start_day', 2, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BD_END_YEAR'), 'search_b_end_year', 4, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BD_END_MONTH'), 'search_b_end_month', 2, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BD_END_DAY'), 'search_b_end_day', 2, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BIRTH_MONTH'), 'search_birth_month', 2, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_EMAIL'), 'search_email', MTEXT_LEN, 'a', array('SPTAB_CHECK', 'EMAIL_CHAR_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_EMAIL_MOBILE'), 'search_email_mobile', MTEXT_LEN, 'a', array('SPTAB_CHECK', 'EMAIL_CHAR_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TEL_NUMBER'), 'search_tel', TEL_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BUY_TOTAL_FROM'), 'search_buy_total_from', PRICE_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BUY_TOTAL_TO'), 'search_buy_total_to', PRICE_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BUY_TIMES_FROM'), 'search_buy_times_from', INT_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BUY_TIMES_TO'), 'search_buy_times_to', INT_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_UPDATE_START_YEAR'), 'search_start_year', 4, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_UPDATE_START_MONTH'), 'search_start_month', 2, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_UPDATE_START_DAY'), 'search_start_day', 2, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_UPDATE_END_YEAR'), 'search_end_year', 4, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_UPDATE_END_MONTH'), 'search_end_month', 2, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_UPDATE_END_DAY'), 'search_end_day', 2, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PAGE_MAX'), 'search_page_max', INT_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'), SEARCH_PMAX, false);
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PAGE_NO'), 'search_pageno', INT_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'), 1, false);
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BUY_START_YEAR'), 'search_buy_start_year', 4, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BUY_START_MONTH'), 'search_buy_start_month', 2, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BUY_START_DAY'), 'search_buy_start_day', 2, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BUY_END_YEAR'), 'search_buy_end_year', 4, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BUY_END_MONTH'), 'search_buy_end_month', 2, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BUY_END_DAY'), 'search_buy_end_day', 2, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BUY_PRODUCT_CODE'), 'search_buy_product_code', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BUY_PRODUCT_NAME'), 'search_buy_product_name', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CATEGORY'), 'search_category_id', INT_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_SEX'), 'search_sex', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_STATUS'), 'search_status', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_JOB'), 'search_job', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
    }

    /**
     * 会員検索パラメーター　エラーチェック（管理画面用）
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return array エラー配列
     */
    function sfCheckErrorSearchParam(&$objFormParam) {
        // パラメーターの基本チェック
        $arrErr = $objFormParam->checkError();
        // 拡張エラーチェック初期化
        $objErr = new SC_CheckError_Ex($objFormParam->getHashArray());
        // 拡張エラーチェック
        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_BD_START_DAY'), 'search_b_start_year', 'search_b_start_month', 'search_b_start_day'), array('CHECK_DATE'));
        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_BD_END_DAY'), 'search_b_end_year', 'search_b_end_month', 'search_b_end_day'), array('CHECK_DATE'));

        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_BD_START_DAY'),SC_I18n_Ex::t('PARAM_LABEL_BD_END_DAY'), 'search_b_start_year', 'search_b_start_month', 'search_b_start_day', 'search_b_end_year', 'search_b_end_month', 'search_b_end_day'), array('CHECK_SET_TERM'));
        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_UPDATE_START_DAY'), 'search_start_year', 'search_start_month', 'search_start_day',), array('CHECK_DATE'));
        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_UPDATE_END_DAY'), 'search_end_year', 'search_end_month', 'search_end_day'), array('CHECK_DATE'));
        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_UPDATE_START_DAY'),SC_I18n_Ex::t('PARAM_LABEL_UPDATE_END_DAY'), 'search_start_year', 'search_start_month', 'search_start_day', 'search_end_year', 'search_end_month', 'search_end_day'), array('CHECK_SET_TERM'));
        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_BUY_START_DAY'), 'search_buy_start_year', 'search_buy_start_month', 'search_buy_start_day',), array('CHECK_DATE'));
        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_BUY_END_DAY'), 'search_buy_end_year', 'search_buy_end_month', 'search_buy_end_day'), array('CHECK_DATE'));
        // 開始 > 終了 の場合はエラーとする
        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_BUY_START_DAY'),SC_I18n_Ex::t('PARAM_LABEL_BUY_END_DAY'), 'search_buy_start_year', 'search_buy_start_month', 'search_buy_start_day', 'search_buy_end_year', 'search_buy_end_month', 'search_buy_end_day'), array('CHECK_SET_TERM'));

        if (SC_Utils_Ex::sfIsInt($array['search_buy_total_from'])
            && SC_Utils_Ex::sfIsInt($array['search_buy_total_to'])
            && $array['search_buy_total_from'] > $array['buy_total_to']
        ) {
            $objErr->arrErr['search_buy_total_from'] .= SC_I18n_Ex::t('SC_Helper_Customer_002');
        }

        if (SC_Utils_Ex::sfIsInt($array['search_buy_times_from'])
            && SC_Utils_Ex::sfIsInt($array['search_buy_times_to'])
            && $array['search_buy_times_from'] > $array['search_buy_times_to']
        ) {
            $objErr->arrErr['search_buy_times_from'] .= SC_I18n_Ex::t('SC_Helper_Customer_003');
        }
        if (!SC_Utils_Ex::isBlank($objErr->arrErr)) {
            $arrErr = array_merge($arrErr, $objErr->arrErr);
        }
        return $arrErr;
    }

    /**
     * 会員一覧検索をする処理（ページング処理付き、管理画面用共通処理）
     *
     * @param array $arrParam 検索パラメーター連想配列
     * @param string $limitMode ページングを利用するか判定用フラグ
     * @return array( integer 全体件数, mixed 会員データ一覧配列, mixed SC_PageNaviオブジェクト)
     */
    function sfGetSearchData($arrParam, $limitMode = '') {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objSelect = new SC_CustomerList_Ex($arrParam, 'customer');
        $page_max = SC_Utils_Ex::sfGetSearchPageMax($arrParam['search_page_max']);
        $disp_pageno = $arrParam['search_pageno'];
        if ($disp_pageno == 0) {
            $disp_pageno = 1;
        }
        $offset = intval($page_max) * (intval($disp_pageno) - 1);
        if ($limitMode == '') {
            $objQuery->setLimitOffset($page_max, $offset);
        }
        $arrData = $objQuery->getAll($objSelect->getList(), $objSelect->arrVal);

        // 該当全体件数の取得
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $linemax = $objQuery->getOne($objSelect->getListCount(), $objSelect->arrVal);

        // ページ送りの取得
        $objNavi = new SC_PageNavi_Ex($arrParam['search_pageno'],
                                    $linemax,
                                    $page_max,
                                    'fnNaviSearchOnlyPage',
                                    NAVI_PMAX);
        return array($linemax, $arrData, $objNavi);
    }

    /**
     * 仮会員かどうかを判定する.
     *
     * @param string $login_email メールアドレス
     * @return boolean 仮会員の場合 true
     */
    public function checkTempCustomer($login_email) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $where = 'email = ? AND status = 1 AND del_flg = 0';
        $exists = $objQuery->exists('dtb_customer', $where, array($login_email));
        return $exists;
    }
}
