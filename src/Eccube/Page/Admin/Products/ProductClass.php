<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Page\Admin\Products;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\FormParam;
use Eccube\Framework\Image;
use Eccube\Framework\Product;
use Eccube\Framework\Query;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\TaxRuleHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

/**
 * 商品登録(商品規格)のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class ProductClass extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'products/product_class.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'product';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = '商品登録(商品規格)';
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrProductType = $masterData->getMasterData('mtb_product_type');
        // 規格プルダウンのリスト
        $this->arrClass = $this->getAllClass();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        // 商品マスターの検索条件パラメーターを初期化
        $objFormParam = Application::alias('eccube.form_param');
        $this->initParam($objFormParam);

        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        $this->arrSearchHidden = $objFormParam->getSearchArray();

        switch ($this->getMode()) {
            // 編集実行
            case 'edit':
                $this->arrErr = $this->lfCheckProductsClass($objFormParam);

                // エラーの無い場合は確認画面を表示
                if (Utils::isBlank($this->arrErr)) {
                    $this->tpl_mainpage = 'products/product_class_confirm.tpl';
                    $this->doDisp($objFormParam);
                    $this->fillCheckboxesValue('stock_unlimited', $_POST['total']);
                    $objFormParam->setParam($_POST);
                    $objFormParam->convParam();
                // エラーが発生した場合
                } else {
                    $objFormParam->setParam($_POST);
                    $objFormParam->convParam();
                }
                break;

            // 削除
            case 'delete':
                $this->doDelete($objFormParam->getValue('product_id'));
                $objFormParam->setValue('class_id1', '');
                $objFormParam->setValue('class_id2', '');
                $this->doDisp($objFormParam);
                break;

            // 初期表示
            case 'pre_edit':
                $this->doPreEdit($objFormParam);
                break;

            // 「表示する」ボタン押下時
            case 'disp':
                $this->arrErr = $this->lfCheckSelectClass();
                if (Utils::isBlank($this->arrErr)) {
                    $this->doDisp($objFormParam);
                    $this->initDispParam($objFormParam);
                }
                break;

            // ダウンロード商品ファイルアップロード
            case 'file_upload':
                $this->doFileUpload($objFormParam);
                break;

            // ダウンロードファイルの削除
            case 'file_delete':
                $this->doFileDelete($objFormParam);
                break;

            // 確認画面からの戻り
            case 'confirm_return':
                $this->doPreEdit($objFormParam);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                break;

            case 'complete':
                $this->tpl_mainpage = 'products/product_class_complete.tpl';
                $this->doUploadComplete($objFormParam);
                $this->registerProductClass($objFormParam->getHashArray(),
                                            $objFormParam->getValue('product_id'),
                                            $objFormParam->getValue('total'));
                break;

            default:
                break;
        }

        // 登録対象の商品名を取得
        $objFormParam->setValue('product_name',
                $this->getProductName($objFormParam->getValue('product_id')));
        $this->arrForm = $objFormParam->getFormParamList();
    }

    /**
     * パラメーター初期化
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function initParam(&$objFormParam)
    {
        // 商品マスター検索パラメーター引き継ぎ
        $objFormParam->addParam('商品ID', 'product_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('カテゴリID', 'category_id', STEXT_LEN, 'n', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ページ送り番号', 'search_pageno', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('表示件数', 'search_page_max', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('商品ID', 'search_product_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品コード', 'search_product_code', STEXT_LEN, 'KVna', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品名', 'search_name', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('カテゴリ', 'search_category_id', STEXT_LEN, 'n', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('種別', 'search_status', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('開始年', 'search_startyear', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('開始月', 'search_startmonth', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('開始日', 'search_startday', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了年', 'search_endyear', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了月', 'search_endmonth', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了日', 'search_endday', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('商品ステータス', 'search_product_statuses', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));

        // 規格プルダウン
        $objFormParam->addParam('規格1', 'class_id1', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('規格2', 'class_id2', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));

        // 商品規格
        $objFormParam->addParam('商品規格数', 'total', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('商品名', 'product_name', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品コード', 'product_code', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('規格ID1', 'classcategory_id1', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('規格ID2', 'classcategory_id2', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('規格名1', 'classcategory_name1', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('規格名2', 'classcategory_name2', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品規格ID', 'product_class_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('在庫数', 'stock', AMOUNT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('在庫数', 'stock_unlimited', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(NORMAL_PRICE_TITLE, 'price01', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SALE_PRICE_TITLE, 'price02', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        if (OPTION_PRODUCT_TAX_RULE) {
            $objFormParam->addParam('消費税率', 'tax_rate', PERCENTAGE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        }
        $objFormParam->addParam('商品種別', 'product_type_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('削除フラグ', 'del_flg', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('ダウンロード販売用ファイル名', 'down_filename', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ダウンロード販売用ファイル名', 'down_realfilename', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('チェックボックス', 'check', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('ファイルアップロード用キー', 'upload_index', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
    }

    /**
     * 規格の登録または更新を行う.
     *
     * @param array   $arrList    入力フォームの内容
     * @param integer $product_id 登録を行う商品ID
     */
    public function registerProductClass($arrList, $product_id, $total)
    {
        $objQuery = Application::alias('eccube.query');
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');

        $objQuery->begin();

        $arrProductsClass = $objQuery->select('*', 'dtb_products_class', 'product_id = ?', array($product_id));
        $arrExists = array();
        foreach ($arrProductsClass as $val) {
            $arrExists[$val['product_class_id']] = $val;
        }

        // デフォルト値として設定する値を取得しておく
        $arrDefault = $this->getProductsClass($product_id);

        $objQuery->delete('dtb_products_class', 'product_id = ? AND (classcategory_id1 <> 0 OR classcategory_id2 <> 0)', array($product_id));

        for ($i = 0; $i < $total; $i++) {
            $del_flg = Utils::isBlank($arrList['check'][$i]) ? 1 : 0;
            $price02 = Utils::isBlank($arrList['price02'][$i]) ? 0 : $arrList['price02'][$i];
            // dtb_products_class 登録/更新用
            $registerKeys = array(
                'classcategory_id1', 'classcategory_id2',
                'product_code', 'stock', 'price01', 'product_type_id',
                'down_filename', 'down_realfilename',
            );

            $arrPC = array();
            foreach ($registerKeys as $key) {
                $arrPC[$key] = $arrList[$key][$i];
            }
            $arrPC['product_id'] = $product_id;
            $arrPC['sale_limit'] = $arrDefault['sale_limit'];
            $arrPC['deliv_fee'] = $arrDefault['deliv_fee'];
            $arrPC['point_rate'] = $arrDefault['point_rate'];
            if ($arrList['stock_unlimited'][$i] == 1) {
                $arrPC['stock_unlimited'] = 1;
                $arrPC['stock'] = null;
            } else {
                $arrPC['stock_unlimited'] = 0;
            }
            $arrPC['price02'] = $price02;

            // 該当関数が無いため, セッションの値を直接代入
            $arrPC['creator_id'] = $_SESSION['member_id'];
            $arrPC['update_date'] = 'CURRENT_TIMESTAMP';
            $arrPC['del_flg'] = $del_flg;

            $arrPC['create_date'] = 'CURRENT_TIMESTAMP';
            // 更新の場合は, product_class_id を使い回す
            if (!Utils::isBlank($arrList['product_class_id'][$i])) {
                $arrPC['product_class_id'] = $arrList['product_class_id'][$i];
            } else {
                $arrPC['product_class_id'] = $objQuery->nextVal('dtb_products_class_product_class_id');
            }

            /*
             * チェックを入れない商品は product_type_id が NULL になるので, 0 を入れる
             */
            $arrPC['product_type_id'] = Utils::isBlank($arrPC['product_type_id']) ? 0 : $arrPC['product_type_id'];

            $objQuery->insert('dtb_products_class', $arrPC);

            // 税情報登録/更新
            if (OPTION_PRODUCT_TAX_RULE) {
                TaxRuleHelper::setTaxRuleForProduct($arrList['tax_rate'][$i], $arrPC['product_id'], $arrPC['product_class_id']);
            }
        }

        // 規格無し用の商品規格を非表示に
        $arrBlank['del_flg'] = 1;
        $arrBlank['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery->update('dtb_products_class', $arrBlank,
                          'product_id = ? AND classcategory_id1 = 0 AND classcategory_id2 = 0',
                          array($product_id));

        // 件数カウントバッチ実行
        $objDb->countCategory($objQuery);
        $objQuery->commit();
    }

    /**
     * 規格選択エラーチェックを行う
     *
     * ※FormParamで対応していないエラーチェックのため, CheckErrorを使用している.
     *
     * @return array エラーの配列
     */
    public function lfCheckSelectClass()
    {
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error');
        $objErr->doFunc(array('規格1', 'class_id1'), array('EXIST_CHECK'));
        $objErr->doFunc(array('規格', 'class_id1', 'select_class_id2'), array('TOP_EXIST_CHECK'));
        $objErr->doFunc(array('規格1', '規格2', 'class_id1', 'class_id2'), array('DIFFERENT_CHECK'));

        return $objErr->arrErr;
    }

    /**
     * 商品規格エラーチェック.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return array        エラー結果の配列
     */
    public function lfCheckProductsClass(&$objFormParam)
    {
        $arrValues = $objFormParam->getHashArray();
        $arrErr = $objFormParam->checkError();
        $total = $objFormParam->getValue('total');

        if (Utils::isBlank($arrValues['check'])) {
            $arrErr['check_empty'] = '※ 規格が選択されていません。<br />';
        }

        for ($i = 0; $i < $total; $i++) {
            /*
             * チェックボックスの入っている項目のみ, 必須チェックを行う.
             * エラーを配列で返す必要があるため, CheckError を使用しない.
             */
            if (!Utils::isBlank($arrValues['check'][$i])) {
                /*
                 * 販売価格の必須チェック
                 */
                if (Utils::isBlank($arrValues['price02'][$i])) {
                    $arrErr['price02'][$i] = '※ ' . SALE_PRICE_TITLE . 'が入力されていません。<br />';
                }
                /*
                 * 在庫数の必須チェック
                 */
                if ((Utils::isBlank($arrValues['stock_unlimited'][$i]) || $arrValues['stock_unlimited'][$i] != 1)
                    && Utils::isBlank($arrValues['stock'][$i])
                ) {
                    $arrErr['stock'][$i] = '※ 在庫数が入力されていません。<br />';
                }
                /*
                 * 消費税率の必須チェック
                 */
                if (OPTION_PRODUCT_TAX_RULE && Utils::isBlank($arrValues['tax_rate'][$i])) {
                    $arrErr['tax_rate'][$i] = '※ 消費税率が入力されていません。<br />';
                }
                /*
                 * 商品種別の必須チェック
                 */
                if (Utils::isBlank($arrValues['product_type_id'][$i])) {
                    $arrErr['product_type_id'][$i] = '※ 商品種別は、いずれかを選択してください。<br />';
                }
                /*
                 * ダウンロード商品の必須チェック
                 */
                if ($arrValues['product_type_id'][$i] == PRODUCT_TYPE_DOWNLOAD) {
                    if (Utils::isBlank($arrValues['down_filename'][$i])) {
                        $arrErr['down_filename'][$i] = '※ ダウンロード商品の場合はダウンロードファイル名を入力してください。<br />';
                    }
                    if (Utils::isBlank($arrValues['down_realfilename'][$i])) {
                        $arrErr['down_realfilename'][$i] = '※ ダウンロード商品の場合はダウンロード商品用ファイルをアップロードしてください。<br />';
                    }
                /*
                 * 通常商品チェック
                 */
                } elseif ($arrValues['product_type_id'][$i] != PRODUCT_TYPE_DOWNLOAD) {
                    if (!Utils::isBlank($arrValues['down_filename'][$i])) {
                        $arrErr['down_filename'][$i] = '※ ダウンロード商品ではない場合、ダウンロードファイル名を設定できません。<br />';
                    }
                    if (!Utils::isBlank($arrValues['down_realfilename'][$i])) {
                        $arrErr['down_realfilename'][$i] = '※ ダウンロード商品ではない場合、ダウンロード商品用ファイルをアップロードできません。<br />ファイルを取り消してください。<br />';
                    }
                }
            }
        }

        return $arrErr;
    }

    /**
     * 規格の組み合わせ一覧を表示する.
     *
     * 規格1, 規格2における規格分類の全ての組み合わせを取得し,
     * 該当商品の商品規格の内容を取得後, フォームに設定する.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function doDisp(&$objFormParam)
    {
        $product_id = $objFormParam->getValue('product_id');
        $class_id1 = $objFormParam->getValue('class_id1');
        $class_id2 = $objFormParam->getValue('class_id2');

        // 全ての組み合わせを取得し, フォームに設定
        $arrClassCat = $this->getAllClassCategory($class_id1, $class_id2);
        $total = count($arrClassCat);
        $objFormParam->setValue('total', $total);
        $objFormParam->setParam(Utils::sfSwapArray($arrClassCat));

        // class_id1, class_id2 を, 入力値で上書き
        $objFormParam->setValue('class_id1', $class_id1);
        $objFormParam->setValue('class_id2', $class_id2);

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
        // 商品種別を 1 に初期化
        $objFormParam->setValue('product_type_id', array_pad(array(), $total, 1));
    }

    /**
     * 「表示する」ボタンをクリックされたときのパラメーター初期化処理
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function initDispParam(&$objFormParam)
    {
        // 登録チェックボックス初期化(全てチェックを外す)
        $objFormParam->setValue('check', '');

        // 規格2が選択されていない場合、規格2名称初期化
        $class_id2 = $objFormParam->getValue('class_id2');
        if (Utils::isBlank($class_id2) == true) {
            $objFormParam->setValue('classcategory_name2', '');
        }
    }

    /**
     * 規格編集画面を表示する
     *
     * @param FormParam $objFormParam
     */
    public function doPreEdit(&$objFormParam)
    {
        $product_id = $objFormParam->getValue('product_id');
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
        $existsProductsClass = $objProduct->getProductsClassFullByProductId($product_id);

        // 規格のデフォルト値(全ての組み合わせ)を取得し, フォームに反映
        $class_id1 = $existsProductsClass[0]['class_id1'];
        $class_id2 = $existsProductsClass[0]['class_id2'];
        $objFormParam->setValue('class_id1', $class_id1);
        $objFormParam->setValue('class_id2', $class_id2);
        $this->doDisp($objFormParam);

        /*
         * 登録済みのデータで, フォームの値を上書きする.
         *
         * 登録済みデータと, フォームの値は, 配列の形式が違うため,
         * 同じ形式の配列を生成し, マージしてフォームの値を上書きする
         */
        $arrKeys = array('classcategory_id1', 'classcategory_id2', 'product_code',
            'classcategory_name1', 'classcategory_name2', 'stock',
            'stock_unlimited', 'price01', 'price02',
            'product_type_id', 'down_filename', 'down_realfilename', 'upload_index', 'tax_rate'
        );
        $arrFormValues = $objFormParam->getSwapArray($arrKeys);
        // フォームの規格1, 規格2をキーにした配列を生成
        $arrClassCatKey = array();
        foreach ($arrFormValues as $formValue) {
            $arrClassCatKey[$formValue['classcategory_id1']][$formValue['classcategory_id2']] = $formValue;
        }
        // 登録済みデータをマージ
        foreach ($existsProductsClass as $existsValue) {
            $arrClassCatKey[$existsValue['classcategory_id1']][$existsValue['classcategory_id2']] = $existsValue;
        }

        // 規格のデフォルト値に del_flg をつけてマージ後の1次元配列を生成
        $arrMergeProductsClass = array();
        foreach ($arrClassCatKey as $arrC1) {
            foreach ($arrC1 as $arrValues) {
                $arrValues['del_flg'] = (string) $arrValues['del_flg'];
                if (Utils::isBlank($arrValues['del_flg'])
                    || $arrValues['del_flg'] === '1') {
                    $arrValues['del_flg'] = '1';
                } else {
                    $arrValues['del_flg'] = '0';
                }

                // 消費税率を設定
                if (OPTION_PRODUCT_TAX_RULE) {
                    $arrRet = TaxRuleHelper::getTaxRule($arrValues['product_id'], $arrValues['product_class_id']);
                    $arrValues['tax_rate'] = $arrRet['tax_rate'];
                }

                $arrMergeProductsClass[] = $arrValues;
            }
        }

        // 登録済みのデータで上書き
        $objFormParam->setParam(Utils::sfSwapArray($arrMergeProductsClass));

        // $arrMergeProductsClass で product_id が配列になってしまうため数値で上書き
        $objFormParam->setValue('product_id', $product_id);

        // check を設定
        $arrChecks = array();
        $index = 0;
        foreach ($objFormParam->getValue('del_flg') as $key => $val) {
            if ($val === '0') {
                $arrChecks[$index] = 1;
            }
            $index++;
        }
        $objFormParam->setValue('check', $arrChecks);

        // class_id1, class_id2 を取得値で上書き
        $objFormParam->setValue('class_id1', $class_id1);
        $objFormParam->setValue('class_id2', $class_id2);
    }

    /**
     * 規格の削除を実行する
     *
     * @param $product_id
     * @return void
     */
    public function doDelete($product_id)
    {
        $objQuery = Application::alias('eccube.query');

        $objQuery->begin();

        // 商品規格なしデータの復元
        $where = 'product_id = ? AND classcategory_id1 = 0 AND classcategory_id2 = 0';
        $objQuery->update('dtb_products_class', array('del_flg' => 0), $where, array($product_id));

        // 商品規格データの削除
        $where = 'product_id = ? AND (classcategory_id1 <> 0 OR classcategory_id2 <> 0)';
        $objQuery->delete('dtb_products_class', $where, array($product_id));

        $objQuery->commit();

        // 在庫無し商品の非表示対応
        if (NOSTOCK_HIDDEN) {
            // 件数カウントバッチ実行
            /* @var $objDb DbHelper */
            $objDb = Application::alias('eccube.helper.db');
            $objDb->countCategory($objQuery);
        }
    }

    /**
     * ファイルアップロードを行う.
     *
     * 以下のチェックを行い, ファイルを一時領域へアップロードする.
     * 1. ファイルサイズチェック
     * 2. 拡張子チェック
     *
     * TODO
     * CheckError クラスや, UploadFile クラスが多次元配列に対応して
     * いないため, 独自のロジックを使用している.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function doFileUpload(&$objFormParam)
    {
        $index   = $objFormParam->getValue('upload_index');
        $arrDownRealFiles = $objFormParam->getValue('down_realfilename');

        if ($_FILES['down_realfilename']['size'][$index] <= 0) {
            $this->arrErr['down_realfilename'][$index] = '※ ファイルがアップロードされていません';
        } elseif ($_FILES['down_realfilename']['size'][$index] > DOWN_SIZE *  1024) {
            $size = DOWN_SIZE;
            $byte = 'KB';
            if ($size >= 1000) {
                $size = $size / 1000;
                $byte = 'MB';
            }
            $this->arrErr['down_realfilename'][$index] = '※ ダウンロード販売用ファイル名のファイルサイズは' . $size . $byte . '以下のものを使用してください。<br />';
        } else {
            // CheckError::FILE_EXT_CHECK とのソース互換を強めるための配列
            $value = array(
                0 => 'ダウンロード販売用ファイル名',
                1 => 'down_realfilename',
                2 => explode(',', DOWNLOAD_EXTENSION),
            );
            // ▼CheckError::FILE_EXT_CHECK から移植
            $match = false;
            if (strlen($_FILES[$value[1]]['name'][$index]) >= 1) {
                $filename = $_FILES[$value[1]]['name'][$index];

                foreach ($value[2] as $check_ext) {
                    $match = preg_match('/' . preg_quote('.' . $check_ext) . '$/i', $filename) >= 1;
                    if ($match === true) {
                        break 1;
                    }
                }
            }

            if ($match === false) {
                $str_ext = implode('・', $value[2]);
                $this->arrErr[$value[1]][$index] = '※ ' . $value[0] . 'で許可されている形式は、' . $str_ext . 'です。<br />';
            // ▲CheckError::FILE_EXT_CHECK から移植
            } else {
                $uniqname = date('mdHi') . '_' . uniqid('').'.';
                $temp_file = preg_replace("/^.*\./", $uniqname, $_FILES['down_realfilename']['name'][$index]);

                if (move_uploaded_file($_FILES['down_realfilename']['tmp_name'][$index], DOWN_TEMP_REALDIR . $temp_file)) {
                    $arrDownRealFiles[$index] = $temp_file;
                    $objFormParam->setValue('down_realfilename', $arrDownRealFiles);
                    GcUtils::gfPrintLog($_FILES['down_realfilename']['name'][$index] .' -> '. realpath(DOWN_TEMP_REALDIR . $temp_file));
                } else {
                    $objErr->arrErr[$keyname] = '※ ファイルのアップロードに失敗しました。<br />';
                    GcUtils::gfPrintLog('File Upload Error!: ' . $_FILES['down_realfilename']['name'][$index] . ' -> ' . DOWN_TEMP_REALDIR . $temp_file);
                }
            }
        }
    }

    /**
     * アップロードしたファイルを削除する.
     *
     * TODO 一時ファイルの削除
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function doFileDelete(&$objFormParam)
    {
        $objImage = new Image(DOWN_TEMP_REALDIR);
        $arrRealFileName = $objFormParam->getValue('down_realfilename');
        $index = $objFormParam->getValue('upload_index');
        $objImage->deleteImage($arrRealFileName[$index], DOWN_SAVE_REALDIR);
        $arrRealFileName[$index] = '';
        $objFormParam->setValue('down_realfilename', $arrRealFileName);
    }

    /**
     * アップロードした一時ファイルを保存する.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function doUploadComplete(&$objFormParam)
    {
        $objImage = new Image(DOWN_TEMP_REALDIR);
        $arrRealFileName = $objFormParam->getValue('down_realfilename');
        if (is_array($arrRealFileName)) {
            foreach ($arrRealFileName as $real_file_name) {
                $objImage->moveTempImage($real_file_name, DOWN_SAVE_REALDIR);
            }
        }
    }

    /**
     * 規格ID1, 規格ID2の規格分類全てを取得する.
     *
     * @param  integer $class_id1 規格ID1
     * @param  integer $class_id2 規格ID2
     * @return array   規格と規格分類の配列
     */
    public function getAllClassCategory($class_id1, $class_id2 = null)
    {
        $objQuery = Application::alias('eccube.query');

        $col = <<< __EOF__
            T1.class_id AS class_id1,
            T1.classcategory_id AS classcategory_id1,
            T1.name AS classcategory_name1,
            T1.rank AS rank1
__EOF__;
        $table = '';
        $arrParams = array();
        if (Utils::isBlank($class_id2)) {
            $col .= ',0 AS classcategory_id2';
            $table = 'dtb_classcategory T1 ';
            $objQuery->setWhere('T1.class_id = ?');
            $objQuery->setOrder('T1.rank DESC');
            $arrParams = array($class_id1);
        } else {
            $col .= <<< __EOF__
                ,
                T2.class_id AS class_id2,
                T2.classcategory_id AS classcategory_id2,
                T2.name AS classcategory_name2,
                T2.rank AS rank2
__EOF__;
            $table = 'dtb_classcategory AS T1, dtb_classcategory AS T2';
            $objQuery->setWhere('T1.class_id = ? AND T2.class_id = ?');
            $objQuery->setOrder('T1.rank DESC, T2.rank DESC');
            $arrParams = array($class_id1, $class_id2);
        }

        return $objQuery->select($col, $table, '', $arrParams);
    }

    /**
     * 商品名を取得する.
     *
     * @access private
     * @param  integer $product_id 商品ID
     * @return string  商品名の文字列
     */
    public function getProductName($product_id)
    {
        $objQuery = Application::alias('eccube.query');

        return $objQuery->get('name', 'dtb_products', 'product_id = ?', array($product_id));
    }

    /**
     * 規格分類の登録された, 全ての規格を取得する.
     *
     * @access private
     * @return array 規格分類の登録された, 全ての規格
     */
    public function getAllClass()
    {
        $arrClass = Application::alias('eccube.helper.db')->getIDValueList('dtb_class', 'class_id', 'name');

        // 規格分類が登録されていない規格は表示しないようにする。
        $arrClassCatCount = Utils::sfGetClassCatCount();

        $results = array();
        if (!Utils::isBlank($arrClass)) {
            foreach ($arrClass as $key => $val) {
                if ($arrClassCatCount[$key] > 0) {
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
     * @param  integer $product_id 商品ID
     * @return array   商品規格の配列
     */
    public function getProductsClass($product_id)
    {
        $objQuery = Application::alias('eccube.query');
        $col = 'product_code, price01, price02, stock, stock_unlimited, sale_limit, deliv_fee, point_rate';
        $where = 'product_id = ? AND classcategory_id1 = 0 AND classcategory_id2 = 0';

        return $objQuery->getRow($col, 'dtb_products_class', $where, array($product_id));
    }

    /**
     * チェックボックスの値を埋める.
     *
     * チェックボックスが, 全て空で submit されると, $_POST の値が全く渡らない
     * ため, FormParam::getValue() で取得できない.
     * これを防ぐため, $_POST[$key] を直接操作し, 指定の長さで空白の配列を作成する
     *
     * @param  string  $key  $_POST のキー
     * @param  integer $size 作成する配列のサイズ
     * @return void
     */
    public function fillCheckboxesValue($key, $size)
    {
        if (empty($_POST[$key])) {
            $_POST[$key] = array_pad(array(), $size, '');
        }
    }
}
