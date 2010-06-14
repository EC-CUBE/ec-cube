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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * レビュー編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_ReviewEdit extends LC_Page {

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
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'review';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrRECOMMEND = $masterData->getMasterData("mtb_recommend");
        $this->tpl_subtitle = 'レビュー管理';
        $this->arrSex = $masterData->getMasterData("mtb_sex");
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $this->objQuery = new SC_Query();
        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // 検索ワードの引継ぎ
        foreach ($_POST as $key => $val){
            if (ereg("^search_", $key)){
                $this->arrSearchHidden[$key] = $val;
            }
        }

        // 両方選択可能
        $this->tpl_status_change = true;

        if (!isset($_POST['mode'])) $_POST['mode'] = "";
        switch ($_POST['mode']) {
            // 登録
            case 'complete':
                // 取得文字列の変換用カラム
                $arrRegistColumn = array (
                    array("column" => "status"),
                    array("column" => "recommend_level"),
                    array("column" => "title", "convert" => "KVa"),
                    array("column" => "comment", "convert" => "KVa"),
                    array("column" => "reviewer_name", "convert" => "KVa"),
                    array("column" => "reviewer_url", "convert" => "KVa"),
                    array("column" => "sex", "convert" => "n")
                );

                // フォーム値の変換
                $arrReview = $this->lfConvertParam($_POST, $arrRegistColumn);
                $this->arrErr = $this->lfCheckError($arrReview);

                // エラー有り
                if ($this->arrErr) {
                    // 入力内容を引き継ぐ
                    $this->arrReview = $arrReview;
                }
                // エラー無し
                else {
                    // レビュー情報の更新
                    $this->lfRegistReviewData($arrReview, $arrRegistColumn);

                    // レビュー情報のDB取得
                    $this->arrReview = $this->lfGetReviewData($arrReview['review_id']);

                    $this->tpl_onload = "alert('登録が完了しました。');";
                }
                break;

            default:
                // レビュー情報のDB取得
                $this->arrReview = $this->lfGetReviewData($_POST['review_id']);
                break;
        }

        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
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
     * 入力エラーチェック
     *
     */
    function lfCheckError($arrReview) {
        $objErr = new SC_CheckError($arrReview);
        $objErr->doFunc(array("おすすめレベル", "recommend_level"), array("SELECT_CHECK"));
        $objErr->doFunc(array("タイトル", "title", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("コメント", "comment", LTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("投稿者名", "reviewer_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("投稿者URL", "reviewer_url", URL_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("性別", "sex", STEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        return $objErr->arrErr;
    }

    /**
     * 取得文字列の変換
     *
     */
    function lfConvertParam($array, $arrRegistColumn) {
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         *  a :  全角英数字を半角英数字に変換する
         */
        // カラム名とコンバート情報
        foreach ($arrRegistColumn as $data) {
            $arrConvList[ $data["column"] ] = isset($data["convert"])
                ? $data["convert"] : "";
        }

        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(strlen(($array[$key])) > 0) {
                $array[$key] = mb_convert_kana($array[$key] ,$val);
            }
        }
        return $array;
    }

    /**
     * レビュー情報のDB取得
     *
     */
    function lfGetReviewData($review_id){
        $select="review_id, A.product_id, reviewer_name, sex, recommend_level, ";
        $select.="reviewer_url, title, comment, A.status, A.create_date, A.update_date, name";
        $from = "dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id ";
        $where = "A.del_flg = 0 AND B.del_flg = 0 AND review_id = ? ";
        $arrReview = $this->objQuery->select($select, $from, $where, array($review_id));
        if (empty($arrReview)) {
            SC_Utils_Ex::sfDispError("");
        }

        return $arrReview[0];
    }

    /**
     * レビュー情報の更新
     *
     */
    function lfRegistReviewData($arrReview, $arrRegistColumn){
        foreach ($arrRegistColumn as $data) {
            $arrRegist[ $data["column"] ] = $arrReview[ $data["column"] ];
        }
        $arrRegist['update_date'] = 'now()';

        // 更新実行
        $this->objQuery->update("dtb_review", $arrRegist, "review_id = ?", array($arrReview['review_id']));
    }
}
?>
