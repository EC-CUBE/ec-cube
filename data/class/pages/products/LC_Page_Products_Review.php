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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * お客様の声投稿のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Products_Review.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Products_Review extends LC_Page_Ex {

    // {{{ properties

    /** おすすめレベル */
    var $arrRECOMMEND;

    /** 性別 */
    var $arrSex;

    /** 入力禁止URL */
    var $arrReviewDenyURL;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrRECOMMEND = $masterData->getMasterData('mtb_recommend');
        $this->arrSex = $masterData->getMasterData('mtb_sex');
        $this->arrReviewDenyURL = $masterData->getMasterData('mtb_review_deny_url');
        $this->tpl_mainpage = 'products/review.tpl';
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
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

        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        switch ($this->getMode()) {
            case 'confirm':
                $this->arrErr = $this->lfCheckError($objFormParam);

                //エラーチェック
                if (empty($this->arrErr)) {
                    //重複タイトルでない
                    $this->tpl_mainpage = 'products/review_confirm.tpl';
                }
                break;

            case 'return':
                break;

            case 'complete':
                $this->arrErr = $this->lfCheckError($objFormParam);
                //エラーチェック
                if (empty($this->arrErr)) {
                    //登録実行
                    $this->lfRegistRecommendData($objFormParam);


                    //レビュー書き込み完了ページへ
                    SC_Response_Ex::sendRedirect('review_complete.php');
                    SC_Response_Ex::actionExit();
                }
                break;

            default:
                // 最初のproduct_idは、$_GETで渡ってくる。
                $objFormParam->setParam($_GET);
                break;
        }

        $this->arrForm = $objFormParam->getHashArray();

        //商品名の取得
        $this->arrForm['name'] = $this->lfGetProductName($this->arrForm['product_id']);
        if (empty($this->arrForm['name'])) {
            SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
        }

        $this->setTemplate($this->tpl_mainpage);


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
     * パラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam(t('c_Review ID_01'), 'review_id', INT_LEN, 'aKV');
        $objFormParam->addParam(t('c_Product ID_01'), 'product_id', INT_LEN, 'n', array('NUM_CHECK','EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Poster name_01'), 'reviewer_name', STEXT_LEN, 'aKV', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Poster URL_01'), 'reviewer_url', MTEXT_LEN, 'a', array('NO_SPTAB', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'URL_CHECK'));
        $objFormParam->addParam(t('c_Gender_01'), 'sex', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Recommendation level_01'), 'recommend_level', INT_LEN, 'n', array('EXIST_CHECK', 'SELECT_CHECK'));
        $objFormParam->addParam(t('c_Title_01'), 'title', STEXT_LEN, 'aKV', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Comment_01'), 'comment', LTEXT_LEN, 'aKV', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * 入力内容のチェックを行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラーメッセージの配列
     */
    function lfCheckError(&$objFormParam) {
        $arrErr = $objFormParam->checkError();

        $arrForm = $objFormParam->getHashArray();

        // 重複メッセージの判定
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $exists = $objQuery->exists('dtb_review','product_id = ? AND title = ? ', array($arrForm['product_id'], $arrForm['title']));
        if ($exists) {
            $arrErr['title'] .= t('c_It is not possible to register a duplicate title._01');
        }

        if (REVIEW_ALLOW_URL == false) {
            $objErr = new SC_CheckError_Ex($objFormParam->getHashArray());
            // コメント欄へのURLの入力を禁止
            $objErr->doFunc(array(t('c_URL_01'), 'comment', $this->arrReviewDenyURL), array('PROHIBITED_STR_CHECK'));
            $arrErr += $objErr->arrErr;
        }

        return $arrErr;
    }

    /**
     * 商品名を取得
     *
     * @param integer $product_id 商品ID
     * @return string $product_name 商品名
     */
    function lfGetProductName($product_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        return $objQuery->get('name', 'dtb_products', 'product_id = ? AND del_flg = 0 AND status = 1', array($product_id));
    }

    //登録実行
    function lfRegistRecommendData(&$objFormParam) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrRegist = $objFormParam->getDbArray();

        $arrRegist['create_date'] = 'CURRENT_TIMESTAMP';
        $arrRegist['update_date'] = 'CURRENT_TIMESTAMP';
        $arrRegist['creator_id'] = '0';

        //-- 登録実行
        $objQuery->begin();
        $arrRegist['review_id'] = $objQuery->nextVal('dtb_review_review_id');
        $objQuery->insert('dtb_review', $arrRegist);
        $objQuery->commit();
    }
}
