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
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 商品登録(商品規格)のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_ProductClass extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

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
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrProductType = $masterData->getMasterData("mtb_product_type");
        // 規格プルダウンのリスト
        $this->arrClass = $this->getAllClass();
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
        // 商品マスタの検索条件パラメータを初期化
        $objFormParam = new SC_FormParam_Ex();
        $this->initParam($objFormParam);

        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        $this->arrSearchHidden = $objFormParam->getSearchArray();

        // Downファイル管理クラスを初期化
        $this->objDownFile = new SC_UploadFile_Ex(DOWN_TEMP_REALDIR, DOWN_SAVE_REALDIR);
        $this->initDownFile($count, $this->objDownFile);

        switch ($this->getMode()) {

        // 編集実行
        case 'edit':

            $this->arrErr = $this->lfCheckProductsClass($objFormParam);

            // エラーの無い場合は確認画面を表示
            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                $this->tpl_mainpage = 'products/product_class_confirm.tpl';
                $this->doDisp($objFormParam);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
            }
            // エラーが発生した場合
            else {
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();

                /* TODO
                // Hiddenからのデータを引き継ぐ
                $this->objDownFile->setHiddenFileList($_POST);
                // HIDDEN用に配列を渡す。
                $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objDownFile->getHiddenFileList());
                // Form用に配列を渡す。
                $this->arrForm = array_merge((array)$this->arrForm, (array)$this->objDownFile->getFormKikakuDownFile());
                */
            }
            break;

        // 削除
        case 'delete':
            $this->doDelete($objFormParam->getValue('product_id'));
            break;

        // 初期表示
        case 'pre_edit':
            $this->doPreEdit($objFormParam);

            /* TODO
            // HIDDEN用に配列を渡す。
            $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objDownFile->getHiddenFileList());
            */
            break;

        // 「表示する」ボタン押下時
        case 'disp':
            $this->arrErr = $this->lfCheckSelectClass();
            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                $this->doDisp($objFormParam);
            }
            break;

        // ダウンロード商品ファイルアップロード
        case 'upload_down':

            /* TODO
            $product_id = $objFormParam->getValue('product_id');
            $down_key   = $objFormParam->getValue('down_key');
            // 編集画面用パラメータをセット
            $this->doPreEdit($product_id, true);
            // Hiddenからのデータを引き継ぐ
            $this->objDownFile->setHiddenKikakuFileList($_POST);
            // ファイル存在チェック
            $this->arrErr = array_merge((array)$this->arrErr, (array)$this->objDownFile->checkEXISTS($down_key));
            // ファイル保存処理
            $this->arrErr[$down_key] = $this->objDownFile->makeTempDownFile($down_key);
            // HIDDEN用に配列を渡す。
            $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objDownFile->getHiddenFileList());
            // Form用に配列を渡す。
            $this->arrForm = array_merge((array)$this->arrForm, (array)$this->objDownFile->getFormKikakuDownFile());
            // 規格の組み合わせ一覧を表示
            $this->doDisp($product_id,
                          $objFormParam->getValue('select_class_id1'),
                          $objFormParam->getValue('select_class_id2'), $objFormParam);
            */
            break;

        // ダウンロードファイルの削除
        case 'delete_down':

            /* TODO
            $product_id = $objFormParam->getValue('product_id');
            $down_key   = $objFormParam->getValue('down_key');
            // 編集画面用パラメータをセット
            $this->doPreEdit($product_id, true);
            // Hiddenからのデータを引き継ぐ
            $this->objDownFile->setHiddenKikakuFileList($_POST);
            // ファイル削除処理
            $this->objDownFile->deleteKikakuFile($down_key);
            // HIDDEN用に配列を渡す。
            $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objDownFile->getHiddenFileList());
            // Form用に配列を渡す。
            $this->arrForm = array_merge((array)$this->arrForm, (array)$this->objDownFile->getFormKikakuDownFile());
            // 規格の組み合わせ一覧を表示
            $this->doDisp($product_id,
                          $objFormParam->getValue('select_class_id1'),
                          $objFormParam->getValue('select_class_id2'), $objFormParam);
            */
            break;

        // 確認画面からの戻り
        case 'confirm_return':
            $this->doPreEdit($objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();

            /* TODO
            // Hiddenからのデータを引き継ぐ
            $this->objDownFile->setHiddenFileList($_POST);
            // HIDDEN用に配列を渡す。
            $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objDownFile->getHiddenFileList());
            // Form用に配列を渡す。
            $this->arrForm = array_merge((array)$this->arrForm, (array)$this->objDownFile->getFormKikakuDownFile());
            */

            break;
        case 'complete':
            $this->tpl_mainpage = 'products/product_class_complete.tpl';
            // TODO $this->objDownFile->setHiddenFileList($_POST);

            $this->registerProductClass($objFormParam->getHashArray(), $objFormParam->getValue('product_id'),
                                        $objFormParam->getValue('total'));
            // TODO
            // 一時ファイルを本番ディレクトリに移動する
            // $this->objDownFile->moveTempDownFile();
            break;

        default:
        }

        // 登録対象の商品名を取得
        $objFormParam->setValue('product_name',
                $this->getProductName($objFormParam->getValue('product_id')));
        $this->arrForm = $objFormParam->getFormParamList();
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
     * パラメータ初期化
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function initParam(&$objFormParam) {
        // 商品マスタ検索パラメータ引き継ぎ
        $objFormParam->addParam("商品ID", "product_id", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("カテゴリID", "category_id", STEXT_LEN, 'n', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("ページ送り番号","search_pageno", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("表示件数", "search_page_max", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("商品ID", "search_product_id", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("商品コード", "search_product_code", STEXT_LEN, 'KVna', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("商品名", "search_name", STEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("カテゴリ", "search_category_id", STEXT_LEN, 'n', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("種別", "search_status", INT_LEN, 'n', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("開始年", "search_startyear", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("開始月", "search_startmonth", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("開始日", "search_startday", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("終了年", "search_endyear", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("終了月", "search_endmonth", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("終了日", "search_endday", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("ステータス", "search_product_flag", INT_LEN, 'n', array("MAX_LENGTH_CHECK"));

        // 規格プルダウン
        $objFormParam->addParam("規格1", "select_class_id1", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("規格2", "select_class_id2", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("規格1", "class_id1", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("規格2", "class_id2", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        // 商品規格
        $objFormParam->addParam("商品規格数", "total", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("商品名", "product_name", STEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("商品コード", "product_code", STEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("規格ID1", "classcategory_id1", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("規格ID2", "classcategory_id2", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("規格名1", "classcategory_name1", STEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("規格名2", "classcategory_name2", STEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("商品規格ID", "product_class_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("在庫数", "stock", AMOUNT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("在庫数", "stock_unlimited", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam(NORMAL_PRICE_TITLE, "price01", PRICE_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam(SALE_PRICE_TITLE, "price02", PRICE_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("商品種別", "product_type_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("削除フラグ", "del_flg", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("DLファイル名", "down_filename", STEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("DLファイル名", "down_realfilename", STEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("チェックボックス", "check", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("ファイルアップロード用キー", "down_key", STEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK"));
    }

    /**
     * Downファイル管理クラスを初期化
     *
     * @param 行数 $count
     * @param SC_FormParam $objDownFile
     */
    function initDownFile($count, &$objDownFile) {
        $i = 1;
        for ($i = 1; $i < $count; $i++) {
            $objDownFile->addFile("ダウンロード販売用ファイル", 'down_realfilename'. ":" . $i, explode(",", DOWNLOAD_EXTENSION), DOWN_SIZE, true, 0, 0);
        }
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
    function registerProductClass($arrList, $product_id, $total) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objDb = new SC_Helper_DB_Ex();

        $objQuery->begin();

        $arrProductsClass = $objQuery->select("*", "dtb_products_class", "product_id = ?", array($product_id));
        $arrExists = array();
        foreach ($arrProductsClass as $val) {
            $arrExists[$val['product_class_id']] = $val;
        }

        // デフォルト値として設定する値を取得しておく
        $arrDefault = $this->getProductsClass($product_id);

        for ($i = 0; $i < $total; $i++) {
            $del_flg = SC_Utils_Ex::isBlank($arrList['check'][$i]) ? 1 : 0;
            $stock_unlimited = SC_Utils_Ex::isBlank($arrList['stock_unlimited'][$i]) ? 0 : 1;

            // dtb_products_class 登録/更新用
            $registerKeys = array('product_code', 'stock',
                                  'price01', 'price02', 'product_type_id',
                                  'down_filename', 'down_realfilename');

            $arrPC = array();
            foreach ($registerKeys as $key) {
                $arrPC[$key] = $arrList[$key][$i];
            }
            $arrPC['product_id'] = $product_id;
            $arrPC['sale_limit'] = $arrDefault['sale_limit'];
            $arrPC['deliv_fee'] = $arrDefault['deliv_fee'];
            $arrPC['point_rate'] = $arrDefault['point_rate'];
            $arrPC['stock_unlimited'] = $stock_unlimited;

            // 該当関数が無いため, セッションの値を直接代入
            $arrPC['creator_id'] = $_SESSION['member_id'];
            $arrPC['update_date'] = 'now()';
            $arrPC['del_flg'] = $del_flg;

            // 登録 or 更新
            $is_update = false;
            if (!SC_Utils_Ex::isBlank($arrList['product_class_id'][$i])) {
                $is_update = true;

                // 更新の場合は規格組み合わせを検索し, 削除しておく
                $class_combination_id = $arrExists[$arrList['product_class_id'][$i]]['class_combination_id'];
                $existsCombi = $objQuery->getRow(
                    '*',
                    'dtb_class_combination',
                    'class_combination_id = ?',
                    array($class_combination_id));

                $objQuery->delete('dtb_class_combination',
                                  'class_combination_id IN (?, ?)',
                                  array($existsCombi['class_combination_id'],
                                        $existsCombi['parent_class_combination_id']));
            }

            // 規格組み合わせを登録
            $arrComb1['class_combination_id'] = $objQuery->nextVal('dtb_class_combination_class_combination_id');
            $arrComb1['classcategory_id'] = $arrList['classcategory_id1'][$i];
            $arrComb1['level'] = 1;
            $objQuery->insert('dtb_class_combination', $arrComb1);

            // 規格2も登録する場合
            if (!SC_Utils_Ex::isBlank($arrList['classcategory_id2'][$i])) {
                $arrComb2['class_combination_id'] = $objQuery->nextVal('dtb_class_combination_class_combination_id');
                $arrComb2['classcategory_id'] = $arrList['classcategory_id2'][$i];
                $arrComb2['parent_class_combination_id'] = $arrComb1['class_combination_id'];
                $arrComb2['level'] = 2;
                $objQuery->insert('dtb_class_combination', $arrComb2);

                $arrPC['class_combination_id'] = $arrComb2['class_combination_id'];
            } else {
                $arrPC['class_combination_id'] = $arrComb1['class_combination_id'];
            }

            // 更新
            if ($is_update) {
                $arrPC['product_class_id'] = $arrList['product_class_id'][$i];
                $objQuery->update("dtb_products_class", $arrPC,
                                  "product_class_id = ?",
                                  array($arrPC['product_class_id']));
            }
            // 新規登録
            else {
                $arrPC['create_date'] = "now()";
                $arrPC['product_class_id'] = $objQuery->nextVal('dtb_products_class_product_class_id');
                $objQuery->insert("dtb_products_class", $arrPC);
            }
        }

        // 規格無し用の商品規格を非表示に
        $arrBlank['del_flg'] = 1;
        $arrBlank['update_date'] = 'now()';
        $objQuery->update("dtb_products_class", $arrBlank,
                          "product_id = ? AND class_combination_id IS NULL",
                          array($product_id));

        // 件数カウントバッチ実行
        $objDb->sfCountCategory($objQuery);
        $objQuery->commit();
    }

    /**
     * 規格選択エラーチェックを行う
     *
     * ※SC_FormParamで対応していないエラーチェックのため, SC_CheckErrorを使用している.
     *
     * @return array エラーの配列
     */
    function lfCheckSelectClass() {
        $objErr = new SC_CheckError_Ex();
        $objErr->doFunc(array("規格1", "select_class_id1"), array("EXIST_CHECK"));
        $objErr->doFunc(array("規格", "select_class_id1", "select_class_id2"), array("TOP_EXIST_CHECK"));
        $objErr->doFunc(array("規格1", "規格2", "select_class_id1", "select_class_id2"), array("DIFFERENT_CHECK"));
        return $objErr->arrErr;
    }

    /**
     * 商品規格エラーチェック.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラー結果の配列
     */
    function lfCheckProductsClass(&$objFormParam) {
        $arrValues = $objFormParam->getHashArray();
        $arrErr = $objFormParam->checkError();
        $total = $objFormParam->getValue('total');

        if (SC_Utils_Ex::isBlank($arrValues['check'])) {
            $arrErr['check_empty'] = '※ 商品種別が選択されていません。<br />';
        }

        for ($i = 0; $i < $total; $i++) {

            /*
             * チェックボックスの入っている項目のみ, 必須チェックを行う.
             * エラーを配列で返す必要があるため, SC_CheckError を使用しない.
             */
            if (!SC_Utils_Ex::isBlank($arrValues['check'][$i])) {

                /*
                 * 販売価格の必須チェック
                 */
                if (SC_Utils_Ex::isBlank($arrValues['price02'][$i])) {
                    $arrErr['price02'][$i] = '※ ' . SALE_PRICE_TITLE . 'が入力されていません。<br />';
                }
                /*
                 * 在庫数の必須チェック
                 */
                if ((SC_Utils_Ex::isBlank($arrValues['stock_unlimited'][$i])
                     || $arrValues['stock_unlimited'][$i] != 1)

                    && SC_Utils_Ex::isBlank($arrValues['stock'][$i])) {
                    $arrErr['stock'][$i] = '※ 在庫数が入力されていません。<br />';
                }
                /*
                 * 商品種別の必須チェック
                 */
                if (SC_Utils_Ex::isBlank($arrValues['product_type_id'][$i])) {
                    $arrErr['product_type_id'][$i] = "※ 商品種別は、いずれかを選択してください。<br />";
                }
                /*
                 * ダウンロード商品の必須チェック
                 */
                if($arrValues['product_type_id'][$i] == PRODUCT_TYPE_DOWNLOAD) {
                    if (SC_Utils_Ex::isBlank($arrValues['down_filename'][$i])) {
                        $arrErr['down_filename'][$i] = "※ ダウンロード商品の場合はダウンロードファイル名を入力してください。<br />";
                    }
                    if (SC_Utils_Ex::isBlank($arrValues['down_realfilename'][$i])) {
                        $arrErr['down_realfilename'][$i] = "※ ダウンロード商品の場合はダウンロード商品用ファイルをアップロードしてください。<br />";
                    }
                }
                /*
                 * 通常商品チェック
                 */
                else if ($arrValues['product_type_id'][$i] == PRODUCT_TYPE_NORMAL) {
                    if (!SC_Utils_Ex::isBlank($arrValues['down_filename'][$i])) {
                        $arrErr['down_filename'] = "※ 通常商品の場合はダウンロードファイル名を設定できません。<br />";
                    }
                    if (!SC_Utils_Ex::isBlank($arrValues['down_realfilename'][$i])) {
                        $arrErr['down_realfilename'][$i] = "※ 実商品の場合はダウンロード商品用ファイルをアップロードできません。<br />ファイルを取り消してください。<br />";
                    }
                }
            }
        }
        return $arrErr;
    }

    /**
     * 規格の組み合わせ一覧を表示する.
     *
     * 規格1, 規格2における規格分類のすべての組み合わせを取得し,
     * 該当商品の商品規格の内容を取得後, フォームに設定する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function doDisp(&$objFormParam) {
        $product_id = $objFormParam->getValue('product_id');
        $select_class_id1 = $objFormParam->getValue('select_class_id1');
        $select_class_id2 = $objFormParam->getValue('select_class_id2');

        // すべての組み合わせを取得し, フォームに設定
        $arrClassCat = $this->getAllClassCategory($select_class_id1, $select_class_id2);
        $total = count($arrClassCat);
        $objFormParam->setValue('total', $total);
        $objFormParam->setParam(SC_Utils_Ex::sfSwapArray($arrClassCat));

        // class_id1, class_id2 は select_class_id1 で上書き
        $objFormParam->setValue('class_id1', $select_class_id1);
        $objFormParam->setValue('class_id2', $select_class_id2);

        // 商品情報を取得し, フォームに設定
        $arrProductsClass = $this->getProductsClass($product_id);

        foreach ($arrProductsClass as $key => $val) {
            // 組み合わせ数分の値の配列を生成する
            $arrValues = array();
            for ($i = 0; $i < $total; $i++) {
                $arrValues[] = $val;
            }
            $objFormParam->setValue($key, $arrValues);
        }
    }

    /**
     * 規格編集画面を表示する
     *
     * @param integer $product_id 商品ID
     * @param bool $existsValue
     * @param bool $usepostValue
     */
    function doPreEdit(&$objFormParam) {
        $product_id = $objFormParam->getValue('product_id');
        $objProduct = new SC_Product_Ex();
        $existsProductsClass = $objProduct->getProductsClassFullByProductId($product_id, true);

        $class_id1 = $existsProductsClass[0]['class_id1'];
        $class_id2 = $existsProductsClass[0]['class_id2'];
        $objFormParam->setValue('class_id1', $class_id1);
        $objFormParam->setValue('class_id2', $class_id2);
        $objFormParam->setValue('select_class_id1', $class_id1);
        $objFormParam->setValue('select_class_id2', $class_id2);
        $this->doDisp($objFormParam);

        // 登録済みのデータで上書き
        $objFormParam->setParam(SC_Utils_Ex::sfSwapArray($existsProductsClass));

        // $existsProductsClass で product_id が配列になってしまうため数値で上書き
        $objFormParam->setValue('product_id', $product_id);

        // check を設定
        $arrChecks = array();
        $index = 0;
        foreach ($objFormParam->getValue('del_flg') as $key => $val) {
            if ($val == 0) {
                $arrChecks[$index] = 1;
            }
            $index++;
        }
        $objFormParam->setValue('check', $arrChecks);

        // class_id1, class_id2 を上書き
        $objFormParam->setValue('class_id1', $class_id1);
        $objFormParam->setValue('class_id2', $class_id2);

        // DBデータからダウンロードファイル名の読込
        $this->objDownFile->setDBFileList($this->arrForm);
        // PostデータからダウンロードTempファイル名の読込
        $this->objDownFile->setPostFileList($_POST, $this->arrForm);
    }

    /**
     * 規格の削除を実行する
     *
     * @param $product_id
     * @return void
     */
    function doDelete($product_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $objQuery->begin();
        $objQuery->update("dtb_products_class", array('del_flg' => 0),
                          "product_id = ? AND class_combination_id IS NULL", array($product_id));
        $objQuery->update("dtb_products_class", array('del_flg' => 1),
                          "product_id = ? AND class_combination_id IS NOT NULL", array($product_id));
        $objQuery->commit();

        // 在庫無し商品の非表示対応
        if (NOSTOCK_HIDDEN === true) {
            // 件数カウントバッチ実行
            $objDb = new SC_Helper_DB_Ex();
            $objDb->sfCountCategory($objQuery);
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
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $col = <<< __EOF__
            T1.class_id AS class_id1,
            T1.classcategory_id AS classcategory_id1,
            T1.name AS classcategory_name1,
            T1.rank AS rank1
__EOF__;
        $table = '';
        $arrParams = array();
        if(SC_Utils_Ex::isBlank($class_id2)) {
            $table = "dtb_classcategory T1 ";
            $objQuery->setWhere("T1.class_id = ?");
            $objQuery->setOrder("T1.rank DESC");
            $arrParams = array($class_id1);
        } else {
            $col .= <<< __EOF__
                ,
                T2.class_id AS class_id2,
                T2.classcategory_id AS classcategory_id2,
                T2.name AS classcategory_name2,
                T2.rank AS rank2
__EOF__;
            $table = "dtb_classcategory AS T1, dtb_classcategory AS T2";
            $objQuery->setWhere("T1.class_id = ? AND T2.class_id = ?");
            $objQuery->setOrder("T1.rank DESC, T2.rank DESC");
            $arrParams = array($class_id1, $class_id2);
        }
        return $objQuery->select($col, $table, "", $arrParams);
    }

    /**
     * 商品名を取得する.
     *
     * @access private
     * @param integer $product_id 商品ID
     * @return string 商品名の文字列
     */
    function getProductName($product_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->get('name', 'dtb_products', 'product_id = ?', array($product_id));
    }

    /**
     * 規格分類の登録された, すべての規格を取得する.
     *
     * @access private
     * @return array 規格分類の登録された, すべての規格
     */
    function getAllClass() {
        $arrClass = SC_Helper_DB_Ex::sfGetIDValueList("dtb_class", 'class_id', 'name');

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
     * 商品IDをキーにして, 商品規格の初期値を取得する.
     *
     * 商品IDをキーにし, デフォルトに設定されている商品規格を取得する.
     *
     * @param integer $product_id 商品ID
     * @return array 商品規格の配列
     */
    function getProductsClass($product_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = "product_code, price01, price02, stock, stock_unlimited, sale_limit, deliv_fee, point_rate";
        return $objQuery->getRow($col, "dtb_products_class", "product_id = ? AND class_combination_id IS NULL", array($product_id));
    }
}
