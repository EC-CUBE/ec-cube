<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

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
        $objView = new SC_SiteView();
        $objCustomer = new SC_Customer();
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        // レイアウトデザインを取得
        $helper = new SC_Helper_PageLayout_Ex();
        $helper->sfGetPageLayout($this, false, "products/detail.php");

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        // ファイル管理クラス
        $this->objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
        // ファイル情報の初期化
        $this->lfInitFile();

        // 管理ページからの確認の場合は、非公開の商品も表示する。
        if(isset($_GET['admin']) && $_GET['admin'] == 'on') {
            $where = "del_flg = 0";
        } else {
            $where = "del_flg = 0 AND status = 1";
        }

        if(isset($_POST['mode']) && $_POST['mode'] != "") {
            $tmp_id = $_POST['product_id'];
        } else {
            $tmp_id = $_GET['product_id'];
        }

        // 値の正当性チェック
        if(!SC_Utils_Ex::sfIsInt($_GET['product_id'])
                || !$objDb->sfIsRecord("dtb_products", "product_id", $tmp_id, $where)) {
            SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
        }
        // ログイン判定
        if($objCustomer->isLoginSuccess()) {
            //お気に入りボタン表示
            $this->tpl_login = true;

        /* 閲覧ログ機能は現在未使用

            $table = "dtb_customer_reading";
            $where = "customer_id = ? ";
            $arrval[] = $objCustomer->getValue('customer_id');
            //顧客の閲覧商品数
            $rpcnt = $objQuery->count($table, $where, $arrval);

            //閲覧数が設定数以下
            if ($rpcnt < CUSTOMER_READING_MAX){
                //閲覧履歴に新規追加
                lfRegistReadingData($tmp_id, $objCustomer->getValue('customer_id'));
            } else {
                //閲覧履歴の中で一番古いものを削除して新規追加
                $oldsql = "SELECT MIN(update_date) FROM ".$table." WHERE customer_id = ?";
                $old = $objQuery->getone($oldsql, array($objCustomer->getValue("customer_id")));
                $where = "customer_id = ? AND update_date = ? ";
                $arrval = array($objCustomer->getValue("customer_id"), $old);
                //削除
                $objQuery->delete($table, $where, $arrval);
                //追加
                lfRegistReadingData($tmp_id, $objCustomer->getValue('customer_id'));
            }
        */
        }


        // 規格選択セレクトボックスの作成
        $this->lfMakeSelect($tmp_id);

        // 商品IDをFORM内に保持する。
        $this->tpl_product_id = $tmp_id;

        if (!isset($_['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'cart':
            // 入力値の変換
            $this->objFormParam->convParam();
            $this->arrErr = $this->lfCheckError();
            if(count($this->arrErr) == 0) {
                $objCartSess = new SC_CartSession();
                $classcategory_id1 = $_POST['classcategory_id1'];
                $classcategory_id2 = $_POST['classcategory_id2'];

                // 規格1が設定されていない場合
                if(!$this->tpl_classcat_find1) {
                    $classcategory_id1 = '0';
                }

                // 規格2が設定されていない場合
                if(!$this->tpl_classcat_find2) {
                    $classcategory_id2 = '0';
                }

                $objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
                $objCartSess->addProduct(array($_POST['product_id'], $classcategory_id1, $classcategory_id2), $this->objFormParam->getValue('quantity'));
                $this->sendRedirect($this->getLocation(URL_CART_TOP));
                exit;
            }
            break;

        default:
            break;
        }

        $objQuery = new SC_Query();
        // DBから商品情報を取得する。
        $arrRet = $objQuery->select("*", "vw_products_allclass_detail AS alldtl", "product_id = ?", array($tmp_id));
        $this->arrProduct = $arrRet[0];

        // 商品コードの取得
        $code_sql = "SELECT product_code FROM dtb_products_class AS prdcls WHERE prdcls.product_id = ? GROUP BY product_code ORDER BY product_code";
        $arrProductCode = $objQuery->getall($code_sql, array($tmp_id));
        $arrProductCode = SC_Utils_Ex::sfswaparray($arrProductCode);
        $this->arrProductCode = $arrProductCode["product_code"];

        // 購入制限数を取得
        if($this->arrProduct['sale_unlimited'] == 1 || $this->arrProduct['sale_limit'] > SALE_LIMIT_MAX) {
          $this->tpl_sale_limit = SALE_LIMIT_MAX;
        } else {
          $this->tpl_sale_limit = $this->arrProduct['sale_limit'];
        }

        // サブタイトルを取得
        $arrFirstCat = $objDb->sfGetFirstCat($arrRet[0]['category_id']);
        $this->tpl_subtitle = $arrFirstCat['name'];

        // DBからのデータを引き継ぐ
        $this->objUpFile->setDBFileList($this->arrProduct);
        // ファイル表示用配列を渡す
        $this->arrFile = $this->objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL, true);
        // 支払方法の取得
        $this->arrPayment = $this->lfGetPayment();
        // 入力情報を渡す
        $this->arrForm = $this->objFormParam->getFormParamList();
        //レビュー情報の取得
        $this->arrReview = $this->lfGetReviewData($tmp_id);
        // トラックバック情報の取得

        // トラックバック機能の稼働状況チェック
        if (SC_Utils_Ex::sfGetSiteControlFlg(SITE_CONTROL_TRACKBACK) != 1) {
            $this->arrTrackbackView = "OFF";
        } else {
            $this->arrTrackbackView = "ON";
            $this->arrTrackback = $this->lfGetTrackbackData($tmp_id);
        }
        $this->trackback_url = TRACKBACK_TO_URL . $tmp_id;
        // タイトルに商品名を入れる
        $this->tpl_title = "商品詳細 ". $this->arrProduct["name"];
        //オススメ商品情報表示
        $this->arrRecommend = $this->lfPreGetRecommendProducts($tmp_id);
        //この商品を買った人はこんな商品も買っています
        $this->arrRelateProducts = $this->lfGetRelateProducts($tmp_id);

        // 拡大画像のウィンドウサイズをセット
        if (isset($this->arrFile["main_large_image"])) {
            $image_path = IMAGE_SAVE_DIR . basename($this->arrFile["main_large_image"]["filepath"]);
        } else {
            $image_path = "";
        }

        list($large_width, $large_height) = getimagesize($image_path);
        $this->tpl_large_width = $large_width + 60;
        $this->tpl_large_height = $large_height + 80;

        $this->lfConvertParam();

        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* ファイル情報の初期化 */
    function lfInitFile() {
        $this->objUpFile->addFile("一覧-メイン画像", 'main_list_image', array('jpg','gif'),IMAGE_SIZE, true, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
        $this->objUpFile->addFile("詳細-メイン画像", 'main_image', array('jpg'), IMAGE_SIZE, true, NORMAL_IMAGE_WIDTH, NORMAL_IMAGE_HEIGHT);
        $this->objUpFile->addFile("詳細-メイン拡大画像", 'main_large_image', array('jpg'), IMAGE_SIZE, false, LARGE_IMAGE_HEIGHT, LARGE_IMAGE_HEIGHT);
        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $this->objUpFile->addFile("詳細-サブ画像$cnt", "sub_image$cnt", array('jpg'), IMAGE_SIZE, false, NORMAL_SUBIMAGE_HEIGHT, NORMAL_SUBIMAGE_HEIGHT);
            $this->objUpFile->addFile("詳細-サブ拡大画像$cnt", "sub_large_image$cnt", array('jpg'), IMAGE_SIZE, false, LARGE_SUBIMAGE_HEIGHT, LARGE_SUBIMAGE_HEIGHT);
        }
        $this->objUpFile->addFile("商品比較画像", 'file1', array('jpg'), IMAGE_SIZE, false, NORMAL_IMAGE_HEIGHT, NORMAL_IMAGE_HEIGHT);
        $this->objUpFile->addFile("商品詳細ファイル", 'file2', array('pdf'), PDF_SIZE, false, 0, 0, false);
    }

    /* 規格選択セレクトボックスの作成 */
    function lfMakeSelect($product_id) {

        $objDb = new SC_Helper_DB_Ex();
        $classcat_find1 = false;
        $classcat_find2 = false;
        // 在庫ありの商品の有無
        $stock_find = false;

        // 規格名一覧
        $arrClassName = $objDb->sfGetIDValueList("dtb_class", "class_id", "name");
        // 規格分類名一覧
        $arrClassCatName = $objDb->sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
        // 商品規格情報の取得
        $arrProductsClass = $this->lfGetProductsClass($product_id);

        // 規格1クラス名の取得
        $this->tpl_class_name1 = isset($arrClassName[$arrProductsClass[0]['class_id1']])
                                        ? $arrClassName[$arrProductsClass[0]['class_id1']] : "";
        // 規格2クラス名の取得
        $this->tpl_class_name2 = isset($arrClassName[$arrProductsClass[0]['class_id2']])
                                        ? $arrClassName[$arrProductsClass[0]['class_id2']] : "";

        // すべての組み合わせ数
        $count = count($arrProductsClass);

        $classcat_id1 = "";

        $arrSele = array();
        $arrList = array();

        $list_id = 0;
        $arrList[0] = "\tlist0 = new Array('選択してください'";
        $arrVal[0] = "\tval0 = new Array(''";

        for ($i = 0; $i < $count; $i++) {
            // 在庫のチェック
            if($arrProductsClass[$i]['stock'] <= 0 && $arrProductsClass[$i]['stock_unlimited'] != '1') {
                continue;
            }

            $stock_find = true;

            // 規格1のセレクトボックス用
            if($classcat_id1 != $arrProductsClass[$i]['classcategory_id1']){
                $arrList[$list_id].=");\n";
                $arrVal[$list_id].=");\n";
                $classcat_id1 = $arrProductsClass[$i]['classcategory_id1'];
                $arrSele[$classcat_id1] = $arrClassCatName[$classcat_id1];
                $list_id++;
            }

            // 規格2のセレクトボックス用
            $classcat_id2 = $arrProductsClass[$i]['classcategory_id2'];

            // セレクトボックス表示値
            if (!isset($arrList[$list_id])) $arrList[$list_id] = "";
            if($arrList[$list_id] == "") {
                $arrList[$list_id] = "\tlist".$list_id." = new Array('選択してください', '".$arrClassCatName[$classcat_id2]."'";
            } else {
                $arrList[$list_id].= ", '".$arrClassCatName[$classcat_id2]."'";
            }

            // セレクトボックスPOST値
            if (!isset($arrVal[$list_id])) $arrVal[$list_id] = "";
            if($arrVal[$list_id] == "") {
                $arrVal[$list_id] = "\tval".$list_id." = new Array('', '".$classcat_id2."'";
            } else {
                $arrVal[$list_id].= ", '".$classcat_id2."'";
            }
        }

        $arrList[$list_id].=");\n";
        $arrVal[$list_id].=");\n";

        // 規格1
        $this->arrClassCat1 = $arrSele;

        $lists = "\tlists = new Array(";
        $no = 0;

        foreach($arrList as $val) {
            $this->tpl_javascript.= $val;
            if ($no != 0) {
                $lists.= ",list".$no;
            } else {
                $lists.= "list".$no;
            }
            $no++;
        }
        $this->tpl_javascript.=$lists.");\n";

        $vals = "\tvals = new Array(";
        $no = 0;

        foreach($arrVal as $val) {
            $this->tpl_javascript.= $val;
            if ($no != 0) {
                $vals.= ",val".$no;
            } else {
                $vals.= "val".$no;
            }
            $no++;
        }
        $this->tpl_javascript.=$vals.");\n";

        // 選択されている規格2ID
        if (!isset($_POST['classcategory_id2'])) $_POST['classcategory_id2'] = "";
        $this->tpl_onload = "lnSetSelect('form1', 'classcategory_id1', 'classcategory_id2', '" . htmlspecialchars($_POST['classcategory_id2'], ENT_QUOTES) . "');";

        // 規格1が設定されている
        if($arrProductsClass[0]['classcategory_id1'] != '0') {
            $classcat_find1 = true;
        }

        // 規格2が設定されている
        if($arrProductsClass[0]['classcategory_id2'] != '0') {
            $classcat_find2 = true;
        }

        $this->tpl_classcat_find1 = $classcat_find1;
        $this->tpl_classcat_find2 = $classcat_find2;
        $this->tpl_stock_find = $stock_find;
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("規格1", "classcategory_id1", INT_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("規格2", "classcategory_id2", INT_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("個数", "quantity", INT_LEN, "n", array("EXIST_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
    }

    /* 商品規格情報の取得 */
    function lfGetProductsClass($product_id) {
        $arrRet = array();
        if(SC_Utils_Ex::sfIsInt($product_id)) {
            // 商品規格取得
            $objQuery = new SC_Query();
            $col = "product_class_id, classcategory_id1, classcategory_id2, class_id1, class_id2, stock, stock_unlimited";
            $table = "vw_product_class AS prdcls";
            $where = "product_id = ?";
            $objQuery->setorder("rank1 DESC, rank2 DESC");
            $arrRet = $objQuery->select($col, $table, $where, array($product_id));
        }
        return $arrRet;
    }

    /* 登録済みオススメ商品の読み込み */
    function lfPreGetRecommendProducts($product_id) {
        $arrRecommend = array();
        $objQuery = new SC_Query();
        $objQuery->setorder("rank DESC");
        $arrRet = $objQuery->select("recommend_product_id, comment", "dtb_recommend_products", "product_id = ?", array($product_id));
        $max = count($arrRet);
        $no = 0;
        for($i = 0; $i < $max; $i++) {
            $where = "del_flg = 0 AND product_id = ? AND status = 1";
            $arrProductInfo = $objQuery->select("main_list_image, price02_min, price02_max, price01_min, price01_max, name, point_rate", "vw_products_allclass  AS allcls", $where, array($arrRet[$i]['recommend_product_id']));

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

        return $objErr->arrErr;
    }

    //閲覧履歴新規登録
    function lfRegistReadingData($tmp_id, $customer_id){
        $objQuery = new SC_Query;
        $sqlval['customer_id'] = $customer_id;
        $sqlval['reading_product_id'] = $tmp_id;
        $sqlval['create_date'] = 'NOW()';
        $sqlval['update_date'] = 'NOW()';
        $objQuery->insert("dtb_customer_reading", $sqlval);
    }

    //この商品を買った人はこんな商品も買っています
    function lfGetRelateProducts($tmp_id) {
        $objQuery = new SC_Query;
        //自動抽出
        $objQuery->setorder("random()");
        //表示件数の制限
        $objQuery->setlimit(RELATED_PRODUCTS_MAX);
        //検索条件
        $col = "name, main_list_image, price01_min, price02_min, price01_max, price02_max, point_rate";
        $from = "vw_products_allclass AS allcls ";
        $where = "del_flg = 0 AND status = 1 AND (stock_max <> 0 OR stock_max IS NULL) AND product_id = ? ";
        $arrval[] = $tmp_id;
        //結果の取得
        $arrProducts = $objQuery->select($col, $from, $where, $arrval);

        return $arrProducts;
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
        $objQuery->setorder($order);
        $arrRet = $objQuery->select($col, $from, $where);
        return $arrRet;
    }

    function lfConvertParam() {
        if (!isset($this->arrForm['quantity']['value'])) $this->arrForm['quantity']['value'] = "";
        $value = $this->arrForm['quantity']['value'];
        $this->arrForm['quantity']['value'] = htmlspecialchars($value, ENT_QUOTES, CHAR_CODE);
    }
}
?>
