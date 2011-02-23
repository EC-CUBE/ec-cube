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
require_once(CLASS_EX_REALDIR . "page_extends/admin/LC_Page_Admin_Ex.php");

/**
 * 商品登録 のページクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_Product extends LC_Page_Admin_Ex {

    // {{{ properties

    /** ファイル管理クラスのインスタンス */
    var $objUpFile;

    /** ダウンロード用ファイル管理クラスのインスタンス */
    var $objDownFile;

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
        $this->arrDISP = $masterData->getMasterData("mtb_disp");
        $this->arrCLASS = $masterData->getMasterData("mtb_class");
        $this->arrSTATUS = $masterData->getMasterData("mtb_status");
        $this->arrSTATUS_IMAGE = $masterData->getMasterData("mtb_status_image");
        $this->arrDELIVERYDATE = $masterData->getMasterData("mtb_delivery_date");
        $this->arrAllowedTag = $masterData->getMasterData("mtb_allowed_tag");
        $this->arrProductType = $masterData->getMasterData("mtb_product_type");
        $this->arrMaker = SC_Helper_DB_Ex::sfGetIDValueList("dtb_maker", "maker_id", "name");
        $this->tpl_nonclass = true;
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
        $objSiteInfo = new SC_SiteInfo();
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();
        $objProduct = new SC_Product();

        // Downファイル管理クラス
        $this->objDownFile = new SC_UploadFile(DOWN_TEMP_REALDIR, DOWN_SAVE_REALDIR);
        // Downファイル情報の初期化
        $this->lfInitDownFile();
        // Hiddenからのデータを引き継ぐ
        $this->objDownFile->setHiddenFileList($_POST);

        // ファイル管理クラス
        $this->objUpFile = new SC_UploadFile(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);

        // ファイル情報の初期化
        $this->lfInitFile();
        // Hiddenからのデータを引き継ぐ
        $this->objUpFile->setHiddenFileList($_POST);

        // 規格の有り無し判定
        $this->tpl_nonclass = !$objDb->sfHasProductClass($_POST['product_id']);

        // 検索パラメータの引き継ぎ
        foreach ($_POST as $key => $val) {
            if (ereg("^search_", $key)) {
                $this->arrSearchHidden[$key] = $val;
            }
        }

        // FORMデータの引き継ぎ
        $this->arrForm = $_POST;

        switch($this->getMode()) {
            // 検索画面からの編集
            case 'pre_edit':
            case 'copy' :
                if (!SC_Utils_Ex::sfIsInt($_POST['product_id'])) {
                    SC_Utils_Ex::sfDispException();
                }

                // DBから商品情報の読込
                $this->arrForm = $this->lfGetProduct($_POST['product_id']);
                $productStatus= $objProduct->getProductStatus(array($_POST['product_id']));
                $this->arrForm['product_status'] = $productStatus[$_POST['product_id']];

                // DBデータから画像ファイル名の読込
                $this->objUpFile->setDBFileList($this->arrForm);
                // DBデータからダウンロードファイル名の読込
                $this->objDownFile->setDBDownFile($this->arrForm);

                // 商品ステータスの変換
                $arrRet = SC_Utils_Ex::sfSplitCBValue($this->arrForm['product_flag'], "product_flag");
                $this->arrForm = array_merge($this->arrForm, $arrRet);
                // DBから関連商品の読み込み
                $this->lfPreGetRecommendProducts($_POST['product_id']);

                $this->lfProductPage();     // 商品登録ページ
                //TODO 要リファクタリング(MODE if利用)
                if($this->getMode() == "copy"){
                    $this->arrForm["copy_product_id"] = $this->arrForm["product_id"];
                    $this->arrForm["product_id"] = "";
                    // 画像ファイルのコピー
                    $arrKey = $this->objUpFile->keyname;
                    $arrSaveFile = $this->objUpFile->save_file;

                    foreach($arrSaveFile as $key => $val){
                        $this->lfMakeScaleImage($arrKey[$key], $arrKey[$key], true);
                    }
                }
                break;
            // 商品登録・編集
            case 'edit':
                if($_POST['product_id'] == "" and SC_Utils_Ex::sfIsInt($_POST['copy_product_id'])){
                    $this->tpl_nonclass = !$objDb->sfHasProductClass($_POST['copy_product_id']);
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
                    $this->lfProductPage();     // 商品登録ページ
                }
                break;
            // 確認ページから完了ページへ
            case 'complete':
                $this->tpl_mainpage = 'products/complete.tpl';

                $this->arrForm['product_id'] = $this->lfRegistProduct($_POST);      // データ登録

                // 件数カウントバッチ実行
                $objDb->sfCountCategory($objQuery);
                $objDb->sfCountMaker($objQuery);
                // 一時ファイルを本番ディレクトリに移動する
                // TODO: SC_UploadFile::moveTempFileの画像削除条件見直し要
                $objImage = new SC_Image($this->objUpFile->temp_dir);
                $arrKeyName = $this->objUpFile->keyname;
                $arrTempFile = $this->objUpFile->temp_file;
                $arrSaveFile = $this->objUpFile->save_file;
                $arrImageKey = array();
                foreach ($arrTempFile as $key => $temp_file) {
                    if ($temp_file) {
                        $objImage->moveTempImage($temp_file, $this->objUpFile->save_dir);
                        $arrImageKey[] = $arrKeyName[$key];
                        if (!empty($arrSaveFile[$key]) && !$this->lfHasSameProductImage($this->arrForm['product_id'], $arrImageKey, $arrSaveFile[$key]) && !in_array($temp_file, $arrSaveFile)) {
                            $objImage->deleteImage($arrSaveFile[$key], $this->objUpFile->save_dir);
                        }
                    }
                }
                $this->objDownFile->moveTempDownFile();

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
                // TODO: SC_UploadFile::deleteFileの画像削除条件見直し要
                $arrTempFile = $this->objUpFile->temp_file;
                $arrKeyName = $this->objUpFile->keyname;
                foreach ($arrKeyName as $key => $keyname) {
                    if ($keyname != $_POST['image_key']) continue;
                    if (!empty($arrTempFile[$key])) {
                        $temp_file = $arrTempFile[$key];
                        $arrTempFile[$key] = '';
                        if (!in_array($temp_file, $arrTempFile)) {
                            $this->objUpFile->deleteFile($_POST['image_key']);
                        } else {
                            $this->objUpFile->temp_file[$key] = '';
                            $this->objUpFile->save_file[$key] = '';
                        }
                    } else {
                        $this->objUpFile->temp_file[$key] = '';
                        $this->objUpFile->save_file[$key] = '';
                    }
                }
                $this->lfProductPage(); // 商品登録ページ
                break;
            // ダウンロード商品ファイルアップロード
            case 'upload_down':
                // ファイル存在チェック
                $this->arrErr = array_merge((array)$this->arrErr, (array)$this->objDownFile->checkEXISTS($_POST['down_key']));
                // 画像保存処理
                $this->arrErr[$_POST['down_key']] = $this->objDownFile->makeTempDownFile();

                $this->lfProductPage(); // 商品登録ページ
                break;
            // ダウンロードファイルの削除
            case 'delete_down':
                $this->objDownFile->deleteFile($_POST['down_key']);
                $this->lfProductPage(); // 商品登録ページ
                break;
            // 確認ページからの戻り
            case 'confirm_return':
                $this->lfProductPage();     // 商品登録ページ
                break;
            // 関連商品選択
            case 'recommend_select' :
                $this->lfProductPage();     // 商品登録ページ
                break;
            default:
                $this->lfProductPage();     // 商品登録ページ
                break;
        }

        // 関連商品の読み込み
        $this->arrRecommend = $this->lfGetRecommendProducts();

        // 基本情報を渡す
        $this->arrInfo = $objSiteInfo->data;

        // サブ情報の入力があるかどうかチェックする
        $sub_find = false;
        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            if( (isset($this->arrForm['sub_title'.$cnt])
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
     * 関連商品の名称などを商品マスタから読み込み、一つの配列にまとめて返す
     *
     * @return array 関連商品の情報を格納した2次元配列
     */
    function lfGetRecommendProducts() {
        $objQuery = new SC_Query();
        $arrRecommend = array();
        for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
            $keyname = "recommend_id" . $i;
            $delkey = "recommend_delete" . $i;
            $commentkey = "recommend_comment" . $i;

            if (!isset($this->arrForm[$delkey])) $this->arrForm[$delkey] = null;

            if((isset($this->arrForm[$keyname]) && !empty($this->arrForm[$keyname])) && $this->arrForm[$delkey] != 1) {
                $objProduct = new SC_Product();
                $arrRecommend[$i] = $objProduct->getDetail($this->arrForm[$keyname]);
                $arrRecommend[$i]['product_id'] = $this->arrForm[$keyname];
                $arrRecommend[$i]['comment'] = $this->arrForm[$commentkey];
            }
        }
        return $arrRecommend;
    }

    /* 関連商品の登録 */
    function lfInsertRecommendProducts($objQuery, $arrList, $product_id) {
        // 一旦関連商品をすべて削除する
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

    /**
     * 指定商品の関連商品をDBから読み込む
     *
     * @param string $product_id 商品ID
     * @return void
     */
    function lfPreGetRecommendProducts($product_id) {
        $objQuery = new SC_Query();
        $objQuery->setOrder("rank DESC");
        $arrRet = $objQuery->select("recommend_product_id, comment", "dtb_recommend_products", "product_id = ?", array($product_id));
        $no = 1;

        foreach ($arrRet as $ret) {
            $this->arrForm['recommend_id' . $no] = $ret['recommend_product_id'];
            $this->arrForm['recommend_comment' . $no] = $ret['comment'];
            $no++;
        }
    }

    /* 商品情報の読み込み */
    function lfGetProduct($product_id) {
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        $col = "*";
        $table = <<< __EOF__
                      dtb_products AS T1
            LEFT JOIN (
                       SELECT product_id AS product_id_sub,
                              product_code,
                              price01,
                              price02,
                              stock,
                              stock_unlimited,
                              sale_limit,
                              point_rate,
                              product_type_id,
                              down_filename,
                              down_realfilename
                         FROM dtb_products_class
                        WHERE class_combination_id IS NULL
                       ) AS T2
                     ON T1.product_id = T2.product_id_sub
__EOF__;
        $where = "product_id = ?";

        $arrRet = $objQuery->select($col, $table, $where, array($product_id));

        // カテゴリID を取得
        $arrRet[0]['category_id'] = $objQuery->getCol(
            "category_id",
            "dtb_product_categories",
            "product_id = ?",
            array($product_id)
        );
        //編集時に規格IDが変わってしまうのを防ぐために規格が登録されていなければ規格IDを取得する
        if (!$objDb->sfHasProductClass($_POST['product_id'])) {
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
        if($this->arrForm['product_type_id'] == "") {
            $this->arrForm['product_type_id'] = DEFAULT_PRODUCT_DOWN;
        }

        // HIDDEN用に配列を渡す。
        $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objUpFile->getHiddenFileList());
        $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objDownFile->getHiddenFileList());
        // Form用配列を渡す。
        $this->arrFile = $this->objUpFile->getFormFileList(IMAGE_TEMP_URLPATH, IMAGE_SAVE_URLPATH);

        $this->arrForm['down_realfilename'] = $this->objDownFile->getFormDownFile();

        // アンカーを設定
        if (isset($_POST['image_key']) && !empty($_POST['image_key'])) {
            $anchor_hash = "location.hash='#" . $_POST['image_key'] . "'";
        } elseif (isset($_POST['anchor_key']) && !empty($_POST['anchor_key'])) {
            $anchor_hash = "location.hash='#" . $_POST['anchor_key'] . "'";
        } else {
            $anchor_hash = "";
        }

        $this->tpl_onload = "fnCheckStockLimit('" . DISABLED_RGB . "'); fnMoveSelect('category_id_unselect', 'category_id');" . $anchor_hash;
    }

    /* ファイル情報の初期化 */
    function lfInitFile() {
        $this->objUpFile->addFile("一覧-メイン画像", 'main_list_image', array('jpg', 'gif', 'png'),IMAGE_SIZE, false, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
        $this->objUpFile->addFile("詳細-メイン画像", 'main_image', array('jpg', 'gif', 'png'), IMAGE_SIZE, false, NORMAL_IMAGE_WIDTH, NORMAL_IMAGE_HEIGHT);
        $this->objUpFile->addFile("詳細-メイン拡大画像", 'main_large_image', array('jpg', 'gif', 'png'), IMAGE_SIZE, false, LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT);
        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $this->objUpFile->addFile("詳細-サブ画像$cnt", "sub_image$cnt", array('jpg', 'gif', 'png'), IMAGE_SIZE, false, NORMAL_SUBIMAGE_WIDTH, NORMAL_SUBIMAGE_HEIGHT);
            $this->objUpFile->addFile("詳細-サブ拡大画像$cnt", "sub_large_image$cnt", array('jpg', 'gif', 'png'), IMAGE_SIZE, false, LARGE_SUBIMAGE_WIDTH, LARGE_SUBIMAGE_HEIGHT);
        }
    }

    /* 商品の登録 */
    function lfRegistProduct($arrList) {
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();
        $objQuery->begin();

        // 配列の添字を定義
        $checkArray = array("name", "status",
                            "main_list_comment", "main_comment",
                            "deliv_fee", "comment1", "comment2", "comment3",
                            "comment4", "comment5", "comment6", "main_list_comment",
                            "sale_limit", "deliv_date_id", "maker_id", "note");
        $arrList = SC_Utils_Ex::arrayDefineIndexes($arrList, $checkArray);

        // INSERTする値を作成する。
        $sqlval['name'] = $arrList['name'];
        $sqlval['status'] = $arrList['status'];
        $sqlval['main_list_comment'] = $arrList['main_list_comment'];
        $sqlval['main_comment'] = $arrList['main_comment'];
        $sqlval['comment1'] = $arrList['comment1'];
        $sqlval['comment2'] = $arrList['comment2'];
        $sqlval['comment3'] = $arrList['comment3'];
        $sqlval['comment4'] = $arrList['comment4'];
        $sqlval['comment5'] = $arrList['comment5'];
        $sqlval['comment6'] = $arrList['comment6'];
        $sqlval['main_list_comment'] = $arrList['main_list_comment'];
        $sqlval['deliv_date_id'] = $arrList['deliv_date_id'];
        $sqlval['maker_id'] = $arrList['maker_id'];
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

        // 新規登録(複製時を含む)
        if ($arrList['product_id'] == "") {
            $product_id = $objQuery->nextVal("dtb_products_product_id");
            $sqlval['product_id'] = $product_id;

            // INSERTの実行
            $sqlval['create_date'] = "Now()";
            $objQuery->insert("dtb_products", $sqlval);

            $arrList['product_id'] = $product_id;

            // カテゴリを更新
            $objDb->updateProductCategories($arrList['category_id'], $product_id);

            // 複製商品の場合には規格も複製する
            if($_POST["copy_product_id"] != "" and SC_Utils_Ex::sfIsInt($_POST["copy_product_id"])){

                if($this->tpl_nonclass)
                {
                    //規格なしの場合、複製は価格等の入力が発生しているため、その内容で追加登録を行う
                    $this->lfCopyProductClass($arrList, $objQuery);
                }
                else
                {
                    //規格がある場合の複製は複製元の内容で追加登録を行う
                    // dtb_products_class のカラムを取得
                    $dbFactory = SC_DB_DBFactory_Ex::getInstance();
                    $arrColList = $dbFactory->sfGetColumnList("dtb_products_class", $objQuery);
                    $arrColList_tmp = array_flip($arrColList);

                    // 複製しない列
                    unset($arrColList[$arrColList_tmp["product_class_id"]]);     //規格ID
                    unset($arrColList[$arrColList_tmp["product_id"]]);           //商品ID
                    unset($arrColList[$arrColList_tmp["create_date"]]);

                    $col = SC_Utils_Ex::sfGetCommaList($arrColList);
                    $product_class_id = $objQuery->nextVal('dtb_products_class_product_class_id');
                    $objQuery->query("INSERT INTO dtb_products_class (product_class_id, product_id, create_date, ". $col .") SELECT ?, now(), " . $col. " FROM dtb_products_class WHERE product_id = ? ORDER BY product_class_id", array($product_class_id, $product_id, $_POST["copy_product_id"]));
                }
            }
        }
        // 更新
        else {
            $product_id = $arrList['product_id'];
            // 削除要求のあった既存ファイルの削除
            $arrRet = $this->lfGetProduct($arrList['product_id']);
            // TODO: SC_UploadFile::deleteDBFileの画像削除条件見直し要
            $objImage = new SC_Image($this->objUpFile->temp_dir);
            $arrKeyName = $this->objUpFile->keyname;
            $arrSaveFile = $this->objUpFile->save_file;
            $arrImageKey = array();
            foreach ($arrKeyName as $key => $keyname) {
                if ($arrRet[$keyname] && !$arrSaveFile[$key]) {
                    $arrImageKey[] = $keyname;
                    $has_same_image = $this->lfHasSameProductImage($arrList['product_id'], $arrImageKey, $arrRet[$keyname]);
                    if (!$has_same_image) {
                        $objImage->deleteImage($arrRet[$keyname], $this->objUpFile->save_dir);
                    }
                }
            }
            $this->objDownFile->deleteDBDownFile($arrRet);

            // UPDATEの実行
            $where = "product_id = ?";
            $objQuery->update("dtb_products", $sqlval, $where, array($product_id));

            // カテゴリを更新
            $objDb->updateProductCategories($arrList['category_id'], $product_id);
        }

        //商品登録の時は規格を生成する。複製の場合は規格も複製されるのでこの処理は不要。
        if( $_POST["copy_product_id"] == "" ){
            // 規格登録
            $this->lfInsertDummyProductClass($arrList);
        }

        // ステータス設定
        $objProduct = new SC_Product();
        $objProduct->setProductStatus($product_id, $arrList['product_status']);

        // 関連商品登録
        $this->lfInsertRecommendProducts($objQuery, $arrList, $product_id);

        $objQuery->commit();
        return $product_id;
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

        // スポット商品
        $arrConvList['name'] = "KVa";
        $arrConvList['main_list_comment'] = "KVa";
        $arrConvList['main_comment'] = "KVa";
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

        // 関連商品
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
        $max = max(array_keys($this->arrSTATUS));
        $array['product_flag'] = SC_Utils_Ex::sfMergeCheckBoxes($array['product_flag'], $max);

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
        $objErr->doFunc(array("メーカー", 'maker_id', INT_LEN), array("NUM_CHECK"));

        if($this->tpl_nonclass) {
            $objErr->doFunc(array("商品コード", "product_code", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK","MAX_LENGTH_CHECK"));
            $objErr->doFunc(array(NORMAL_PRICE_TITLE, "price01", PRICE_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));
            $objErr->doFunc(array(SALE_PRICE_TITLE, "price02", PRICE_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

            if(!isset($array['stock_unlimited']) && $array['stock_unlimited'] != UNLIMITED_FLG_UNLIMITED) {
                $objErr->doFunc(array("在庫数", "stock", AMOUNT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
            }

            //ダウンロード商品チェック
            if($array['product_type_id'] == PRODUCT_TYPE_DOWNLOAD) {
                $objErr->doFunc(array("ダウンロードファイル名", "down_filename", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
                if($array['down_realfilename'] == "") {
                    $objErr->arrErr['down_realfilename'] = "※ ダウンロード商品の場合はダウンロード商品用ファイルをアップロードしてください。<br />";
                }
            }
            //実商品チェック
            if($array['product_type_id'] == PRODUCT_TYPE_NORMAL) {
                if($array['down_filename'] != "") {
                    $objErr->arrErr['down_filename'] = "※ 通常商品の場合はダウンロードファイル名を設定できません。<br />";
                }
                if($array['down_realfilename'] != "") {
                    $objErr->arrErr['down_realfilename'] = "※ 通常商品の場合はダウンロード商品用ファイルをアップロードできません。<br />ファイルを取り消してください。<br />";
                }
            }
        }

        $objErr->doFunc(array("購入制限", "sale_limit", AMOUNT_LEN), array("SPTAB_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

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
                $objErr->doFunc(array("関連商品コメント$cnt", "recommend_comment$cnt", LTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
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
        $this->arrFile = $this->objUpFile->getFormFileList(IMAGE_TEMP_URLPATH, IMAGE_SAVE_URLPATH);
        $this->arrForm['down_realfilename'] = $this->objDownFile->getFormDownFile();
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

    /**
     * リネームする際は、自動生成される画像名に一意となるように、Suffixを付ける
     */
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
        //非編集項目は複製、編集項目は上書きして登録
        foreach($arrProductClass as $records)
        {
            foreach($records as $key => $value)
            {
                if(isset($arrList[$key]))
                {
                    $records[$key] = $arrList[$key];
                }
            }

            $records["product_class_id"] = $objQuery->nextVal('dtb_products_class_product_class_id');
            $records["update_date"] = 'now()';
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

    /**
     * 規格を設定していない商品を商品規格テーブルに登録
     *
     * @param array $arrList
     * @return void
     */
    function lfInsertDummyProductClass($arrList) {
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        $product_id = $arrList['product_id'];
        // 規格登録してある商品の場合、処理しない
        if ($objDb->sfHasProductClass($product_id)) return;

        // 配列の添字を定義
        $checkArray = array('product_class_id', 'product_id', 'product_code', 'stock', 'stock_unlimited', 'price01', 'price02', 'sale_limit', 'deliv_fee', 'point_rate' ,'product_type_id', 'down_filename', 'down_realfilename');
        $sqlval = SC_Utils_Ex::sfArrayIntersectKeys($arrList, $checkArray);
        $sqlval = SC_Utils_Ex::arrayDefineIndexes($sqlval, $checkArray);

        $sqlval['stock_unlimited'] = $sqlval['stock_unlimited'] ? UNLIMITED_FLG_UNLIMITED : UNLIMITED_FLG_LIMITED;
        $sqlval['creator_id'] = strlen($_SESSION['member_id']) >= 1 ? $_SESSION['member_id'] : '0';

        if (strlen($sqlval['product_class_id']) == 0) {
            $sqlval['product_class_id'] = $objQuery->nextVal('dtb_products_class_product_class_id');
            $sqlval['create_date'] = 'now()';
            $sqlval['update_date'] = 'now()';
            // INSERTの実行
            $objQuery->insert('dtb_products_class', $sqlval);
        } else {
            $sqlval['update_date'] = 'now()';
            // UPDATEの実行
            $objQuery->update('dtb_products_class', $sqlval, "product_class_id = ?", array($sqlval['product_class_id']));

        }
    }

    /* ダウンロードファイル情報の初期化 */
    function lfInitDownFile() {
        $this->objDownFile->addFile("ダウンロード販売用ファイル", 'down_file', explode(",", DOWNLOAD_EXTENSION),DOWN_SIZE, true, 0, 0);
    }

    /**
     * 同名画像ファイル登録の有無を確認する.
     *
     * 画像ファイルの削除可否判定用。
     * 同名ファイルの登録がある場合には画像ファイルの削除を行わない。
     * 戻り値： 同名ファイル有り(true) 同名ファイル無し(false)
     *
     * @param string $product_id 商品ID
     * @param string $arrImageKey 対象としない画像カラム名
     * @param string $image_file_name 画像ファイル名
     * @return boolean
     */
    function lfHasSameProductImage($product_id, $arrImageKey, $image_file_name) {
        if (!SC_Utils_Ex::sfIsInt($product_id)) return false;
        if (!$arrImageKey) return false;
        if (!$image_file_name) return false;

        $arrWhere = array();
        $sqlval = array('0', $product_id);
        foreach ($arrImageKey as $image_key) {
            $arrWhere[] = "{$image_key} = ?";
            $sqlval[] = $image_file_name;
        }
        $where = implode(" OR ", $arrWhere);
        $where = "del_flg = ? AND ((product_id <> ? AND ({$where}))";

        $arrKeyName = $this->objUpFile->keyname;
        foreach ($arrKeyName as $key => $keyname) {
            if (in_array($keyname, $arrImageKey)) continue;
            $where .= " OR {$keyname} = ?";
            $sqlval[] = $image_file_name;
        }
        $where .= ")";

        $objQuery = new SC_Query();
        $count = $objQuery->count('dtb_products', $where, $sqlval);
        if (!$count) return false;
        return true;
    }
}
?>
