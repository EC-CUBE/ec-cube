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
require_once CLASS_REALDIR . 'pages/admin/products/LC_Page_Admin_Products_Review.php';

/**
 * レビュー編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_ReviewEdit extends LC_Page_Admin_Products_Review {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/review_edit.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'review';
        // 両方選択可能
        $this->tpl_status_change = true;

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrRECOMMEND = $masterData->getMasterData("mtb_recommend");
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = 'レビュー管理';
        $this->arrSex = $masterData->getMasterData("mtb_sex");
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        // パラメーター情報の初期化
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        // 検索ワードの引き継ぎ
        $this->arrSearchHidden = $objFormParam->getSearchArray();
        $this->arrForm = $objFormParam->getHashArray();

        switch ($this->getMode()) {
            // 登録
            case 'complete':
                $this->arrErr = $this->lfCheckError($objFormParam);
                // エラー無し
                if (!$this->arrErr) {
                    // レビュー情報の更新
                    $this->lfRegistReviewData($this->arrForm['review_id'], $objFormParam);
                    // レビュー情報のDB取得
                    $this->arrForm = $this->lfGetReviewData($this->arrForm['review_id']);
                    $this->tpl_onload = "alert('登録が完了しました。');";
                }
                break;
            default:
                // レビュー情報のDB取得
                $this->arrForm = $this->lfGetReviewData($this->arrForm['review_id']);
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
     * パラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        // 検索条件のパラメーターを初期化
        parent::lfInitParam($objFormParam);
        $objFormParam->addParam("レビューID", "review_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("商品名", 'name', "", "", array(), "", false);
        $objFormParam->addParam("投稿日", "create_date", "", "", array(), "", false);

        // 登録情報
        $objFormParam->addParam("レビュー表示", 'status', INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("投稿者名", "reviewer_name", STEXT_LEN, 'KVa', array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("投稿者URL", "reviewer_url", URL_LEN, 'KVCa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("性別", 'sex', INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("おすすめレベル", "recommend_level", INT_LEN, 'n', array("SELECT_CHECK"));
        $objFormParam->addParam("タイトル", 'title', STEXT_LEN, 'KVa', array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("コメント", 'comment', LTEXT_LEN, 'KVa', array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     * フォーム入力パラメーターエラーチェック
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    function lfCheckError(&$objFormParam) {
        $arrErr = $objFormParam->checkError();
        if (!SC_Utils_Ex::isBlank($arrErr)) {
            return $arrErr;
        }
    }

    /**
     * レビュー情報のDB取得
     *
     * @param integer $review_id レビューID
     * @return array レビュー情報
     */
    function lfGetReviewData($review_id){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $select="review_id, A.product_id, reviewer_name, sex, recommend_level, ";
        $select.="reviewer_url, title, comment, A.status, A.create_date, A.update_date, name";
        $from = "dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id ";
        $where = "A.del_flg = 0 AND B.del_flg = 0 AND review_id = ? ";
        $arrReview = $objQuery->select($select, $from, $where, array($review_id));
        if (empty($arrReview)) {
            SC_Utils_Ex::sfDispError("");
        }
        return $arrReview[0];
    }

    /**
     * レビュー情報の更新
     *
     * @param integer $review_id レビューID
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfRegistReviewData($review_id, &$objFormParam){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrValues = $objFormParam->getDbArray();
        $arrValues['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery->update("dtb_review", $arrValues, "review_id = ?", array($review_id));
    }
}
