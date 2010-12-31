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
require_once(CLASS_FILE_PATH . "pages/LC_Page.php");

/**
 * お届け先編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_Delivery extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = 'MYページ';
        $this->tpl_subtitle = 'お届け先追加･変更';
        $this->tpl_navi = TEMPLATE_DIR . 'mypage/navi.tpl';
        $this->tpl_mainno = 'mypage';
        $this->tpl_mypageno = 'delivery';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref= $masterData->getMasterData('mtb_pref');
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
        //$objView = new SC_SiteView();
        $objCustomer = new SC_Customer();
        
        // 退会判定用情報の取得
        $this->tpl_login = $objCustomer->isLoginSuccess();

        // ポップアップを開けたまま退会された状態でポップアップが閉じた場合のエラー画面の抑止。
        // コメントアウトした「ログイン判定」は他の「Mypage」内に施した退会時処理で補間。
        
        // XXX コメントアウトによる問題が確認された場合はコメントアウトを外し、エラー画面が出る様に戻す。
        ////ログイン判定
        // if(!$objCustomer->isLoginSuccess()) {
        //     SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        // }else {
            //マイページトップ顧客情報表示用
            $this->CustomerName1 = $objCustomer->getvalue('name01');
            $this->CustomerName2 = $objCustomer->getvalue('name02');
            $this->CustomerPoint = $objCustomer->getvalue('point');
        //}

        $mode = isset($_POST['mode']) ? $_POST['mode'] : '';
        $customerId = $objCustomer->getValue('customer_id');

        switch($mode) {

        // お届け先の削除
        case 'delete':
            $objForm = $this->initParam();
            if ($objForm->checkError()) {
                SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                exit;
            }

            $this->deleteOtherDeliv($customerId, $objForm->getValue('other_deliv_id'));
            break;

        // お届け先の表示
        default:
            break;
        }

        //別のお届け先情報
        $this->arrOtherDeliv = $this->getOtherDeliv($customerId);

        //お届け先登録数
        $this->tpl_linemax = count($this->arrOtherDeliv);;

        //$objView->assignobj($this);
        //$objView->display(SITE_FRAME);
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
    function initParam() {
        $objForm = new SC_FormParam();
        $objForm->addParam('お届け先ID', 'other_deliv_id', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objForm->setParam($_POST);
        $objForm->convParam();
        return $objForm;
    }

    /**
     * お届け先の取得
     *
     * @param integer $customerId
     * @return array
     */
    function getOtherDeliv($customerId) {
        $objQuery = new SC_Query;
        $objQuery->setOrder('other_deliv_id DESC');
        $arrRet = $objQuery->select('*', 'dtb_other_deliv', 'customer_id = ?', array($customerId));
        return empty($arrRet) ? array() : $arrRet;
    }

    /**
     * お届け先の削除
     *
     * @param integer $customerId
     * @param integer $delivId
     */
    function deleteOtherDeliv($customerId, $delivId) {
        $where = 'customer_id = ? AND other_deliv_id = ?';
        $objQuery = new SC_Query;
        $objQuery->delete("dtb_other_deliv", $where, array($customerId, $delivId));
    }
}
?>
