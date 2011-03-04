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
 * お届け先編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_Delivery extends LC_Page_AbstractMypage_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_subtitle = 'お届け先追加･変更';
        $this->tpl_mypageno = 'delivery';
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
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
        $objCustomer    = new SC_Customer_Ex();
        $customer_id    = $objCustomer->getValue('customer_id');
        $objFormParam   = new SC_FormParam_Ex();

        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        switch($this->getMode()) {

        // お届け先の削除
        case 'delete':
            if ($objFormParam->checkError()) {
                SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                exit;
            }

            $this->deleteOtherDeliv($customer_id, $objFormParam->getValue('other_deliv_id'));
            break;

        // お届け先の表示
        default:
            break;
        }

        //別のお届け先情報
        $this->arrOtherDeliv = $this->getOtherDeliv($customer_id);

        //お届け先登録数
        $this->tpl_linemax = count($this->arrOtherDeliv);
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
     * フォームパラメータの初期化
     *
     * @return SC_FormParam
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('お届け先ID', 'other_deliv_id', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * お届け先の取得
     *
     * @param integer $customerId
     * @return array
     */
    function getOtherDeliv($customer_id) {
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('other_deliv_id DESC');
        return $objQuery->select('*', 'dtb_other_deliv', 'customer_id = ?', array($customer_id));
    }

    /**
     * お届け先の削除
     *
     * @param integer $customerId
     * @param integer $delivId
     */
    function deleteOtherDeliv($customer_id, $deliv_id) {
        $where      = 'customer_id = ? AND other_deliv_id = ?';
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $objQuery->delete("dtb_other_deliv", $where, array($customer_id, $deliv_id));
    }
}
