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
require_once(CLASS_PATH . "pages/admin/LC_Page_Admin.php");

/**
 * 商品登録(商品規格)のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_ProductClass extends LC_Page_Admin {

    // }}}
    // {{{ functions
    /** ダウンロード用ファイル管理クラスのインスタンス */
    var $objDownFile;

    /** hidden 項目の配列 */
    var $arrHidden;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/product_class.tpl';
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'product';
        $this->tpl_subtitle = '商品登録(商品規格)';
    }

    function lfInitDownFile() {
    	$i = 1;
        while (isset($_POST['classcategory_id1:' . $i])) {
	        //ファイル系処理
	        $this->objDownFile->addFile("ダウンロード販売用ファイル". ":" . $i, 'down_realfilename'. ":" . $i, explode(",", DOWNLOAD_EXTENSION),DOWN_SIZE, true, 0, 0);
            $i++;
        }
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
        $this->authorization();

        $this->arrSearchHidden = $this->createSearchParams($_POST);

        // FORMデータの引き継ぎ
        $this->arrForm = $_POST;

        $this->tpl_product_id =
            isset($_POST['product_id']) ? $_POST['product_id'] : "" ;
        $this->tpl_pageno = isset($_POST['pageno']) ? $_POST['pageno'] : "";
        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        // Downファイル管理クラス
        $this->objDownFile = new SC_UploadFile(DOWN_TEMP_DIR, DOWN_SAVE_DIR);

        switch ($_POST['mode']) {
        case 'edit':
            // 入力値の変換
            $this->arrForm = $this->lfConvertParam($_POST);
            // エラーチェック
            $this->arrErr = $this->lfProductClassError($this->arrForm);
            if ($this->arrErr == null){
            //if (SC_Utils_Ex::isBlank($this->arrErr)) {
            	$this->tpl_mainpage = 'products/product_class_confirm.tpl';
                $this->lfProductConfirmPage(); // 確認ページ表示
            } else {
            	$this->doPreEdit(false,true);
		        // Hiddenからのデータを引き継ぐ
		        $this->objDownFile->setHiddenFileList($_POST);
	            // HIDDEN用に配列を渡す。
		        $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objDownFile->getHiddenFileList());
		        // Form用に配列を渡す。
		        $this->arrForm = array_merge((array)$this->arrForm, (array)$this->objDownFile->getFormKikakuDownFile());
            }
            break;

        case 'delete':
            $this->doDelete();
            break;

        case 'pre_edit':
            $this->doPreEdit();
            // HIDDEN用に配列を渡す。
	        $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objDownFile->getHiddenFileList());
            break;

        case 'disp':
            $this->doDisp();
            break;
        // ダウンロード商品ファイルアップロード
        case 'upload_down':
            $this->doPreEdit();
	        // Hiddenからのデータを引き継ぐ
	        $this->objDownFile->setHiddenKikakuFileList($_POST);
            // ファイル存在チェック
            $this->arrErr = array_merge((array)$this->arrErr, (array)$this->objDownFile->checkEXISTS($_POST['down_key']));
            // ファイル保存処理
            $this->arrErr[$_POST['down_key']] = $this->objDownFile->makeTempDownFile($_POST['down_key']);
            // HIDDEN用に配列を渡す。
	        $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objDownFile->getHiddenFileList());
	        // Form用に配列を渡す。
	        $this->arrForm = array_merge((array)$this->arrForm, (array)$this->objDownFile->getFormKikakuDownFile());
            break;
        // ダウンロードファイルの削除
        case 'delete_down':
            $this->doPreEdit();
	        // Hiddenからのデータを引き継ぐ
	        $this->objDownFile->setHiddenKikakuFileList($_POST);
	        // ファイル削除処理
            $this->objDownFile->deleteKikakuFile($_POST['down_key']);
            // HIDDEN用に配列を渡す。
	        $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objDownFile->getHiddenFileList());
	        // Form用に配列を渡す。
	        $this->arrForm = array_merge((array)$this->arrForm, (array)$this->objDownFile->getFormKikakuDownFile());
            break;
        case 'confirm_return':
            // フォームパラメータの引き継ぎ
            $this->arrForm = $_POST;
            // 規格の選択情報は引き継がない。
            $this->arrForm['select_class_id1'] = "";
            $this->arrForm['select_class_id2'] = "";
            $this->doPreEdit(false,true);
	        // Hiddenからのデータを引き継ぐ
	        $this->objDownFile->setHiddenFileList($_POST);
            // HIDDEN用に配列を渡す。
	        $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objDownFile->getHiddenFileList());
	        // Form用に配列を渡す。
	        $this->arrForm = array_merge((array)$this->arrForm, (array)$this->objDownFile->getFormKikakuDownFile());
            break;
        case 'complete':
            // 完了ページ設定
            $this->tpl_mainpage = 'products/product_class_complete.tpl';
            // ファイル情報の初期化
            $this->lfInitDownFile();
	        // Hiddenからのデータを引き継ぐ
	        $this->objDownFile->setHiddenFileList($_POST);
            // 商品規格の登録
            $this->registerProductClass($_POST, $_POST['product_id']);
            // 一時ファイルを本番ディレクトリに移動する
            $this->objDownFile->moveTempDownFile();
            break;

        default:
        }
        $this->arrClass = $this->getAllClass();
        $this->arrForm['product_name'] = $this->getProductName($_POST['product_id']);
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
     * 規格の登録または更新を行う.
     *
     * TODO dtb_class_combination は, dtb_product_categories に倣って,
     *      DELETE to INSERT だが, UPDATE を検討する.
     *
     * @param array $arrList 入力フォームの内容
     * @param integer $product_id 登録を行う商品ID
     */
    function registerProductClass($arrList, $product_id) {
        $objQuery =& SC_Query::getSingletonInstance();
        $objDb = new SC_Helper_DB_Ex();

        $objQuery->begin();

        $productsClass = $objQuery->select("*", "dtb_products_class", "product_id = ?", array($product_id));

        $exists = array();
        foreach ($productsClass as $val) {
            $exists[$val['product_class_id']] = $val;
        }

        $i = 1;
        while (isset($arrList['classcategory_id1:' . $i])) {
            $pVal = array();
            $pVal['product_id'] = $product_id;;
            $pVal['product_code'] = $arrList["product_code:".$i];
            $pVal['stock'] = $arrList["stock:".$i];
            $pVal['stock_unlimited'] = ($arrList["stock_unlimited:".$i]) ? '1' : '0';
            $pVal['price01'] = $arrList['price01:'.$i];
            $pVal['price02'] = $arrList['price02:'.$i];
            $pVal['product_type_id'] = $arrList['product_type_id:'.$i];
            $pVal['down_filename'] = $arrList['down_filename:'.$i];
            $pVal['down_realfilename'] = $arrList['down_realfilename:'.$i];
            $pVal['creator_id'] = $_SESSION['member_id'];
            $pVal['update_date'] = "now()";

            if($arrList["check:".$i] == 1) {
                $pVal['del_flg'] = 0;
            } else {
                $pVal['del_flg'] = 1;
            }

            // 更新 or 登録
            $isUpdate = false;
            if (!SC_Utils_Ex::isBlank($arrList["product_class_id:".$i])) {
                $isUpdate = true;
                // 更新の場合は規格組み合わせを検索し, 削除しておく
                $class_combination_id = $exists[$arrList["product_class_id:".$i]]['class_combination_id'];
                $existsCombi = $objQuery->getRow("dtb_class_combination",
                                                 "*", "class_combination_id = ?",
                                                 array($class_combination_id));

                $objQuery->delete("dtb_class_combination",
                                  "class_combination_id IN (?, ?)",
                                  array($existsCombi['class_combination_id'],
                                        $existsCombi['parent_class_combination_id']));
            }

            // 規格組み合わせを登録
            $cVal1['class_combination_id'] = $objQuery->nextVal('dtb_class_combination_class_combination_id');

            $cVal1['classcategory_id'] = $arrList["classcategory_id1:".$i];
            $cVal1['level'] = 1;
            $objQuery->insert("dtb_class_combination", $cVal1);

            $pVal['class_combination_id'] = $cVal1['class_combination_id'];

            // 規格2も登録する場合
            if (!SC_Utils_Ex::isBlank($arrList["classcategory_id2:".$i])) {
                $cVal2['class_combination_id'] = $objQuery->nextVal('dtb_class_combination_class_combination_id');
                $cVal2['classcategory_id'] = $arrList["classcategory_id2:".$i];
                $cVal2['parent_class_combination_id'] = $cVal1['class_combination_id'];
                $cVal2['level'] = 2;
                $objQuery->insert("dtb_class_combination", $cVal2);

                $pVal['class_combination_id'] = $cVal2['class_combination_id'];
            }

            // 更新
            if ($isUpdate) {
                $pVal['product_class_id'] = $arrList["product_class_id:".$i];
                $objQuery->update("dtb_products_class", $pVal,
                                  "product_class_id = ?",
                                  array($pVal['product_class_id']));
            }
            // 新規登録
            else {
                $pVal['create_date'] = "now()";
                $pVal['product_class_id'] = $objQuery->nextVal('dtb_products_class_product_class_id');
                $objQuery->insert("dtb_products_class", $pVal);
            }
            $i++;
        }

        // 規格無し用の商品規格を非表示に
        $bVal['del_flg'] = 1;
        $bVal['update_date'] = 'now()';
        $objQuery->update("dtb_products_class", $bVal,
                          "product_class_id = ? AND class_combination_id IS NULL",
                          array($pVal['product_class_id']));

        // 件数カウントバッチ実行
        $objDb->sfCategory_Count($objQuery);
        $objQuery->commit();
    }

    // 規格選択エラーチェック
    function lfClassError() {
        $objErr = new SC_CheckError();
        $objErr->doFunc(array("規格1", "select_class_id1"), array("EXIST_CHECK"));
        $objErr->doFunc(array("規格", "select_class_id1", "select_class_id2"), array("TOP_EXIST_CHECK"));
        $objErr->doFunc(array("規格1", "規格2", "select_class_id1", "select_class_id2"), array("DIFFERENT_CHECK"));
        return $objErr->arrErr;
    }

    /* 取得文字列の変換 */
    function lfConvertParam($array) {
        /*
         *  文字列の変換
         *  K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *  C :  「全角ひら仮名」を「全角かた仮名」に変換
         *  V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *  n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         */

        $no = 1; // FIXME 未定義変数の修正
        while($array["classcategory_id1:".$no] != "") {
            $arrConvList["product_code:".$no] = "KVa";
            $arrConvList["price01:".$no] = "n";
            $arrConvList["price02:".$no] = "n";
            $arrConvList["stock:".$no] = "n";
            $no++;
        }

        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(isset($array[$key])) {
                $array[$key] = mb_convert_kana($array[$key] ,$val);
            }
        }
        return $array;
    }

    // 商品規格エラーチェック
    function lfProductClassError($array) {
        $objErr = new SC_CheckError($array);
        $no = 1; // FIXME 未定義変数の修正
        while($array["classcategory_id1:".$no] != "") {
            if($array["check:".$no] == 1) {
                $objErr->doFunc(array("商品コード", "product_code:".$no, STEXT_LEN), array("MAX_LENGTH_CHECK"));
                $objErr->doFunc(array(NORMAL_PRICE_TITLE, "price01:".$no, PRICE_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));
                $objErr->doFunc(array(SALE_PRICE_TITLE, "price02:".$no, PRICE_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

                if($array["stock_unlimited:".$no] != '1') {
                    $objErr->doFunc(array("在庫数", "stock:".$no, AMOUNT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
                }

                //ダウンロード商品チェック
                if($array["product_type_id:".$no] == PRODUCT_TYPE_DOWNLOAD) {
                    $objErr->doFunc(array("ダウンロードファイル名", "down_filename:".$no, STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
                    if($array["down_realfilename:".$no] == "") {
                        $objErr->arrErr["down_realfilename:".$no] = "※ ダウンロード商品の場合はダウンロード商品用ファイルをアップロードしてください。<br />";
                    }
                }
                //実商品チェック
                else if($array["product_type_id:".$no] == PRODUCT_TYPE_DOWNLOAD) {
                    if($array["down_filename:".$no] != "") {
                        $objErr->arrErr["down_filename:".$no] = "※ 実商品の場合はダウンロードファイル名を設定できません。<br />";
                    }
                    if($array["down_realfilename:".$no] != "") {
                        $objErr->arrErr["down_realfilename:".$no] = "※ 実商品の場合はダウンロード商品用ファイルをアップロードできません。<br />ファイルを取り消してください。<br />";
                    }
                }
            }
            if(count($objErr->arrErr) > 0) {
                $objErr->arrErr["error:".$no] = $objErr->arrErr["product_code:".$no];
                $objErr->arrErr["error:".$no].= $objErr->arrErr["price01:".$no];
                $objErr->arrErr["error:".$no].= $objErr->arrErr["price02:".$no];
                $objErr->arrErr["error:".$no].= $objErr->arrErr["stock:".$no];
            }
            $no++;
        }
        return $objErr->arrErr;
    }

    /* 確認ページ表示用 */
    function lfProductConfirmPage() {
        $objDb = new SC_Helper_DB_Ex();
        $this->arrForm['mode'] = 'complete';
        $this->arrClass = $objDb->sfGetIDValueList("dtb_class", 'class_id', 'name');
        $cnt = 0;
        $check = 0;
        $no = 1;
        while($_POST["classcategory_id1:".$no] != "") {
            if($_POST["check:".$no] != "") {
                $check++;
            }
            $no++;
            $cnt++;
        }
        $this->tpl_check = $check;
        $this->tpl_count = $cnt;
    }

    /**
     * 規格の組み合わせ一覧を表示する.
     *
     * 1. 規格1, 規格2を組み合わせた場合の妥当性を検証する.
     * 2. 規格1, 規格2における規格分類のすべての組み合わせを取得し,
     *    該当商品の商品規格の内容を取得し, フォームに設定する.
     */
    function doDisp() {
        $this->arrForm['select_class_id1'] = $_POST['select_class_id1'];
        $this->arrForm['select_class_id2'] = $_POST['select_class_id2'];

        $this->arrErr = $this->lfClassError();
        if (SC_Utils_Ex::isBlank($this->arrErr)) {
            $this->arrClassCat = $this->getAllClassCategory($_POST['select_class_id1'], $_POST['select_class_id2']);

            $productsClass = $this->getProductsClass($_POST['product_id']);

            $total = count($this->arrClassCat);
            for ($i = 1; $i <= $total; $i++) {
                foreach ($productsClass as $key => $val) {
                    $this->arrForm[$key . ":" . $i] = $val;
                }
            }
        }
        $this->tpl_onload.= "fnCheckAllStockLimit('$total', '" . DISABLED_RGB . "');";
    }

    /**
     * 規格編集画面を表示する.
     */
    function doPreEdit($existsValue = true,$usepostValue = false) {
        $existsProductsClass = $this->getProductsClassAndClasscategory($_POST['product_id']);
        $productsClass = $this->getProductsClass($_POST['product_id']);
        $this->arrForm["class_id1"] = $existsProductsClass[0]['class_id1'];
        $this->arrForm["class_id2"] = $existsProductsClass[0]['class_id2'];
        $this->arrForm['select_class_id1'] = $this->arrForm["class_id1"];
        $this->arrForm['select_class_id2'] = $this->arrForm["class_id2"];
        $this->arrClassCat = $this->getAllClassCategory($this->arrForm["class_id1"], $this->arrForm["class_id2"]);
        $total = count($this->arrClassCat);
        for ($i = 0; $i < $total; $i++) {
            if ($existsValue) {
                foreach ($productsClass as $key => $val) {
                	if(!$usepostValue){
                    	$this->arrForm[$key . ":" . ($i+1)] = $val;
                	}
                }
            }
            foreach ($existsProductsClass[$i] as $key => $val) {
            	if(!$usepostValue){
                	$this->arrForm[$key . ":" . ($i+1)] = $val;
            	}
                if($key=="down"){
                    $this->objDownFile->addFile("ダウンロード販売用ファイル". ":" . ($i+1), 'down_realfilename'. ":" . ($i+1), explode(",", DOWNLOAD_EXTENSION),DOWN_SIZE, true, 0, 0);
                }
            }
            if (!SC_Utils_Ex::isBlank($this->arrForm['product_id:' . ($i+1)])
                && $this->arrForm["del_flg:" . ($i+1)] == 0) {
                $line .= "'check:" . ($i+1) . "',";
            }
        }

        $line = preg_replace("/,$/", "", $line);
        $this->tpl_javascript = "list = new Array($line);";
        $color = DISABLED_RGB;
        $this->tpl_onload.= "fnListCheck(list); fnCheckAllStockLimit('$total', '$color');";

        // DBデータからダウンロードファイル名の読込
        $this->objDownFile->setDBFileList($this->arrForm);
        // PostデータからダウンロードTempファイル名の読込
        $this->objDownFile->setPostFileList($_POST,$this->arrForm);
    }

    function doDelete() {
        $objQuery =& SC_Query::getSingletonInstance();

        $objQuery->begin();
        $val['del_flg'] = 0;
        $objQuery->update("dtb_products_class", $val, "product_id = ? AND class_combination_id IS NULL", array($_POST['product_id']));

        $val['del_flg'] = 1;
        $objQuery->update("dtb_products_class", $val, "product_id = ? AND class_combination_id IS NOT NULL", array($_POST['product_id']));

        $objQuery->commit();

        // 在庫無し商品の非表示対応
        if (NOSTOCK_HIDDEN === true) {
            // 件数カウントバッチ実行
            //$objDb->sfCategory_Count($objQuery);
        }
    }

    /**
     * 規格ID1, 規格ID2の規格分類すべてを取得する.
     *
     * @param integer $class_id1 規格ID1
     * @param integer $class_id2 規格ID2
     * @return array 規格と規格分類の配列
     */
    function getAllClassCategory($class_id1, $class_id2 = null) {
        $objQuery =& SC_Query::getSingletonInstance();

        $col = "T1.class_id AS class_id1, "
            . " T1.classcategory_id AS classcategory_id1, "
            . " T1.name AS name1, "
            . " T1.rank AS rank1 ";

        if(SC_Utils_Ex::isBlank($class_id2)) {
            $table = "dtb_classcategory T1 ";
            $objQuery->setWhere("T1.class_id = ?")
                     ->setOrder("T1.rank DESC");
            $val = array($class_id1);
        } else {
            $col .= ","
                . "T2.class_id AS class_id2,"
                . "T2.classcategory_id AS classcategory_id2,"
                . "T2.name AS name2,"
                . "T2.rank AS rank2";
            $table = "dtb_classcategory AS T1, dtb_classcategory AS T2";
            $objQuery->setWhere("T1.class_id = ? AND T2.class_id = ?")
                     ->setOrder("T1.rank DESC, T2.rank DESC");
            $val = array($class_id1, $class_id2);
        }
        return $objQuery->select($col, $table, "", $val);
    }

    /**
     * 商品名を取得する.
     *
     * @access private
     * @param integer $product_id 商品ID
     * @return string 商品名の文字列
     */
    function getProductName($product_id) {
        $objQuery =& SC_Query::getSingletonInstance();
        return $objQuery->getOne("SELECT name FROM dtb_products WHERE product_id = ?", array($product_id));
    }

    /**
     * 検索パラメータを生成する.
     *
     * "search_" で始まるパラメータのみを生成して返す.
     *
     * TODO パラメータの妥当性検証
     *
     * @access private
     * @param array $params 生成元の POST パラメータ
     * @return array View にアサインするパラメータの配列
     */
    function createSearchParams($params) {
        $results = array();
        foreach ($params as $key => $val) {
            if (substr($key, 0, 7) == "search_") {
                $results[$key] = $val;
            }
        }
        return $results;
    }

    /**
     * 規格分類の登録された, すべての規格を取得する.
     *
     * @access private
     * @return array 規格分類の登録された, すべての規格
     */
    function getAllClass() {
        $objDb = new SC_Helper_DB_Ex();
        $arrClass = $objDb->sfGetIDValueList("dtb_class", 'class_id', 'name');

        // 規格分類が登録されていない規格は表示しないようにする。
        $arrClassCatCount = SC_Utils_Ex::sfGetClassCatCount();

        $results = array();
        if (!SC_Utils_Ex::isBlank($arrClass)) {
            foreach($arrClass as $key => $val) {
                if($arrClassCatCount[$key] > 0) {
                    $results[$key] = $arrClass[$key];
                }
            }
        }
        return $results;
    }

    /**
     * 商品IDをキーにして, 商品規格を取得する.
     *
     * @param integer $product_id 商品ID
     * @return array 商品規格の配列
     */
    function getProductsClass($product_id) {
        $objQuery =& SC_Query::getSingletonInstance();
        return $objQuery->getRow("dtb_products_class", "*", "product_id = ?", array($product_id));
    }

    /**
     * 登録済みの商品規格, 規格, 規格分類を取得する.
     *
     * @param integer $product_id 商品ID
     * @return array 商品規格, 規格, 規格分類の配列
     */
    function getProductsClassAndClasscategory($productId) {
        $objProduct = new SC_Product();
        return $objProduct->getProductsClassFullByProductId($productId);
    }

    /**
     * @access private
     */
    function authorization() {
        SC_Utils_Ex::sfIsSuccess(new SC_Session());
    }
}
?>
