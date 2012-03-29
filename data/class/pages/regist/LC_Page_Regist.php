<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 会員登録のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Regist extends LC_Page_Ex {

    // {{{ properties

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
        // フックポイント.
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
        $objPlugin->doAction('LC_Page_Regist_action_before', array($this));

        switch ($this->getMode()) {
            case 'regist':
            //--　本登録完了のためにメールから接続した場合
                //-- 入力チェック
                $this->arrErr       = $this->lfErrorCheck($_GET);
                if ($this->arrErr) SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', true, $this->arrErr['id']);

                $registSecretKey    = $this->lfRegistData($_GET);   //本会員登録（フラグ変更）
                $this->lfSendRegistMail($registSecretKey);          //本会員登録完了メール送信

                SC_Response_Ex::sendRedirect('complete.php', array('ci' => SC_Helper_Customer_Ex::sfGetCustomerId($registSecretKey)));
                break;
            //--　それ以外のアクセスは無効とする
            default:
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', true, '無効なアクセスです。');
                break;
        }

        // フックポイント.
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
        $objPlugin->doAction('LC_Page_Regist_action_regist', array($this));
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * 仮会員を本会員にUpdateする
     *
     * @param mixed $array
     * @access private
     * @return string $arrRegist['secret_key'] 本登録ID
     */
    function lfRegistData($array) {
        $objQuery                   = SC_Query_Ex::getSingletonInstance();
        $arrRegist['secret_key']    = SC_Helper_Customer_Ex::sfGetUniqSecretKey(); //本登録ID発行
        $arrRegist['status']        = 2;
        $arrRegist['update_date']   = 'CURRENT_TIMESTAMP';

        $objQuery->begin();
        $objQuery->update('dtb_customer', $arrRegist, 'secret_key = ? AND status = 1', array($array['id']));
        $objQuery->commit();

        return $arrRegist['secret_key'];
    }

    /**
     * 入力エラーチェック
     *
     * @param mixed $array
     * @access private
     * @return array エラーの配列
     */
    function lfErrorCheck($array) {
        $objErr     = new SC_CheckError_Ex($array);

        if (preg_match("/^[[:alnum:]]+$/", $array['id'])) {

            if (!is_numeric(SC_Helper_Customer_Ex::sfGetCustomerId($array['id'], true))) {
                $objErr->arrErr['id'] = '※ 既に会員登録が完了しているか、無効なURLです。<br>';
            }

        } else {
            $objErr->arrErr['id'] = '無効なURLです。メールに記載されている本会員登録用URLを再度ご確認ください。';
        }
        return $objErr->arrErr;
    }

    /**
     * 正会員登録完了メール送信
     *
     * @param mixed $registSecretKey
     * @access private
     * @return void
     */
    function lfSendRegistMail($registSecretKey) {
        $objQuery       = SC_Query_Ex::getSingletonInstance();
        $objCustomer    = new SC_Customer_Ex();
        $objHelperMail  = new SC_Helper_Mail_Ex();
        $CONF           = SC_Helper_DB_Ex::sfGetBasisData();

        //-- 会員データを取得
        $arrCustomer    = $objQuery->select('*', 'dtb_customer', 'secret_key = ?', array($registSecretKey));
        $data           = $arrCustomer[0];
        $objCustomer->setLogin($data['email']);

        //--　メール送信
        $objMailText    = new SC_SiteView_Ex();
        $objMailText->assign('CONF', $CONF);
        $objMailText->assign('name01', $data['name01']);
        $objMailText->assign('name02', $data['name02']);
        $toCustomerMail = $objMailText->fetch('mail_templates/customer_regist_mail.tpl');
        $subject = $objHelperMail->sfMakesubject('会員登録が完了しました。');
        $objMail = new SC_SendMail();

        $objMail->setItem(
                              ''                                // 宛先
                            , $subject                  // サブジェクト
                            , $toCustomerMail           // 本文
                            , $CONF['email03']          // 配送元アドレス
                            , $CONF['shop_name']        // 配送元 名前
                            , $CONF['email03']          // reply_to
                            , $CONF['email04']          // return_path
                            , $CONF['email04']          // Errors_to
        );
        // 宛先の設定
        $name = $data['name01'] . $data['name02'] .' 様';
        $objMail->setTo($data['email'], $name);
        $objMail->sendMail();
    }
}
