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

// {{{ requires
require_once(CLASS_EX_REALDIR . "page_extends/mypage/LC_Page_AbstractMypage_Ex.php");

/**
 * 登録内容変更 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_Change extends LC_Page_AbstractMypage_Ex {


    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_subtitle = '会員登録内容変更(入力ページ)';
        $this->tpl_mypageno = 'change';

        $masterData         = new SC_DB_MasterData_Ex();
        $this->arrReminder  = $masterData->getMasterData("mtb_reminder");
        $this->arrPref      = $masterData->getMasterData('mtb_pref');
        $this->arrJob       = $masterData->getMasterData("mtb_job");
        $this->arrMAILMAGATYPE = $masterData->getMasterData("mtb_mail_magazine_type");
        $this->arrSex       = $masterData->getMasterData("mtb_sex");
        $this->httpCacheControl('nocache');

        // 生年月日選択肢の取得
        $objDate            = new SC_Date_Ex(BIRTH_YEAR, date("Y",strtotime("now")));
        $this->arrYear      = $objDate->getYear('', START_BIRTH_YEAR, '');
        $this->arrMonth     = $objDate->getMonth(true);
        $this->arrDay       = $objDate->getDay(true);
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
    }

    /**
     * Page のプロセス
     * @return void
     */
    function action() {
        $objCustomer = new SC_Customer_Ex();
        $customer_id = $objCustomer->getValue('customer_id');

        // mobile用（戻るボタンでの遷移かどうかを判定）
        if (!empty($_POST["return"])) {
            $_POST["mode"] = "return";
        }

        // パラメータ管理クラス,パラメータ情報の初期化
        $objFormParam = new SC_FormParam();
        SC_Helper_Customer_Ex::sfCustomerMypageParam($objFormParam);
        $objFormParam->setParam($_POST);    // POST値の取得

        $this->arrForm = $objFormParam->getHashArray();

        switch ($this->getMode()) {
        case 'confirm':
        //-- 確認
            $this->arrErr = SC_Helper_Customer_Ex::sfCustomerMypageErrorCheck($objFormParam);
            // 入力エラーなし
            if(empty($this->arrErr)) {
                //パスワード表示
                $this->passlen      = SC_Utils_Ex::sfPassLen(strlen($this->arrForm['password']));

                $this->tpl_mainpage = 'mypage/change_confirm.tpl';
                $this->tpl_title    = '会員登録(確認ページ)';
            }
            break;
        case 'complete':
            //-- 会員登録と完了画面

            // 会員情報の登録
            $this->lfRegistCustomerData($objFormParam, $customer_id);

            // 完了ページに移動させる。
            SC_Response_Ex::sendRedirect('change_complete.php');
            break;
        case 'return':
            break;
        default:
            $this->arrForm = SC_Helper_Customer_Ex::sfGetCustomerData($customer_id);
            break;
        }
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
     *  会員情報を登録する
     *
     * @param mixed $objFormParam
     * @param mixed $customer_id
     * @access private
     * @return void
     */
    function lfRegistCustomerData(&$objFormParam, $customer_id) {
        $arrRet             = $objFormParam->getHashArray();
        $sqlval             = $objFormParam->getDbArray();
        $sqlval['birth']    = SC_Utils_Ex::sfGetTimestamp($arrRet['year'], $arrRet['month'], $arrRet['day']);

        SC_Helper_Customer_Ex::sfEditCustomerData($sqlval, $customer_id);
    }
}
