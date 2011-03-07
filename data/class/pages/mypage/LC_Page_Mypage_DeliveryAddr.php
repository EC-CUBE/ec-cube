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
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * お届け先追加 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_DeliveryAddr extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title    = "お届け先の追加･変更";
        $masterData         = new SC_DB_MasterData_Ex();
        $this->arrPref      = $masterData->getMasterData('mtb_pref');
        $this->httpCacheControl('nocache');
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
        $objCustomer = new SC_Customer_Ex();
        $ParentPage  = MYPAGE_DELIVADDR_URLPATH;

        // GETでページを指定されている場合には指定ページに戻す
        if (isset($_GET['page'])) {
            $ParentPage = htmlspecialchars($_GET['page'], ENT_QUOTES);
        } else if (isset($_POST['ParentPage'])) {
            $ParentPage = htmlspecialchars($_POST['ParentPage'], ENT_QUOTES);
        }
        $this->ParentPage = $ParentPage;

        /*
         * ログイン判定 及び 退会判定
         * 未ログインでも, 複数配送設定ページからのアクセスの場合は表示する
         *
         * TODO 購入遷移とMyPageで別クラスにすべき
         */
        if (!$objCustomer->isLoginSuccess(true) && $ParentPage != MULTIPLE_URLPATH){
            $this->tpl_onload = "fnUpdateParent('". $this->getLocation($_POST['ParentPage']) ."'); window.close();";
        }

        // $_GET['other_deliv_id'] のあるなしで追加か編集か判定しているらしい
        if (!isset($_GET['other_deliv_id'])) $_GET['other_deliv_id'] = "";
        $_SESSION['other_deliv_id'] = $_GET['other_deliv_id'];

        // パラメータ管理クラス,パラメータ情報の初期化
        $objFormParam   = new SC_FormParam_Ex();
        SC_Helper_Customer_Ex::sfCustomerOtherDelivParam($objFormParam);
        $objFormParam->setParam($_POST);
        $this->arrForm  = $objFormParam->getHashArray();

        switch ($this->getMode()) {
            // 入力は必ずedit
            case 'edit':
                $this->arrErr = SC_Helper_Customer_Ex::sfCustomerOtherDelivErrorCheck($objFormParam);
                // 入力エラーなし
                if(empty($this->arrErr)) {

                    // TODO ここでやるべきではない
                    $validUrl = array(MYPAGE_DELIVADDR_URLPATH,
                                      DELIV_URLPATH,
                                      MULTIPLE_URLPATH);
                    if (in_array($_POST['ParentPage'], $validUrl)) {
                        $this->tpl_onload = "fnUpdateParent('". $this->getLocation($_POST['ParentPage']) ."'); window.close();";
                    } else {
                        SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                    }

                    if ($objCustomer->isLoginSuccess(true)) {
                        $this->lfRegistData($objFormParam, $objCustomer->getValue("customer_id"));
                    } else {
                        $this->lfRegistDataNonMember($objFormParam);
                    }

                    if(SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
                        // モバイルの場合、元のページに遷移
                        SC_Response_Ex::sendRedirect($this->getLocation($_POST['ParentPage']));
                        exit;
                    }
                }
                break;
            case 'multiple':
                // 複数配送先用？
                break;
            default :

                if ($_GET['other_deliv_id'] != ""){
                    $arrOtherDeliv = $this->lfGetOtherDeliv($objCustomer->getValue("customer_id"), $_SESSION['other_deliv_id']);

                    //不正アクセス判定
                    if (!$objCustomer->isLoginSuccess(true)
                        || count($arrOtherDeliv) == 0){
                        SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                    }

                    //別のお届け先情報取得
                    $this->arrForm = $arrOtherDeliv[0];
                }
                break;
        }

        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
            $this->tpl_mainpage = 'mypage/delivery_addr.tpl';
        } else {
            $this->setTemplate('mypage/delivery_addr.tpl');
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
     * ほかのお届け先を取得する
     *
     * @param mixed $customer_id
     * @param mixed $other_deliv_id
     * @access private
     * @return array()
     */
    function lfGetOtherDeliv($customer_id, $other_deliv_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->select("*", "dtb_other_deliv", "customer_id = ? AND other_deliv_id = ?", array($customer_id, $other_deliv_id));

         $arrOtherDeliv[0];
    }

    /* 登録実行 */
    function lfRegistData($objFormParam, $customer_id) {
        $objQuery   =& SC_Query_Ex::getSingletonInstance();

        $arrRet     = $objFormParam->getHashArray();
        $sqlval     = $objFormParam->getDbArray();

        $sqlval['customer_id'] = $customer_id;

        // 追加
        if (strlen($arrRet['other_deliv_id'] == 0)) {
            // 別のお届け先登録数の取得
            $deliv_count = $objQuery->count("dtb_other_deliv", "customer_id = ?", array($customer_id));
            // 別のお届け先最大登録数に達している場合、エラー
            if ($deliv_count >= DELIV_ADDR_MAX) {
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, "", false, '別のお届け先最大登録数に達しています。');
            }

            // 実行
            $sqlval['other_deliv_id'] = $objQuery->nextVal('dtb_other_deliv_other_deliv_id');
            $objQuery->insert("dtb_other_deliv", $sqlval);

        // 変更
        } else {
            $deliv_count = $objQuery->count("dtb_other_deliv","customer_id = ? AND other_deliv_id = ?" ,array($customer_id, $arrRet['other_deliv_id']));
            if ($deliv_count != 1) {
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, "", false, '一致する別のお届け先がありません。');
            }

            // 実行
            $objQuery->update("dtb_other_deliv", $sqlval, "other_deliv_id = ?", array($arrRet['other_deliv_id']));
        }
    }

    function lfRegistDataNonMember($objFormParam) {
        $arrRegistColumn = $objFormParam->getDbArray();

        foreach ($arrRegistColumn as $data) {
            $arrRegist['shipping_' . $data['column'] ] = $array[ $data['column'] ];
        }
        if (count($shipping) >= DELIV_ADDR_MAX) {
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, "", false, '別のお届け先最大登録数に達しています。');
        } else {
            $_SESSION['shipping'][] = $arrRegist;
        }
    }
}
?>
