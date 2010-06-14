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
 * キャンペーンタグ生成 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_CampaignCreateTag extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contents/campaign_create_tag.tpl';
        $this->tpl_mainno = 'create';
        $this->tpl_subtitle = '商品設定';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        switch($_POST['mode']) {

            // 商品検索
        case  'search':
            // POST値の引き継ぎ
            $this->arrForm = $_POST;
            // 入力文字の強制変換
            $this->lfConvertParam();

            $where = "del_flg = 0";

            // where句生成
            foreach ($this->arrForm as $key => $val) {
                if($val == "") {
                    continue;
                }

                switch ($key) {
                case 'search_name':
                    $where .= " AND name ILIKE ?";
                    $arrval[] = "%$val%";
                    break;
                case 'search_category_id':
                        list($tmp_where, $tmp_arrval) = $objDb->sfGetCatWhere($val); 
                        if($tmp_where != "") { 
                            $where.= " AND product_id IN (SELECT product_id FROM dtb_product_categories WHERE " . $tmp_where . ")"; 
                            $arrval = array_merge((array)$arrval, (array)$tmp_arrval);
                    }
                    break;
                case 'search_product_id':
                    $where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code LIKE ? GROUP BY product_id)";
                    $arrval[] = "$val%";
                    break;
                default:
                    break;
                }
            }

            $order = "update_date DESC, product_id DESC ";

            // 読み込む列とテーブルの指定
            $col = "product_id, name, category_id, main_list_image, status, product_code, price01, stock, stock_unlimited";
            $from = "vw_products_nonclass AS noncls ";

            // 行数の取得
            $linemax = $objQuery->count("dtb_products", $where, $arrval);
            $this->tpl_linemax = $linemax;				// 何件が該当しました。表示用

            // ページ送りの処理
            if(is_numeric($_POST['search_page_max'])) {
                $page_max = $_POST['search_page_max'];
            } else {
                $page_max = SEARCH_PMAX;
            }

            // ページ送りの取得
            $objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchOnlyPage", NAVI_PMAX);
            $this->tpl_strnavi = $objNavi->strnavi;		// 表示文字列
            $startno = $objNavi->start_row;

            // 取得範囲の指定(開始行番号、行数のセット)
            if(DB_TYPE != "mysql") $objQuery->setLimitOffset($page_max, $startno);
            // 表示順序
            $objQuery->setOrder($order);

            // 検索結果の取得
            $this->arrProducts = $objQuery->select($col, $from, $where, $arrval);
            break;

            // タグ表示
        case 'view':

            // タグを生成
            $create_tag = $this->lfGetCreateTag($_POST['product_id']);
            $this->tpl_create_tag = $create_tag;
            break;
        }

        // カテゴリ取得
        $this->arrCatList = $objDb->sfGetCategoryList();

        //----　ページ表示
        $objView->assignobj($this);
        $objView->display($this->tpl_mainpage);
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
     * 取得する文字数の変換を行うメソッド
     *
     * @return void
     */
    function lfConvertParam() {
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         */
        $arrConvList['search_name'] = "KVa";
        $arrConvList['search_product_code'] = "KVa";

        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(isset($this->arrForm[$key])) {
                $this->arrForm[$key] = mb_convert_kana($this->arrForm[$key] ,$val);
            }
        }
    }

    /**
     * キャンペーンページのための商品用タグを生成するメソッド
     *
     * @param string $product_id 商品ID
     * @return void
     */
    
    function lfGetCreateTag($product_id) {
        // 書き込みタグ
        $read_file = file_get_contents(CAMPAIGN_BLOC_PATH . "cart_tag.tpl");
        $read_file = ereg_replace("<{assign_product_id}>", $product_id, $read_file);
        // カートタグ保存用ファイル作成
        $create_tag .= "<!--{* ▼商品ID$product_id *}-->\n";
        $create_tag .= $read_file;

        return $create_tag;
    }

}
?>
