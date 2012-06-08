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

if (file_exists(MODULE_REALDIR . 'mdl_gmopg/inc/function.php')) {
    require_once MODULE_REALDIR . 'mdl_gmopg/inc/function.php';
}
/**
 * 商品詳細 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Products_Detail.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Products_Detail extends LC_Page_Ex {

    /** 商品ステータス */
    var $arrSTATUS;

    /** 商品ステータス画像 */
    var $arrSTATUS_IMAGE;

    /** 発送予定日 */
    var $arrDELIVERYDATE;

    /** おすすめレベル */
    var $arrRECOMMEND;

    /** フォームパラメーター */
    var $objFormParam;

    /** アップロードファイル */
    var $objUpFile;

    /** モード */
    var $mode;

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
        $this->arrSTATUS = $masterData->getMasterData('mtb_status');
        $this->arrSTATUS_IMAGE = $masterData->getMasterData('mtb_status_image');
        $this->arrDELIVERYDATE = $masterData->getMasterData('mtb_delivery_date');
        $this->arrRECOMMEND = $masterData->getMasterData('mtb_recommend');
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
        // 会員クラス
        $objCustomer = new SC_Customer_Ex();

        // パラメーター管理クラス
        $this->objFormParam = new SC_FormParam_Ex();
        // パラメーター情報の初期化
        $this->arrForm = $this->lfInitParam($this->objFormParam);
        // ファイル管理クラス
        $this->objUpFile = new SC_UploadFile_Ex(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        // ファイル情報の初期化
        $this->objUpFile = $this->lfInitFile($this->objUpFile);

        // プロダクトIDの正当性チェック
        $product_id = $this->lfCheckProductId($this->objFormParam->getValue('admin'),$this->objFormParam->getValue('product_id'));
        $this->mode = $this->getMode();

        $objProduct = new SC_Product_Ex();
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
        $this->tpl_product_class_id = $objProduct->classCategories[$product_id]['__unselected']['__unselected']['product_class_id'];
        $this->tpl_product_type = $objProduct->classCategories[$product_id]['__unselected']['__unselected']['product_type'];

        // 在庫が無い場合は、OnLoadしない。(javascriptエラー防止)
        if ($this->tpl_stock_find) {
            // 規格選択セレクトボックスの作成
            $this->js_lnOnload .= $this->lfMakeSelect();
        }

        $this->tpl_javascript .= 'classCategories = ' . SC_Utils_Ex::jsonEncode($objProduct->classCategories[$product_id]) . ';';
        $this->tpl_javascript .= 'function lnOnLoad(){' . $this->js_lnOnload . '}';
        $this->tpl_onload .= 'lnOnLoad();';

        // モバイル用 規格選択セレクトボックスの作成
        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
            $this->lfMakeSelectMobile($this, $product_id,$this->objFormParam->getValue('classcategory_id1'));
        }

        // 商品IDをFORM内に保持する
        $this->tpl_product_id = $product_id;

        switch ($this->mode) {
            case 'cart':
                $this->arrErr = $this->lfCheckError($this->mode,$this->objFormParam,
                                                    $this->tpl_classcat_find1,
                                                    $this->tpl_classcat_find2);
                if (count($this->arrErr) == 0) {
                    $objCartSess = new SC_CartSession_Ex();
                    $product_class_id = $this->objFormParam->getValue('product_class_id');

                    $objCartSess->addProduct($product_class_id, $this->objFormParam->getValue('quantity'));


                    SC_Response_Ex::sendRedirect(CART_URLPATH);
                    SC_Response_Ex::actionExit();
                }
                break;
            case 'add_favorite':
                // ログイン中のユーザが商品をお気に入りにいれる処理
                if ($objCustomer->isLoginSuccess() === true && $this->objFormParam->getValue('favorite_product_id') > 0) {
                    $this->arrErr = $this->lfCheckError($this->mode,$this->objFormParam);
                    if (count($this->arrErr) == 0) {
                        if (!$this->lfRegistFavoriteProduct($this->objFormParam->getValue('favorite_product_id'),$objCustomer->getValue('customer_id'))) {

                            $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
                            $objPlugin->doAction('LC_Page_Products_Detail_action_add_favorite', array($this));

                            SC_Response_Ex::actionExit();
                        }
                    }
                }
                break;

            case 'add_favorite_sphone':
                // ログイン中のユーザが商品をお気に入りにいれる処理(スマートフォン用)
                if ($objCustomer->isLoginSuccess() === true && $this->objFormParam->getValue('favorite_product_id') > 0) {
                    $this->arrErr = $this->lfCheckError($this->mode,$this->objFormParam);
                    if (count($this->arrErr) == 0) {
                        if ($this->lfRegistFavoriteProduct($this->objFormParam->getValue('favorite_product_id'),$objCustomer->getValue('customer_id'))) {

                            $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
                            $objPlugin->doAction('LC_Page_Products_Detail_action_add_favorite_sphone', array($this));

                            print 'true';
                            SC_Response_Ex::actionExit();
                        }
                    }
                    print 'error';
                    SC_Response_Ex::actionExit();
                }
                break;

            case 'select':
            case 'select2':
            case 'selectItem':
                /**
                 * モバイルの数量指定・規格選択の際に、
                 * $_SESSION['cart_referer_url'] を上書きさせないために、
                 * 何もせずbreakする。
                 */
                break;

            default:
                // カート「戻るボタン」用に保持
                $netURL = new Net_URL();
                $_SESSION['cart_referer_url'] = $netURL->getURL();
                break;
        }

        // モバイル用 ポストバック処理
        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
            switch ($this->mode) {
                case 'select':
                    // 規格1が設定されている場合
                    if ($this->tpl_classcat_find1) {
                        // templateの変更
                        $this->tpl_mainpage = 'products/select_find1.tpl';
                        break;
                    }

                    // 数量の入力を行う
                    $this->tpl_mainpage = 'products/select_item.tpl';
                    break;

                case 'select2':
                    $this->arrErr = $this->lfCheckError($this->mode,$this->objFormParam,$this->tpl_classcat_find1,$this->tpl_classcat_find2);

                    // 規格1が設定されていて、エラーを検出した場合
                    if ($this->tpl_classcat_find1 and $this->arrErr['classcategory_id1']) {
                        // templateの変更
                        $this->tpl_mainpage = 'products/select_find1.tpl';
                        break;
                    }

                    // 規格2が設定されている場合
                    if ($this->tpl_classcat_find2) {
                        $this->arrErr = array();

                        $this->tpl_mainpage = 'products/select_find2.tpl';
                        break;
                    }
                    
                case 'selectItem':
                    $this->arrErr = $this->lfCheckError($this->mode,$this->objFormParam,$this->tpl_classcat_find1,$this->tpl_classcat_find2);

                    // 規格2が設定されていて、エラーを検出した場合
                    if ($this->tpl_classcat_find2 and $this->arrErr['classcategory_id2']) {
                        // templateの変更
                        $this->tpl_mainpage = 'products/select_find2.tpl';
                        break;
                    }

                    $value1 = $this->objFormParam->getValue('classcategory_id1');
                    
                    // 規格2が設定されている場合.
                    if (SC_Utils_Ex::isBlank($this->objFormParam->getValue('classcategory_id2')) == false){
                        $value2 = '#' . $this->objFormParam->getValue('classcategory_id2');
                    } else {
                        $value2 = '#0';
                    }
                    
                    if (strlen($value1) === 0) {
                        $value1 = '__unselected';
                    }

                    $this->tpl_product_class_id = $objProduct->classCategories[$product_id][$value1][$value2]['product_class_id'];

                    // この段階では、数量の入力チェックエラーを出させない。
                    unset($this->arrErr['quantity']);

                    // 数量の入力を行う
                    $this->tpl_mainpage = 'products/select_item.tpl';
                    break;

                case 'cart':
                    // この段階でエラーが出る場合は、数量の入力エラーのはず
                    if (count($this->arrErr)) {
                        // 数量の入力を行う
                        $this->tpl_mainpage = 'products/select_item.tpl';
                    }
                    break;

                default:
                    $this->tpl_mainpage = 'products/detail.tpl';
                    break;
            }
        }

        // 商品詳細を取得
        $this->arrProduct = $objProduct->getDetail($product_id);

        // サブタイトルを取得
        $this->tpl_subtitle = $this->arrProduct['name'];

        // 関連カテゴリを取得
        $this->arrRelativeCat = SC_Helper_DB_Ex::sfGetMultiCatTree($product_id);

        // 商品ステータスを取得
        $this->productStatus = $objProduct->getProductStatus($product_id);

        // 画像ファイル指定がない場合の置換処理
        $this->arrProduct['main_image']
            = SC_Utils_Ex::sfNoImageMain($this->arrProduct['main_image']);

        $this->subImageFlag = $this->lfSetFile($this->objUpFile,$this->arrProduct,$this->arrFile);
        //レビュー情報の取得
        $this->arrReview = $this->lfGetReviewData($product_id);

        //関連商品情報表示
        $this->arrRecommend = $this->lfPreGetRecommendProducts($product_id);

        // ログイン判定
        if ($objCustomer->isLoginSuccess() === true) {
            //お気に入りボタン表示
            $this->tpl_login = true;
            $this->is_favorite = SC_Helper_DB_Ex::sfDataExists('dtb_customer_favorite_products', 'customer_id = ? AND product_id = ?', array($objCustomer->getValue('customer_id'), $product_id));
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

    /* プロダクトIDの正当性チェック */
    function lfCheckProductId($admin_mode,$product_id) {
        // 管理機能からの確認の場合は、非公開の商品も表示する。
        if (isset($admin_mode) && $admin_mode == 'on') {
            SC_Utils_Ex::sfIsSuccess(new SC_Session_Ex());
            $status = true;
            $where = 'del_flg = 0';
        } else {
            $status = false;
            $where = 'del_flg = 0 AND status = 1';
        }

        if (!SC_Utils_Ex::sfIsInt($product_id)
            || SC_Utils_Ex::sfIsZeroFilling($product_id)
            || !SC_Helper_DB_Ex::sfIsRecord('dtb_products', 'product_id', (array)$product_id, $where)
        ) {
                SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
        }
        return $product_id;
    }

    /* ファイル情報の初期化 */
    function lfInitFile($objUpFile) {
        $objUpFile->addFile('詳細-メイン画像', 'main_image', array('jpg'), IMAGE_SIZE);
        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $objUpFile->addFile("詳細-サブ画像$cnt", "sub_image$cnt", array('jpg'), IMAGE_SIZE);
        }
        return $objUpFile;
    }

    /* 規格選択セレクトボックスの作成 */
    function lfMakeSelect() {
        return 'fnSetClassCategories('
            . 'document.form1, '
            . SC_Utils_Ex::jsonEncode($this->objFormParam->getValue('classcategory_id2'))
            . '); ';
    }

    /* 規格選択セレクトボックスの作成(モバイル) */
    function lfMakeSelectMobile(&$objPage, $product_id,$request_classcategory_id1) {

        $classcat_find1 = false;
        $classcat_find2 = false;

        // 規格名一覧
        $arrClassName = SC_Helper_DB_Ex::sfGetIDValueList('dtb_class', 'class_id', 'name');
        // 規格分類名一覧
        $arrClassCatName = SC_Helper_DB_Ex::sfGetIDValueList('dtb_classcategory', 'classcategory_id', 'name');
        // 商品規格情報の取得
        $arrProductsClass = $this->lfGetProductsClass($product_id);

        // 規格1クラス名の取得
        $objPage->tpl_class_name1 = $arrClassName[$arrProductsClass[0]['class_id1']];
        // 規格2クラス名の取得
        $objPage->tpl_class_name2 = $arrClassName[$arrProductsClass[0]['class_id2']];

        // すべての組み合わせ数
        $count = count($arrProductsClass);

        $classcat_id1 = '';

        $arrSele1 = array();
        $arrSele2 = array();

        for ($i = 0; $i < $count; $i++) {
            // 在庫のチェック
            if ($arrProductsClass[$i]['stock'] <= 0 && $arrProductsClass[$i]['stock_unlimited'] != '1') {
                continue;
            }

            // 規格1のセレクトボックス用
            if ($classcat_id1 != $arrProductsClass[$i]['classcategory_id1']) {
                $classcat_id1 = $arrProductsClass[$i]['classcategory_id1'];
                $arrSele1[$classcat_id1] = $arrClassCatName[$classcat_id1];
            }

            // 規格2のセレクトボックス用
            if ($arrProductsClass[$i]['classcategory_id1'] == $request_classcategory_id1 and $classcat_id2 != $arrProductsClass[$i]['classcategory_id2']) {
                $classcat_id2 = $arrProductsClass[$i]['classcategory_id2'];
                $arrSele2[$classcat_id2] = $arrClassCatName[$classcat_id2];
            }
        }

        // 規格1
        $objPage->arrClassCat1 = $arrSele1;
        $objPage->arrClassCat2 = $arrSele2;

        // 規格1が設定されている
        if (isset($arrProductsClass[0]['classcategory_id1']) && $arrProductsClass[0]['classcategory_id1'] != '0') {
            $classcat_find1 = true;
        }

        // 規格2が設定されている
        if (isset($arrProductsClass[0]['classcategory_id2']) && $arrProductsClass[0]['classcategory_id2'] != '0') {
            $classcat_find2 = true;
        }

        $objPage->tpl_classcat_find1 = $classcat_find1;
        $objPage->tpl_classcat_find2 = $classcat_find2;
    }

    /* パラメーター情報の初期化 */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('規格1', 'classcategory_id1', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('規格2', 'classcategory_id2', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('数量', 'quantity', INT_LEN, 'n', array('EXIST_CHECK', 'ZERO_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('管理者ログイン', 'admin', INT_LEN, 'a', array('ALNUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品ID', 'product_id', INT_LEN, 'n', array('EXIST_CHECK', 'ZERO_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お気に入り商品ID', 'favorite_product_id', INT_LEN, 'n', array('ZERO_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品規格ID', 'product_class_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        // 値の取得
        $objFormParam->setParam($_REQUEST);
        // 入力値の変換
        $objFormParam->convParam();
        // 入力情報を渡す
        return $objFormParam->getFormParamList();
    }

    /* 商品規格情報の取得 */
    function lfGetProductsClass($product_id) {
        $objProduct = new SC_Product_Ex();
        return $objProduct->getProductsClassFullByProductId($product_id);
    }

    /* 登録済み関連商品の読み込み */
    function lfPreGetRecommendProducts($product_id) {
        $objProduct = new SC_Product_Ex();
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $objQuery->setOrder('rank DESC');
        $arrRecommendData = $objQuery->select('recommend_product_id, comment', 'dtb_recommend_products as t1 left join dtb_products as t2 on t1.recommend_product_id = t2.product_id', 't1.product_id = ? and t2.del_flg = 0 and t2.status = 1', array($product_id));

        $arrRecommendProductId = array();
        foreach ($arrRecommendData as $recommend) {
            $arrRecommendProductId[] = $recommend['recommend_product_id'];
        }

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrProducts = $objProduct->getListByProductIds($objQuery, $arrRecommendProductId);

        $arrRecommend = array();
        foreach ($arrRecommendData as $key => $arrRow) {
            $arrRecommendData[$key] = array_merge($arrRow, $arrProducts[$arrRow['recommend_product_id']]);
        }

        return $arrRecommendData;
    }

    /* 入力内容のチェック */
    function lfCheckError($mode,&$objFormParam,$tpl_classcat_find1 = null ,$tpl_classcat_find2 = null) {

        switch ($mode) {
        case 'add_favorite_sphone':
        case 'add_favorite':
            $objCustomer = new SC_Customer_Ex();
            $objErr = new SC_CheckError_Ex();
            $customer_id = $objCustomer->getValue('customer_id');
            if (SC_Helper_DB_Ex::sfDataExists('dtb_customer_favorite_products', 'customer_id = ? AND product_id = ?', array($customer_id, $favorite_product_id))) {
                $objErr->arrErr['add_favorite'.$favorite_product_id] = '※ この商品は既にお気に入りに追加されています。<br />';
            }
            break;
        default:
            // 入力データを渡す。
            $arrRet =  $objFormParam->getHashArray();
            $objErr = new SC_CheckError_Ex($arrRet);
            $objErr->arrErr = $objFormParam->checkError();

            // 複数項目チェック
            if ($tpl_classcat_find1) {
                $objErr->doFunc(array('規格1', 'classcategory_id1'), array('EXIST_CHECK'));
            }
            if ($tpl_classcat_find2) {
                $objErr->doFunc(array('規格2', 'classcategory_id2'), array('EXIST_CHECK'));
            }
            break;
        }

        return $objErr->arrErr;
    }

    //商品ごとのレビュー情報を取得する
    function lfGetReviewData($id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        //商品ごとのレビュー情報を取得する
        $col = 'create_date, reviewer_url, reviewer_name, recommend_level, title, comment';
        $from = 'dtb_review';
        $where = 'del_flg = 0 AND status = 1 AND product_id = ?';
        $objQuery->setOrder('create_date DESC');
        $objQuery->setLimit(REVIEW_REGIST_MAX);
        $arrWhereVal = array($id);
        $arrReview = $objQuery->select($col, $from, $where, $arrWhereVal);
        return $arrReview;
    }

    /*
     * ファイルの情報をセットする
     * @return $subImageFlag
     */
    function lfSetFile($objUpFile,$arrProduct,&$arrFile) {
        // DBからのデータを引き継ぐ
        $objUpFile->setDBFileList($arrProduct);
        // ファイル表示用配列を渡す
        $arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URLPATH, IMAGE_SAVE_URLPATH, true);

        // サブ画像の有無を判定
        $subImageFlag = false;
        for ($i = 1; $i <= PRODUCTSUB_MAX; $i++) {
            if ($arrFile['sub_image' . $i]['filepath'] != '') {
                $subImageFlag = true;
            }
        }
        return $subImageFlag;
    }

    /*
     * お気に入り商品登録
     * @return void
     */
    function lfRegistFavoriteProduct($favorite_product_id,$customer_id) {
        // ログイン中のユーザが商品をお気に入りにいれる処理
        if (!SC_Helper_DB_Ex::sfIsRecord('dtb_products', 'product_id', $favorite_product_id, 'del_flg = 0 AND status = 1')) {
            SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
            return false;
        } else {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $exists = $objQuery->exists('dtb_customer_favorite_products', 'customer_id = ? AND product_id = ?', array($customer_id, $favorite_product_id));

            if (!$exists) {
                $sqlval['customer_id'] = $customer_id;
                $sqlval['product_id'] = $favorite_product_id;
                $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
                $sqlval['create_date'] = 'CURRENT_TIMESTAMP';

                $objQuery->begin();
                $objQuery->insert('dtb_customer_favorite_products', $sqlval);
                $objQuery->commit();
            }
            // お気に入りに登録したことを示すフラグ
            $this->just_added_favorite = true;
            return true;
        }
    }
}
