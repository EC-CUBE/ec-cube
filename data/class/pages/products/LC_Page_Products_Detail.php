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
require_once(CLASS_REALDIR . "pages/LC_Page.php");

if (file_exists(MODULE_REALDIR . "mdl_gmopg/inc/function.php")) {
    require_once(MODULE_REALDIR . "mdl_gmopg/inc/function.php");
}
/**
 * 商品詳細 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Products_Detail.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Products_Detail extends LC_Page {

    /** ステータス */
    var $arrSTATUS;

    /** ステータス画像 */
    var $arrSTATUS_IMAGE;

    /** 発送予定日 */
    var $arrDELIVERYDATE;

    /** おすすめレベル */
    var $arrRECOMMEND;

    /** フォームパラメータ */
    var $objFormParam;

    /** アップロードファイル */
    var $objUpFile;

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
        $this->arrSTATUS = $masterData->getMasterData("mtb_status");
        $this->arrSTATUS_IMAGE = $masterData->getMasterData("mtb_status_image");
        $this->arrDELIVERYDATE = $masterData->getMasterData("mtb_delivery_date");
        $this->arrRECOMMEND = $masterData->getMasterData("mtb_recommend");
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
        // プロダクトIDの正当性チェック
        $product_id = $this->lfCheckProductId();

        // XXX 削除可能か、SC_SiteViewクラスコンストラクタ内の処理を要確認
        $objView = new SC_SiteView(strlen($_POST['mode']) == 0);

        $objCustomer = new SC_Customer();
        $objDb = new SC_Helper_DB_Ex();

        // ログイン中のユーザが商品をお気に入りにいれる処理
        if ($objCustomer->isLoginSuccess() === true && strlen($_POST['mode']) > 0 && $_POST['mode'] == "add_favorite" && strlen($_POST['favorite_product_id']) > 0 ) {
            // 値の正当性チェック
            if(!SC_Utils_Ex::sfIsInt($_POST['favorite_product_id']) || !$objDb->sfIsRecord("dtb_products", "product_id", $_POST['favorite_product_id'], "del_flg = 0 AND status = 1")) {
                SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
                exit;
            } else {
                $this->arrErr = $this->lfCheckError();
                if(count($this->arrErr) == 0) {
                    $customer_id = $objCustomer->getValue('customer_id');
                    $this->lfRegistFavoriteProduct($customer_id, $_POST['favorite_product_id']);
                }
            }
        }

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        // ファイル管理クラス
        $this->objUpFile = new SC_UploadFile(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        // ファイル情報の初期化
        $this->lfInitFile();

        // ログイン判定
        if ($objCustomer->isLoginSuccess() === true) {
            //お気に入りボタン表示
            $this->tpl_login = true;
        }

        // 規格選択セレクトボックスの作成
        $this->lfMakeSelect($product_id);

        $objProduct = new SC_Product();
        $objProduct->setProductsClassByProductIds(array($product_id));

        // 規格1クラス名
        $this->tpl_class_name1 = $objProduct->className1[$product_id];

        // 規格2クラス名
        $this->tpl_class_name2 = $objProduct->className2[$product_id];

        // 規格1
        $this->arrClassCat1 = $objProduct->classCats1[$product_id];

        // 規格1が設定されている
        $this->tpl_classcat_find1 = $objProduct->classCat1_find[$product_id];
        // 規格2が設定されている
        $this->tpl_classcat_find2 = $objProduct->classCat2_find[$product_id];

        $this->tpl_stock_find = $objProduct->stock_find[$product_id];
        $this->tpl_product_class_id = $objProduct->classCategories[$product_id]['']['']['product_class_id'];
        $this->tpl_product_type = $objProduct->classCategories[$product_id]['']['']['product_type'];

        $objJson = new Services_JSON();
        $this->tpl_javascript .= 'classCategories = ' . $objJson->encode($objProduct->classCategories[$product_id]) . ';';
        $this->tpl_javascript .= 'function lnOnLoad(){' . $this->js_lnOnload . '}';
        $this->tpl_onload .= 'lnOnLoad();';

        // モバイル用 規格選択セレクトボックスの作成
        if(Net_UserAgent_Mobile::isMobile() === true) {
            $this->lfMakeSelectMobile($this, $product_id);
        }

        // 商品IDをFORM内に保持する
        $this->tpl_product_id = $product_id;

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
            case 'cart':
                // 入力値の変換
                $this->objFormParam->convParam();
                $this->arrErr = $this->lfCheckError();
                if (count($this->arrErr) == 0) {
                    $objCartSess = new SC_CartSession();
                    $classcategory_id1 = $_POST['classcategory_id1'];
                    $classcategory_id2 = $_POST['classcategory_id2'];
                    $product_class_id = $_POST['product_class_id'];
                    $product_type = $_POST['product_type'];

                    if (!empty($_POST['gmo_oneclick'])) {
                        $objCartSess->delAllProducts();
                    }

                    // 規格1が設定されていない場合
                    if(!$this->tpl_classcat_find1) {
                        $classcategory_id1 = '0';
                    }

                    // 規格2が設定されていない場合
                    if(!$this->tpl_classcat_find2) {
                        $classcategory_id2 = '0';
                    }
                    $objCartSess->addProduct($product_class_id, $this->objFormParam->getValue('quantity'), $product_type);

                    // カート「戻るボタン」用に保持
                    if (SC_Utils_Ex::sfIsInternalDomain($_SERVER['HTTP_REFERER'])) {
                        $_SESSION['cart_referer_url'] = $_SERVER['HTTP_REFERER'];
                    }

                    if (!empty($_POST['gmo_oneclick'])) {
                        $objSiteSess = new SC_SiteSession;
                        $objSiteSess->setRegistFlag();
                        $objCartSess->saveCurrentCart($objSiteSess->getUniqId());

                        SC_Response_Ex::sendRedirect(URL_PATH . USER_DIR . 'gmopg_oneclick_confirm.php', array(), false, true);
                        exit;
                    }

                    SC_Response_Ex::sendRedirect(CART_URLPATH);
                    exit;
                }
                break;

            default:
                break;
        }

        // モバイル用 ポストバック処理
        if(Net_UserAgent_Mobile::isMobile() === true) {
            switch($_POST['mode']) {
                case 'select':
                    // 規格1が設定されている場合
                    if($this->tpl_classcat_find1) {
                        // templateの変更
                        $this->tpl_mainpage = "products/select_find1.tpl";
                        break;
                    }

                case 'select2':
                    $this->arrErr = $this->lfCheckError();

                    // 規格1が設定されている場合
                    if($this->tpl_classcat_find1 and $this->arrErr['classcategory_id1']) {
                        // templateの変更
                        $this->tpl_mainpage = "products/select_find1.tpl";
                        break;
                    }

                    // 規格2が設定されている場合
                    if($this->tpl_classcat_find2) {
                        $this->arrErr = array();

                        $this->tpl_mainpage = "products/select_find2.tpl";
                        break;
                    }

                case 'selectItem':
                    $this->arrErr = $this->lfCheckError();

                    // 規格1が設定されている場合
                    if($this->tpl_classcat_find2 and $this->arrErr['classcategory_id2']) {
                        // templateの変更
                        $this->tpl_mainpage = "products/select_find2.tpl";
                        break;
                    }

                    $this->tpl_product_class_id = $objProduct->classCategories[$product_id][$_POST['classcategory_id1']][$_POST['classcategory_id2']]['product_class_id'];

                    // 商品数の選択を行う
                    $this->tpl_mainpage = "products/select_item.tpl";
                    break;

                default:
                    $this->tpl_mainpage = "products/detail.tpl";
                    break;
            }
        }

        // 商品詳細を取得
        $this->arrProduct = $objProduct->getDetail($product_id);

        // サブタイトルを取得
        $this->tpl_subtitle = $this->arrProduct['name'];

        // 関連カテゴリを取得
        $this->arrRelativeCat = $objDb->sfGetMultiCatTree($product_id);

        // 商品ステータスを取得
        $this->productStatus = $objProduct->getProductStatus($product_id);

        // 画像ファイル指定がない場合の置換処理
        $this->arrProduct['main_image']
            = SC_Utils_Ex::sfNoImageMain($this->arrProduct['main_image']);

        $this->lfSetFile();
        // 支払方法の取得
        $this->arrPayment = $this->lfGetPayment();
        // 入力情報を渡す
        $this->arrForm = $this->objFormParam->getFormParamList();
        //レビュー情報の取得
        $this->arrReview = $this->lfGetReviewData($product_id);
        // トラックバック情報の取得

        // トラックバック機能の稼働状況チェック
        if (SC_Utils_Ex::sfGetSiteControlFlg(SITE_CONTROL_TRACKBACK) != 1) {
            $this->arrTrackbackView = "OFF";
        } else {
            $this->arrTrackbackView = "ON";
            $this->arrTrackback = $this->lfGetTrackbackData($product_id);
        }
        $this->trackback_url = TRACKBACK_TO_URL . $product_id;
        //関連商品情報表示
        $this->arrRecommend = $this->lfPreGetRecommendProducts($product_id);

        $this->lfConvertParam();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* プロダクトIDの正当性チェック */
    function lfCheckProductId() {
        // 管理機能からの確認の場合は、非公開の商品も表示する。
        if (isset($_GET['admin']) && $_GET['admin'] == 'on') {
            SC_Utils_Ex::sfIsSuccess(new SC_Session());
            $status = true;
            $where = 'del_flg = 0';
        } else {
            $status = false;
            $where = 'del_flg = 0 AND status = 1';
        }

        if (defined('MOBILE_SITE')) {
            if (!isset($_POST['mode'])) $_POST['mode'] = "";
            if (!empty($_POST['mode'])) {
                $product_id = $_POST['product_id'];
            } else {
                $product_id = $_GET['product_id'];
            }
        } else {
            if(isset($_POST['mode']) && $_POST['mode'] != '') {
                $product_id = $_POST['product_id'];
            } else {
                $product_id = $_GET['product_id'];
            }
        }

        $objDb = new SC_Helper_DB_Ex();
        if(!SC_Utils_Ex::sfIsInt($product_id)
            || SC_Utils_Ex::sfIsZeroFilling($product_id)
            || !$objDb->sfIsRecord('dtb_products', 'product_id', (array)$product_id, $where))
            SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
        return $product_id;
    }

    /* ファイル情報の初期化 */
    function lfInitFile() {
        $this->objUpFile->addFile("詳細-メイン画像", 'main_image', array('jpg'), IMAGE_SIZE, true, NORMAL_IMAGE_WIDTH, NORMAL_IMAGE_HEIGHT);
        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $this->objUpFile->addFile("詳細-サブ画像$cnt", "sub_image$cnt", array('jpg'), IMAGE_SIZE, false, NORMAL_SUBIMAGE_HEIGHT, NORMAL_SUBIMAGE_HEIGHT);
        }
    }

    /* 規格選択セレクトボックスの作成 */
    function lfMakeSelect() {

        // 選択されている規格
        $classcategory_id1
            = isset($_POST['classcategory_id1']) && is_numeric($_POST['classcategory_id1'])
            ? $_POST['classcategory_id1']
            : '';

        $classcategory_id2
            = isset($_POST['classcategory_id2']) && is_numeric($_POST['classcategory_id2'])
            ? $_POST['classcategory_id2']
            : '';

        $this->js_lnOnload .= 'fnSetClassCategories('
            . 'document.form1, '
            . Services_JSON::encode($classcategory_id2)
            . '); ';
    }

    /* 規格選択セレクトボックスの作成(モバイル) */
    function lfMakeSelectMobile(&$objPage, $product_id) {

        $objDb = new SC_Helper_DB_Ex();
        $classcat_find1 = false;
        $classcat_find2 = false;
        // 在庫ありの商品の有無
        $stock_find = false;

        // 規格名一覧
        $arrClassName = $objDb->sfGetIDValueList("dtb_class", "class_id", "name");
        // 規格分類名一覧
        $arrClassCatName = $objDb->sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
        /*
         * FIXME
         * パフォーマンスが出ないため,
         * SC_Product::getProductsClassByProductIds() を使用した実装に変更
         */
        // 商品規格情報の取得
        $arrProductsClass = $this->lfGetProductsClass($product_id);

        // 規格1クラス名の取得
        $objPage->tpl_class_name1 = $arrClassName[$arrProductsClass[0]['class_id1']];
        // 規格2クラス名の取得
        $objPage->tpl_class_name2 = $arrClassName[$arrProductsClass[0]['class_id2']];

        // すべての組み合わせ数
        $count = count($arrProductsClass);

        $classcat_id1 = "";

        $arrSele1 = array();
        $arrSele2 = array();

        for ($i = 0; $i < $count; $i++) {
            // 在庫のチェック
            if($arrProductsClass[$i]['stock'] <= 0 && $arrProductsClass[$i]['stock_unlimited'] != '1') {
                continue;
            }

            $stock_find = true;

            // 規格1のセレクトボックス用
            if($classcat_id1 != $arrProductsClass[$i]['classcategory_id1']){
                $classcat_id1 = $arrProductsClass[$i]['classcategory_id1'];
                $arrSele1[$classcat_id1] = $arrClassCatName[$classcat_id1];
            }

            // 規格2のセレクトボックス用
            if($arrProductsClass[$i]['classcategory_id1'] == $_POST['classcategory_id1'] and $classcat_id2 != $arrProductsClass[$i]['classcategory_id2']) {
                $classcat_id2 = $arrProductsClass[$i]['classcategory_id2'];
                $arrSele2[$classcat_id2] = $arrClassCatName[$classcat_id2];
            }
        }

        // 規格1
        $objPage->arrClassCat1 = $arrSele1;
        $objPage->arrClassCat2 = $arrSele2;

        // 規格1が設定されている
        if(isset($arrProductsClass[0]['classcategory_id1']) && $arrProductsClass[0]['classcategory_id1'] != '0') {
            $classcat_find1 = true;
        }

        // 規格2が設定されている
        if(isset($arrProductsClass[0]['classcategory_id2']) && $arrProductsClass[0]['classcategory_id2'] != '0') {
            $classcat_find2 = true;
        }

        $objPage->tpl_classcat_find1 = $classcat_find1;
        $objPage->tpl_classcat_find2 = $classcat_find2;
        $objPage->tpl_stock_find = $stock_find;
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("規格1", "classcategory_id1", INT_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("規格2", "classcategory_id2", INT_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("数量", "quantity", INT_LEN, "n", array("EXIST_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
    }

    /* 商品規格情報の取得 */
    function lfGetProductsClass($product_id) {
        $objProduct = new SC_Product();
        return $objProduct->getProductsClassFullByProductId($product_id);
    }

    /* 登録済み関連商品の読み込み */
    function lfPreGetRecommendProducts($product_id) {
        $arrRecommend = array();
        $objQuery = new SC_Query();
        $objQuery->setOrder("rank DESC");
        $arrRet = $objQuery->select("recommend_product_id, comment", "dtb_recommend_products", "product_id = ?", array($product_id));
        $max = count($arrRet);
        $no = 0;
        // FIXME SC_Product クラスを使用した実装
        $from = "vw_products_allclass AS T1 "
                . " JOIN ("
                . " SELECT max(T2.rank) AS product_rank, "
                . "        T2.product_id"
                . "   FROM dtb_product_categories T2  "
                . " GROUP BY product_id) AS T3 USING (product_id)";
        $objQuery->setOrder("T3.product_rank DESC");
        for($i = 0; $i < $max; $i++) {
            $where = "del_flg = 0 AND T3.product_id = ? AND status = 1";
            $arrProductInfo = $objQuery->select("DISTINCT main_list_image, price02_min, price02_max, price01_min, price01_max, name, T3.product_rank", $from, $where, array($arrRet[$i]['recommend_product_id']));

            if(count($arrProductInfo) > 0) {
                $arrRecommend[$no] = $arrProductInfo[0];
                $arrRecommend[$no]['product_id'] = $arrRet[$i]['recommend_product_id'];
                $arrRecommend[$no]['comment'] = $arrRet[$i]['comment'];
                $no++;
            }
        }
        return $arrRecommend;
    }

    /* 入力内容のチェック */
    function lfCheckError() {
        if ($_POST['mode'] == "add_favorite") {
            $objCustomer = new SC_Customer();
            $objErr = new SC_CheckError();
            $customer_id = $objCustomer->getValue('customer_id');
            if (SC_Helper_DB_Ex::sfDataExists('dtb_customer_favorite_products', 'customer_id = ? AND product_id = ?', array($customer_id, $favorite_product_id))) {
                $objErr->arrErr['add_favorite'.$favorite_product_id] = "※ この商品は既にお気に入りに追加されています。<br />";
            }
        } else {
            // 入力データを渡す。
            $arrRet =  $this->objFormParam->getHashArray();
            $objErr = new SC_CheckError($arrRet);
            $objErr->arrErr = $this->objFormParam->checkError();

            // 複数項目チェック
            if ($this->tpl_classcat_find1) {
                $objErr->doFunc(array("規格1", "classcategory_id1"), array("EXIST_CHECK"));
            }
            if ($this->tpl_classcat_find2) {
                $objErr->doFunc(array("規格2", "classcategory_id2"), array("EXIST_CHECK"));
            }
        }

        return $objErr->arrErr;
    }

    //閲覧履歴新規登録
    function lfRegistReadingData($product_id, $customer_id){
        $objQuery = new SC_Query;
        $sqlval['customer_id'] = $customer_id;
        $sqlval['reading_product_id'] = $product_id;
        $sqlval['create_date'] = 'NOW()';
        $sqlval['update_date'] = 'NOW()';
        $objQuery->insert("dtb_customer_reading", $sqlval);
    }

    //商品ごとのレビュー情報を取得する
    function lfGetReviewData($id) {
        $objQuery = new SC_Query;
        //商品ごとのレビュー情報を取得する
        $col = "create_date, reviewer_url, reviewer_name, recommend_level, title, comment";
        $from = "dtb_review";
        $where = "del_flg = 0 AND status = 1 AND product_id = ? ORDER BY create_date DESC LIMIT " . REVIEW_REGIST_MAX;
        $arrval[] = $id;
        $arrReview = $objQuery->select($col, $from, $where, $arrval);
        return $arrReview;
    }

    /*
     * 商品ごとのトラックバック情報を取得する
     *
     * @param $product_id
     * @return $arrTrackback
     */
    function lfGetTrackbackData($product_id) {

        $arrTrackback = array();

        $objQuery = new SC_Query;
        //商品ごとのトラックバック情報を取得する
        $col = "blog_name, url, title, excerpt, title, create_date";
        $from = "dtb_trackback";
        $where = "del_flg = 0 AND status = 1 AND product_id = ? ORDER BY create_date DESC LIMIT " . TRACKBACK_VIEW_MAX;
        $arrval[] = $product_id;
        $arrTrackback = $objQuery->select($col, $from, $where, $arrval);
        return $arrTrackback;
    }

    //支払方法の取得
    //payment_id	1:クレジット　2:ショッピングローン
    function lfGetPayment() {
        $objQuery = new SC_Query;
        $col = "payment_id, rule, payment_method";
        $from = "dtb_payment";
        $where = "del_flg = 0";
        $order = "payment_id";
        $objQuery->setOrder($order);
        $arrRet = $objQuery->select($col, $from, $where);
        return $arrRet;
    }

    function lfConvertParam() {
        if (!isset($this->arrForm['quantity']['value'])) $this->arrForm['quantity']['value'] = "";
        $value = $this->arrForm['quantity']['value'];
        $this->arrForm['quantity']['value'] = htmlspecialchars($value, ENT_QUOTES, CHAR_CODE);
    }

    /*
     * ファイルの情報をセットする
     *
     */
    function lfSetFile() {
        // DBからのデータを引き継ぐ
        $this->objUpFile->setDBFileList($this->arrProduct);
        // ファイル表示用配列を渡す
        $this->arrFile = $this->objUpFile->getFormFileList(IMAGE_TEMP_URLPATH, IMAGE_SAVE_URLPATH, true);

        // サブ画像の有無を判定
        $this->subImageFlag = false;
        for ($i = 1; $i <= PRODUCTSUB_MAX; $i++) {
            if ($this->arrFile["sub_image" . $i]["filepath"] != "") {
                $this->subImageFlag = true;
            }
        }
    }

    /*
     * お気に入り商品登録
     */
    function lfRegistFavoriteProduct($customer_id, $product_id) {
        $objQuery = new SC_Query();
        $count = $objQuery->count("dtb_customer_favorite_products", "customer_id = ? AND product_id = ?", array($customer_id, $product_id));

        if ($count == 0) {
            $sqlval['customer_id'] = $customer_id;
            $sqlval['product_id'] = $product_id;
            $sqlval['update_date'] = "now()";
            $sqlval['create_date'] = "now()";

            $objQuery->begin();
            $objQuery->insert('dtb_customer_favorite_products', $sqlval);
            $objQuery->commit();
        }
    }

}
?>
