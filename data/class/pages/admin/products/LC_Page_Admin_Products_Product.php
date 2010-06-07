<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
 * 商品登録 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_Product extends LC_Page {

    // {{{ properties

    /** ファイル管理クラスのインスタンス */
    var $objUpFile;

    /** hidden 項目の配列 */
    var $arrHidden;

    /** エラー情報 */
    var $arrErr;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/product.tpl';
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'product';
        $this->tpl_subtitle = '商品登録';
        $this->arrErr = array();

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrSRANK = $masterData->getMasterData("mtb_srank");
        $this->arrDISP = $masterData->getMasterData("mtb_disp");
        $this->arrCLASS = $masterData->getMasterData("mtb_class");
        $this->arrSTATUS = $masterData->getMasterData("mtb_status");
        $this->arrSTATUS_IMAGE = $masterData->getMasterData("mtb_status_image");
        $this->arrDELIVERYDATE = $masterData->getMasterData("mtb_delivery_date");
        $this->arrAllowedTag = $masterData->getMasterData("mtb_allowed_tag");
        $this->tpl_nonclass = true;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
        $objSiteInfo = new SC_SiteInfo();
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        // ファイル管理クラス
        $this->objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);

        // ファイル情報の初期化
        $this->lfInitFile();
        // Hiddenからのデータを引き継ぐ
        $this->objUpFile->setHiddenFileList($_POST);

        // 規格の有り無し判定
        $this->tpl_nonclass = $this->lfCheckNonClass($_POST['product_id']);

        // 検索パラメータの引き継ぎ
        foreach ($_POST as $key => $val) {
            if (ereg("^search_", $key)) {
                $this->arrSearchHidden[$key] = $val;
            }
        }

        // FORMデータの引き継ぎ
        $this->arrForm = $_POST;

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        // 検索画面からの編集
        case 'pre_edit':
        case 'copy' :
            // 編集時
            if(SC_Utils_Ex::sfIsInt($_POST['product_id'])){
                // DBから商品情報の読込
                $arrForm = $this->lfGetProduct($_POST['product_id']);
                // DBデータから画像ファイル名の読込
                $this->objUpFile->setDBFileList($arrForm);

                if($_POST['mode'] == "copy"){
                    $arrForm["copy_product_id"] = $arrForm["product_id"];
                    $arrForm["product_id"] = "";
                    // 画像ファイルのコピー
                    $arrKey = $this->objUpFile->keyname;
                    $arrSaveFile = $this->objUpFile->save_file;

                    foreach($arrSaveFile as $key => $val){
                        $this->lfMakeScaleImage($arrKey[$key], $arrKey[$key], true);
                    }
                }
                $this->arrForm = $arrForm;

                // 商品ステータスの変換
                $arrRet = SC_Utils_Ex::sfSplitCBValue($this->arrForm['product_flag'], "product_flag");
                $this->arrForm = array_merge($this->arrForm, $arrRet);
                // DBからおすすめ商品の読み込み
                $this->arrRecommend = $this->lfPreGetRecommendProducts($_POST['product_id']);

                $this->lfProductPage();		// 商品登録ページ
            }
            break;
        // 商品登録・編集
        case 'edit':
            if($_POST['product_id'] == "" and SC_Utils_Ex::sfIsInt($_POST['copy_product_id'])){
                $this->tpl_nonclass = $this->lfCheckNonClass($_POST['copy_product_id']);
            }

            // 入力値の変換
            $this->arrForm = $this->lfConvertParam($this->arrForm);
            // エラーチェック
            $this->arrErr = $this->lfErrorCheck($this->arrForm);
            // ファイル存在チェック
            $this->arrErr = array_merge((array)$this->arrErr, (array)$this->objUpFile->checkEXISTS());
            // エラーなしの場合
            if(count($this->arrErr) == 0) {
                $this->lfProductConfirmPage(); // 確認ページ
            } else {
                $this->lfProductPage();		// 商品登録ページ
            }
            break;
        // 確認ページから完了ページへ
        case 'complete':
            $this->tpl_mainpage = 'products/complete.tpl';

            $this->arrForm['product_id'] = $this->lfRegistProduct($_POST);		// データ登録

            // 件数カウントバッチ実行
            $objDb->sfCategory_Count($objQuery);
            // 一時ファイルを本番ディレクトリに移動する
            $this->objUpFile->moveTempFile();

            break;
        // 画像のアップロード
        case 'upload_image':
            // ファイル存在チェック
            $this->arrErr = array_merge((array)$this->arrErr, (array)$this->objUpFile->checkEXISTS($_POST['image_key']));
            // 画像保存処理
            $this->arrErr[$_POST['image_key']] = $this->objUpFile->makeTempFile($_POST['image_key'],IMAGE_RENAME);

            // 中、小画像生成
            $this->lfSetScaleImage();

            $this->lfProductPage(); // 商品登録ページ
            break;
        // 画像の削除
        case 'delete_image':
            $this->objUpFile->deleteFile($_POST['image_key']);
            $this->lfProductPage(); // 商品登録ページ
            break;
        // 確認ページからの戻り
        case 'confirm_return':
            $this->lfProductPage();		// 商品登録ページ
            break;
        // おすすめ商品選択
        case 'recommend_select' :
            $this->lfProductPage();		// 商品登録ページ
            break;
        default:
            $this->lfProductPage();		// 商品登録ページ
            break;
        }

        if($_POST['mode'] != 'pre_edit') {
            // おすすめ商品の読み込み
            $this->arrRecommend = $this->lfGetRecommendProducts();
        }

        // 基本情報を渡す
        $this->arrInfo = $objSiteInfo->data;

        // サブ情報の入力があるかどうかチェックする
        $sub_find = false;
        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            if(	(isset($this->arrForm['sub_title'.$cnt])
                 && !empty($this->arrForm['sub_title'.$cnt])) ||
                (isset($this->arrForm['sub_comment'.$cnt])
                 && !empty($this->arrForm['sub_comment'.$cnt])) ||
                (isset($this->arrForm['sub_image'.$cnt])
                 && !empty($this->arrForm['sub_image'.$cnt])) ||
                (isset($this->arrForm['sub_large_image'.$cnt])
                 && !empty($this->arrForm['sub_large_image'.$cnt])) ||
                (isset($this->arrForm['sub_image'.$cnt])
                 && is_array($this->arrFile['sub_image'.$cnt])) ||
                (isset($this->arrForm['sub_large_image'.$cnt])
                 && is_array($this->arrFile['sub_large_image'.$cnt]))) {
                $sub_find = true;
                break;
            }
        }
        // サブ情報表示・非表示のチェックに使用する。
        $this->sub_find = $sub_find;
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

    /* おすすめ商品の読み込み */
    function lfGetRecommendProducts() {
        $objQuery = new SC_Query();
        $arrRecommend = array();
        for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
            $keyname = "recommend_id" . $i;
            $delkey = "recommend_delete" . $i;
            $commentkey = "recommend_comment" . $i;

            if (!isset($_POST[$delkey])) $_POST[$delkey] = null;

            if((isset($_POST[$keyname]) && !empty($_POST[$keyname])) && $_POST[$delkey] != 1) {
                $arrRet = $objQuery->select("main_list_image, product_code_min, name", "vw_products_allclass AS allcls", "product_id = ?", array($_POST[$keyname]));
                $arrRecommend[$i] = $arrRet[0];
                $arrRecommend[$i]['product_id'] = $_POST[$keyname];
                $arrRecommend[$i]['comment'] = $this->arrForm[$commentkey];
            }
        }
        return $arrRecommend;
    }

    /* おすすめ商品の登録 */
    function lfInsertRecommendProducts($objQuery, $arrList, $product_id) {
        // 一旦オススメ商品をすべて削除する
        $objQuery->delete("dtb_recommend_products", "product_id = ?", array($product_id));
        $sqlval['product_id'] = $product_id;
        $rank = RECOMMEND_PRODUCT_MAX;
        for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
            $keyname = "recommend_id" . $i;
            $commentkey = "recommend_comment" . $i;
            $deletekey = "recommend_delete" . $i;

            if (!isset($arrList[$deletekey])) $arrList[$deletekey] = null;

            if($arrList[$keyname] != "" && $arrList[$deletekey] != '1') {
                $sqlval['recommend_product_id'] = $arrList[$keyname];
                $sqlval['comment'] = $arrList[$commentkey];
                $sqlval['rank'] = $rank;
                $sqlval['creator_id'] = $_SESSION['member_id'];
                $sqlval['create_date'] = "now()";
                $sqlval['update_date'] = "now()";
                $objQuery->insert("dtb_recommend_products", $sqlval);
                $rank--;
            }
        }
    }

    /* 登録済みおすすめ商品の読み込み */
    function lfPreGetRecommendProducts($product_id) {
        $arrRecommend = array();
        $objQuery = new SC_Query();
        $objQuery->setOrder("rank DESC");
        $arrRet = $objQuery->select("recommend_product_id, comment", "dtb_recommend_products", "product_id = ?", array($product_id));
        $max = count($arrRet);
        $no = 1;

        for($i = 0; $i < $max; $i++) {
            $arrProductInfo = $objQuery->select("main_list_image, product_code_min, name", "vw_products_allclass AS allcls", "product_id = ?", array($arrRet[$i]['recommend_product_id']));
            $arrRecommend[$no] = $arrProductInfo[0];
            $arrRecommend[$no]['product_id'] = $arrRet[$i]['recommend_product_id'];
            $arrRecommend[$no]['comment'] = $arrRet[$i]['comment'];
            $no++;
        }
        return $arrRecommend;
    }

    /* 商品情報の読み込み */
    function lfGetProduct($product_id) {
        $objQuery = new SC_Query();
        $col = "*";
        $table = "vw_products_nonclass AS noncls ";
        $where = "product_id = ?";

        $arrRet = $objQuery->select($col, $table, $where, array($product_id));

        // カテゴリID を取得
        $arrRet[0]['category_id'] = $objQuery->getCol("dtb_product_categories",
                                                      "category_id",
                                                      "product_id = ?",
                                                      array($product_id));
        //編集時に規格IDが変わってしまうのを防ぐために規格が登録されていなければ規格IDを取得する
        if( $this->lfCheckNonClass($_POST['product_id']) ){
            $arrRet[0]['product_class_id'] = SC_Utils::sfGetProductClassId($product_id,"0","0");
        }
        return $arrRet[0];
    }

    /* 商品登録ページ表示用 */
    function lfProductPage() {
        $objDb = new SC_Helper_DB_Ex();

        // カテゴリの読込
        list($this->arrCatVal, $this->arrCatOut) = $objDb->sfGetLevelCatList(false);

        if (isset($this->arrForm['category_id']) && !is_array($this->arrForm['category_id'])) {
            $this->arrForm['category_id'] = unserialize($this->arrForm['category_id']);
        }
        if($this->arrForm['status'] == "") {
            $this->arrForm['status'] = DEFAULT_PRODUCT_DISP;
        }

        if(isset($this->arrForm['product_flag']) && !is_array($this->arrForm['product_flag'])) {
            // 商品ステータスの分割読込
            $this->arrForm['product_flag'] = SC_Utils_Ex::sfSplitCheckBoxes($this->arrForm['product_flag']);
        }

        // HIDDEN用に配列を渡す。
        $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objUpFile->getHiddenFileList());
        // Form用配列を渡す。
        $this->arrFile = $this->objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);


        // アンカーを設定
        if (isset($_POST['image_key']) && !empty($_POST['image_key'])) {
            $anchor_hash = "location.hash='#" . $_POST['image_key'] . "'";
        } elseif (isset($_POST['anchor_key']) && !empty($_POST['anchor_key'])) {
            $anchor_hash = "location.hash='#" . $_POST['anchor_key'] . "'";
        } else {
            $anchor_hash = "";
        }

        $this->tpl_onload = "fnCheckSaleLimit('" . DISABLED_RGB . "'); fnCheckStockLimit('" . DISABLED_RGB . "'); fnMoveSelect('category_id_unselect', 'category_id');" . $anchor_hash;
    }

    /* ファイル情報の初期化 */
    function lfInitFile() {
        $this->objUpFile->addFile("一覧-メイン画像", 'main_list_image', array('jpg', 'gif', 'png'),IMAGE_SIZE, true, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
        $this->objUpFile->addFile("詳細-メイン画像", 'main_image', array('jpg', 'gif', 'png'), IMAGE_SIZE, true, NORMAL_IMAGE_WIDTH, NORMAL_IMAGE_HEIGHT);
        $this->objUpFile->addFile("詳細-メイン拡大画像", 'main_large_image', array('jpg', 'gif', 'png'), IMAGE_SIZE, false, LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT);
        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $this->objUpFile->addFile("詳細-サブ画像$cnt", "sub_image$cnt", array('jpg', 'gif', 'png'), IMAGE_SIZE, false, NORMAL_SUBIMAGE_WIDTH, NORMAL_SUBIMAGE_HEIGHT);
            $this->objUpFile->addFile("詳細-サブ拡大画像$cnt", "sub_large_image$cnt", array('jpg', 'gif', 'png'), IMAGE_SIZE, false, LARGE_SUBIMAGE_WIDTH, LARGE_SUBIMAGE_HEIGHT);
        }
        $this->objUpFile->addFile("商品比較画像", 'file1', array('jpg', 'gif', 'png'), IMAGE_SIZE, false, OTHER_IMAGE1_WIDTH, OTHER_IMAGE1_HEIGHT);
        $this->objUpFile->addFile("商品詳細ファイル", 'file2', array('pdf'), PDF_SIZE, false, 0, 0, false);
    }

    /* 商品の登録 */
    function lfRegistProduct($arrList) {
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();
        $objQuery->begin();

        // 配列の添字を定義
        $checkArray = array("name", "status", "product_flag",
                            "main_list_comment", "main_comment", "point_rate",
                            "deliv_fee", "comment1", "comment2", "comment3",
                            "comment4", "comment5", "comment6", "main_list_comment",
                            "sale_limit", "sale_unlimited", "deliv_date_id", "note");
        $arrList = SC_Utils_Ex::arrayDefineIndexes($arrList, $checkArray);

        // INSERTする値を作成する。
        $sqlval['name'] = $arrList['name'];
        $sqlval['status'] = $arrList['status'];
        $sqlval['product_flag'] = $arrList['product_flag'];
        $sqlval['main_list_comment'] = $arrList['main_list_comment'];
        $sqlval['main_comment'] = $arrList['main_comment'];
        $sqlval['point_rate'] = $arrList['point_rate'];
        $sqlval['deliv_fee'] = $arrList['deliv_fee'];
        $sqlval['comment1'] = $arrList['comment1'];
        $sqlval['comment2'] = $arrList['comment2'];
        $sqlval['comment3'] = $arrList['comment3'];
        $sqlval['comment4'] = $arrList['comment4'];
        $sqlval['comment5'] = $arrList['comment5'];
        $sqlval['comment6'] = $arrList['comment6'];
        $sqlval['main_list_comment'] = $arrList['main_list_comment'];
        $sqlval['sale_limit'] = $arrList['sale_limit'];
        $sqlval['sale_unlimited'] = $arrList['sale_unlimited'];
        $sqlval['deliv_date_id'] = $arrList['deliv_date_id'];
        $sqlval['note'] = $arrList['note'];
        $sqlval['update_date'] = "Now()";
        $sqlval['creator_id'] = $_SESSION['member_id'];
        $arrRet = $this->objUpFile->getDBFileList();
        $sqlval = array_merge($sqlval, $arrRet);

        $arrList['category_id'] = unserialize($arrList['category_id']);

        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $sqlval['sub_title'.$cnt] = $arrList['sub_title'.$cnt];
            $sqlval['sub_comment'.$cnt] = $arrList['sub_comment'.$cnt];
        }

        if($arrList['product_id'] == "") {
            // product_id 取得（PostgreSQLの場合）
            if(DB_TYPE=='pgsql'){
                $product_id = $objQuery->nextval("dtb_products", "product_id");
                $sqlval['product_id'] = $product_id;
            }

            // INSERTの実行
            $sqlval['create_date'] = "Now()";
            $objQuery->insert("dtb_products", $sqlval);

            // product_id 取得（MySQLの場合）
            if(DB_TYPE=='mysql'){
                $product_id = $objQuery->nextval("dtb_products", "product_id");
            }

            // カテゴリを更新
            $objDb->updateProductCategories($arrList['category_id'], $product_id);

            // コピー商品の場合には規格もコピーする
            if($_POST["copy_product_id"] != "" and SC_Utils_Ex::sfIsInt($_POST["copy_product_id"])){

                if($this->tpl_nonclass)
                {
                    //規格なしの場合、コピーは価格等の入力が発生しているため、その内容で追加登録を行う
                    $arrList['product_id'] = $product_id;
                    $this->lfCopyProductClass($arrList, $objQuery);
                }
                else
                {
                    //規格がある場合のコピーは複製元の内容で追加登録を行う
                    // dtb_products_class のカラムを取得
                    $dbFactory = SC_DB_DBFactory_Ex::getInstance();
                    $arrColList = $dbFactory->sfGetColumnList("dtb_products_class", $objQuery);
                    $arrColList_tmp = array_flip($arrColList);

                    // コピーしない列
                    unset($arrColList[$arrColList_tmp["product_class_id"]]);    //規格ID
                    unset($arrColList[$arrColList_tmp["product_id"]]);            //商品ID
                    unset($arrColList[$arrColList_tmp["create_date"]]);

                    $col = SC_Utils_Ex::sfGetCommaList($arrColList);

                    $objQuery->query("INSERT INTO dtb_products_class (product_id, create_date, ". $col .") SELECT ?, now(), " . $col. " FROM dtb_products_class WHERE product_id = ? ORDER BY product_class_id", array($product_id, $_POST["copy_product_id"]));
                }
            }
        } else {
            $product_id = $arrList['product_id'];
            // 削除要求のあった既存ファイルの削除
            $arrRet = $this->lfGetProduct($arrList['product_id']);
            $this->objUpFile->deleteDBFile($arrRet);

            // UPDATEの実行
            $where = "product_id = ?";
            $objQuery->update("dtb_products", $sqlval, $where, array($product_id));

            // カテゴリを更新
            $objDb->updateProductCategories($arrList['category_id'], $product_id);
        }

        //商品登録の時は規格を生成する。複製の場合は規格も複製されるのでこの処理は不要。
        if( $_POST["copy_product_id"] == "" ){
            // 規格登録
            SC_Utils_Ex::sfInsertProductClass($objQuery, $arrList, $product_id , $arrList['product_class_id'] );
        }

        // おすすめ商品登録
        $this->lfInsertRecommendProducts($objQuery, $arrList, $product_id);

        $objQuery->commit();
        return $product_id;
    }


    /* 取得文字列の変換 */
    function lfConvertParam($array) {
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         */

        // スポット商品
        $arrConvList['name'] = "KVa";
        $arrConvList['main_list_comment'] = "KVa";
        $arrConvList['main_comment'] = "KVa";
        $arrConvList['note'] = "KVa";
        $arrConvList['price01'] = "n";
        $arrConvList['price02'] = "n";
        $arrConvList['stock'] = "n";
        $arrConvList['sale_limit'] = "n";
        $arrConvList['point_rate'] = "n";
        $arrConvList['product_code'] = "KVna";
        $arrConvList['comment1'] = "a";
        $arrConvList['deliv_fee'] = "n";

        // 詳細-サブ
        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $arrConvList["sub_title$cnt"] = "KVa";
        }
        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $arrConvList["sub_comment$cnt"] = "KVa";
        }

        // おすすめ商品
        for ($cnt = 1; $cnt <= RECOMMEND_PRODUCT_MAX; $cnt++) {
            $arrConvList["recommend_comment$cnt"] = "KVa";
        }

        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(isset($array[$key])) {
                $array[$key] = mb_convert_kana($array[$key] ,$val);
            }
        }

        if (!isset($array['product_flag'])) $array['product_flag'] = "";
        $array['product_flag'] = SC_Utils_Ex::sfMergeCheckBoxes($array['product_flag'], count($this->arrSTATUS));

        return $array;
    }

    // 入力エラーチェック
    function lfErrorCheck($array) {

        $objErr = new SC_CheckError($array);
        $objErr->doFunc(array("商品名", "name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("一覧-メインコメント", "main_list_comment", MTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("詳細-メインコメント", "main_comment", LLTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("詳細-メインコメント", "main_comment", $this->arrAllowedTag), array("HTML_TAG_CHECK"));
        $objErr->doFunc(array("ポイント付与率", "point_rate", PERCENTAGE_LEN), array("EXIST_CHECK", "NUM_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("商品送料", "deliv_fee", PRICE_LEN), array("NUM_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("備考欄(SHOP専用)", "note", LLTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("検索ワード", "comment3", LLTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("メーカーURL", "comment1", URL_LEN), array("SPTAB_CHECK", "URL_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("発送日目安", "deliv_date_id", INT_LEN), array("NUM_CHECK"));

        if($this->tpl_nonclass) {
            $objErr->doFunc(array("商品コード", "product_code", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK","MAX_LENGTH_CHECK"));
            $objErr->doFunc(array("通常価格", "price01", PRICE_LEN), array("ZERO_CHECK", "SPTAB_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
            $objErr->doFunc(array("商品価格", "price02", PRICE_LEN), array("EXIST_CHECK", "NUM_CHECK", "ZERO_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));

            if(!isset($array['stock_unlinited']) && $array['stock_unlimited'] != "1") {
                $objErr->doFunc(array("在庫数", "stock", AMOUNT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
            }
        }

        if(!isset($array['sale_unlimited']) && $array['sale_unlimited'] != "1") {
            $objErr->doFunc(array("購入制限", "sale_limit", AMOUNT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        }

        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $objErr->doFunc(array("詳細-サブタイトル$cnt", "sub_title$cnt", STEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $objErr->doFunc(array("詳細-サブコメント$cnt", "sub_comment$cnt", LLTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $objErr->doFunc(array("詳細-サブコメント$cnt", "sub_comment$cnt", $this->arrAllowedTag),  array("HTML_TAG_CHECK"));
        }

        for ($cnt = 1; $cnt <= RECOMMEND_PRODUCT_MAX; $cnt++) {

            if (!isset($_POST["recommend_delete$cnt"]))  $_POST["recommend_delete$cnt"] = "";

            if(isset($_POST["recommend_id$cnt"])
               && $_POST["recommend_id$cnt"] != ""
               && $_POST["recommend_delete$cnt"] != 1) {
                $objErr->doFunc(array("おすすめ商品コメント$cnt", "recommend_comment$cnt", LTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            }
        }

        // カテゴリID のチェック
        if (empty($array['category_id'])) {
            $objErr->arrErr['category_id'] = "※ 商品カテゴリが選択されていません。<br />";
        } else {
            $arrCategory_id = array();
            for ($i = 0; $i < count($array['category_id']); $i++) {
                $arrCategory_id['category_id' . $i] = $array['category_id'][$i];
            }
            $objCheckCategory = new SC_CheckError($arrCategory_id);
            for ($i = 0; $i < count($array['category_id']); $i++) {
                $objCheckCategory->doFunc(array("商品カテゴリ", "category_id" . $i, STEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            }
            if (!empty($objCheckCategory->arrErr)) {
                $objErr->arrErr = array_merge($objErr->arrErr,
                                              $objCheckCategory->arrErr);
            }
        }
        return $objErr->arrErr;
    }

    /* 確認ページ表示用 */
    function lfProductConfirmPage() {
        $this->tpl_mainpage = 'products/confirm.tpl';
        $this->arrForm['mode'] = 'complete';

        $objDb = new SC_Helper_DB_Ex();

        // カテゴリ表示
        $this->arrCategory_id = $this->arrForm['category_id'];
        $this->arrCatList = array();
        list($arrCatVal, $arrCatOut) = $objDb->sfGetLevelCatList(false);
        for ($i = 0; $i < count($arrCatVal); $i++) {
            $this->arrCatList[$arrCatVal[$i]] = $arrCatOut[$i];
        }

        // hidden に渡す値は serialize する
        $this->arrForm['category_id'] = serialize($this->arrForm['category_id']);

        // Form用配列を渡す。
        $this->arrFile = $this->objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
    }

    /* 規格あり判定用(規格が登録されていない場合:TRUE) */
    function lfCheckNonClass($product_id) {
        if(SC_Utils_Ex::sfIsInt($product_id)) {
            $objQuery  = new SC_Query();
            $where = "product_id = ? AND classcategory_id1 <> 0 AND classcategory_id1 <> 0";
            $count = $objQuery->count("dtb_products_class", $where, array($product_id));
            if($count > 0) {
                return false;
            }
        }
        return true;
    }

    // 縮小した画像をセットする
    function lfSetScaleImage(){

        $subno = str_replace("sub_large_image", "", $_POST['image_key']);
        switch ($_POST['image_key']){
            case "main_large_image":
                // 詳細メイン画像
                $this->lfMakeScaleImage($_POST['image_key'], "main_image");
            case "main_image":
                // 一覧メイン画像
                $this->lfMakeScaleImage($_POST['image_key'], "main_list_image");
                break;
            case "sub_large_image" . $subno:
                // サブメイン画像
                $this->lfMakeScaleImage($_POST['image_key'], "sub_image" . $subno);
                break;
            default:
                break;
        }
    }

    // 縮小画像生成
    function lfMakeScaleImage($from_key, $to_key, $forced = false){
        $arrImageKey = array_flip($this->objUpFile->keyname);

        if($this->objUpFile->temp_file[$arrImageKey[$from_key]]){
            $from_path = $this->objUpFile->temp_dir . $this->objUpFile->temp_file[$arrImageKey[$from_key]];
        }elseif($this->objUpFile->save_file[$arrImageKey[$from_key]]){
            $from_path = $this->objUpFile->save_dir . $this->objUpFile->save_file[$arrImageKey[$from_key]];
        }else{
            return "";
        }

        if(file_exists($from_path)){
            // 元画像サイズを取得
            list($from_w, $from_h) = getimagesize($from_path);

            // 生成先の画像サイズを取得
            $to_w = $this->objUpFile->width[$arrImageKey[$to_key]];
            $to_h = $this->objUpFile->height[$arrImageKey[$to_key]];


            if($forced) $this->objUpFile->save_file[$arrImageKey[$to_key]] = "";

            if(empty($this->objUpFile->temp_file[$arrImageKey[$to_key]]) &&
               empty($this->objUpFile->save_file[$arrImageKey[$to_key]])) {

                // リネームする際は、自動生成される画像名に一意となるように、Suffixを付ける
                $dst_file = $this->objUpFile->lfGetTmpImageName(IMAGE_RENAME, "", $this->objUpFile->temp_file[$arrImageKey[$from_key]]) . $this->lfGetAddSuffix($to_key);
                $path = $this->objUpFile->makeThumb($from_path, $to_w, $to_h, $dst_file);
                $this->objUpFile->temp_file[$arrImageKey[$to_key]] = basename($path);
            }
        }else{
            return "";
        }
    }

    // リネームする際は、自動生成される画像名に一意となるように、Suffixを付ける
    function lfGetAddSuffix($to_key){
        if( IMAGE_RENAME === true ){ return ; }

        // 自動生成される画像名
        $dist_name = "";
        switch($to_key){
            case "main_list_image":
                $dist_name = '_s';
                break;
            case "main_image":
                $dist_name = '_m';
                break;
            default:
                $arrRet = explode('sub_image', $to_key);
                $dist_name = '_sub' .$arrRet[1];
                break;
        }
        return $dist_name;
    }

    /**
    * dtb_products_classの複製
    * 複製後、価格や商品コードを更新する
    *
    * @param array $arrList
    * @param array $objQuery
    * @return bool
    */
    function lfCopyProductClass($arrList,$objQuery)
    {
        // 複製元のdtb_products_classを取得（規格なしのため、1件のみの取得）
        $col = "*";
        $table = "dtb_products_class";
        $where = "product_id = ?";
        $arrProductClass = $objQuery->select($col, $table, $where, array($arrList["copy_product_id"]));

        //トランザクション開始
        $objQuery->begin();
        $err_flag = false;
        //非編集項目はコピー、編集項目は上書きして登録
        foreach($arrProductClass as $records)
        {
            foreach($records as $key => $value)
            {
                if(isset($arrList[$key]))
                {
                    $records[$key] = $arrList[$key];
                }
            }
            unset($records["product_class_id"]);
            unset($records["update_date"]);

            $records["create_date"] = "Now()";
            $objQuery->insert($table, $records);
            //エラー発生時は中断
            if($objQuery->isError())
            {
                $err_flag = true;
                continue;
	        }
        }
        //トランザクション終了
        if($err_flag)
        {
            $objQuery->rollback();
        }
        else
        {
            $objQuery->commit();
        }
        return !$err_flag;
    }
}
?>
