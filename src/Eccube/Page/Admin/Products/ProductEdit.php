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
use Eccube\Framework\CheckError;
use Eccube\Framework\FormParam;
use Eccube\Framework\Image;
use Eccube\Framework\Product;
use Eccube\Framework\Query;
use Eccube\Framework\UploadFile;
use Eccube\Framework\DB\DBFactory;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\MakerHelper;
use Eccube\Framework\Helper\TaxRuleHelper;
use Eccube\Framework\Util\Utils;

/**
 * 商品登録 のページクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class ProductEdit extends Index
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'products/product.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'product';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = '商品登録';

        $masterData = Application::alias('eccube.db.master_data');
        $this->arrProductType = $masterData->getMasterData('mtb_product_type');
        $this->arrDISP = $masterData->getMasterData('mtb_disp');
        $this->arrSTATUS = $masterData->getMasterData('mtb_status');
        $this->arrSTATUS_IMAGE = $masterData->getMasterData('mtb_status_image');
        $this->arrDELIVERYDATE = $masterData->getMasterData('mtb_delivery_date');
        $this->arrMaker = Application::alias('eccube.helper.maker')->getIDValueList();
        $this->arrAllowedTag = $masterData->getMasterData('mtb_allowed_tag');
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
        $objFormParam = Application::alias('eccube.form_param');

        // アップロードファイル情報の初期化
        $objUpFile = new UploadFile(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        $this->lfInitFile($objUpFile);
        $objUpFile->setHiddenFileList($_POST);

        // ダウンロード販売ファイル情報の初期化
        $objDownFile = new UploadFile(DOWN_TEMP_REALDIR, DOWN_SAVE_REALDIR);
        $this->lfInitDownFile($objDownFile);
        $objDownFile->setHiddenFileList($_POST);

        // 検索パラメーター引き継ぎ
        $this->arrSearchHidden = $this->lfGetSearchParam($_POST);

        $mode = $this->getMode();
        switch ($mode) {
            case 'pre_edit':
            case 'copy' :
                // パラメーター初期化(商品ID)
                $this->lfInitFormParam_PreEdit($objFormParam, $_POST);
                // エラーチェック
                $this->arrErr = $objFormParam->checkError();
                if (count($this->arrErr) > 0) {
                    trigger_error('', E_USER_ERROR);
                }

                // 商品ID取得
                $product_id = $objFormParam->getValue('product_id');
                // 商品データ取得
                $arrForm = $this->lfGetFormParam_PreEdit($objUpFile, $objDownFile, $product_id);

                // 複製の場合は、ダウンロード商品情報部分はコピーしない
                if ($mode == 'copy') {
                    // ダウンロード商品ファイル名をunset
                    $arrForm['down_filename'] = '';

                    // $objDownFile->setDBDownFile()でsetされたダウンロードファイル名をunset
                    unset($objDownFile->save_file[0]);
                }

                // ページ表示用パラメーター設定
                $this->arrForm = $this->lfSetViewParam_InputPage($objUpFile, $objDownFile, $arrForm);

                // 商品複製の場合、画像ファイルコピー
                if ($mode == 'copy') {
                    $this->arrForm['copy_product_id'] = $this->arrForm['product_id'];
                    $this->arrForm['product_id'] = '';
                    // 画像ファイルのコピー
                    $this->lfCopyProductImageFiles($objUpFile);
                }

                // ページonload時のJavaScript設定
                $this->tpl_onload = $this->lfSetOnloadJavaScript_InputPage();
                break;

            case 'edit':
                // パラメーター初期化, 取得
                $this->lfInitFormParam($objFormParam, $_POST);
                $arrForm = $objFormParam->getHashArray();
                // エラーチェック
                $this->arrErr = $this->lfCheckError_Edit($objFormParam, $objUpFile, $objDownFile, $arrForm);
                if (count($this->arrErr) == 0) {
                    // 確認画面表示設定
                    $this->tpl_mainpage = 'products/confirm.tpl';
                    $this->arrCatList = $this->lfGetCategoryList_Edit();
                    $this->arrForm = $this->lfSetViewParam_ConfirmPage($objUpFile, $objDownFile, $arrForm);
                } else {
                    // 入力画面表示設定
                    $this->arrForm = $this->lfSetViewParam_InputPage($objUpFile, $objDownFile, $arrForm);
                    // ページonload時のJavaScript設定
                    $this->tpl_onload = $this->lfSetOnloadJavaScript_InputPage();
                }
                break;

            case 'complete':
                // パラメーター初期化, 取得
                $this->lfInitFormParam($objFormParam, $_POST);
                $arrForm = $this->lfGetFormParam_Complete($objFormParam);
                // エラーチェック
                $this->arrErr = $this->lfCheckError_Edit($objFormParam, $objUpFile, $objDownFile, $arrForm);
                if (count($this->arrErr) == 0) {
                    // DBへデータ登録
                    $product_id = $this->lfRegistProduct($objUpFile, $objDownFile, $arrForm);

                    // 件数カウントバッチ実行
                    $objQuery = Application::alias('eccube.query');
                    /* @var $objDb DbHelper */
                    $objDb = Application::alias('eccube.helper.db');
                    $objDb->countCategory($objQuery);
                    $objDb->countMaker($objQuery);

                    // ダウンロード商品の複製時に、ダウンロード商品用ファイルを
                    // 変更すると、複製元のファイルが削除されるのを回避。
                    if (!empty($arrForm['copy_product_id'])) {
                        $objDownFile->save_file = array();
                    }

                    // 一時ファイルを本番ディレクトリに移動する
                    $this->lfSaveUploadFiles($objUpFile, $objDownFile, $product_id);

                    $this->tpl_mainpage = 'products/complete.tpl';
                    $this->arrForm['product_id'] = $product_id;
                } else {
                    // 入力画面表示設定
                    $this->arrForm = $this->lfSetViewParam_InputPage($objUpFile, $objDownFile, $arrForm);
                    // ページonload時のJavaScript設定
                    $this->tpl_onload = $this->lfSetOnloadJavaScript_InputPage();
                }
                break;

            // 画像のアップロード
            case 'upload_image':
            case 'delete_image':
                // パラメーター初期化
                $this->lfInitFormParam_UploadImage($objFormParam);
                $this->lfInitFormParam($objFormParam, $_POST);
                $arrForm = $objFormParam->getHashArray();

                switch ($mode) {
                    case 'upload_image':
                        // ファイルを一時ディレクトリにアップロード
                        $this->arrErr[$arrForm['image_key']] = $objUpFile->makeTempFile($arrForm['image_key'], IMAGE_RENAME);
                        if ($this->arrErr[$arrForm['image_key']] == '') {
                            // 縮小画像作成
                            $this->lfSetScaleImage($objUpFile, $arrForm['image_key']);
                        }
                        break;
                    case 'delete_image':
                        // ファイル削除
                        $this->lfDeleteTempFile($objUpFile, $arrForm['image_key']);
                        break;
                    default:
                        break;
                }

                // 入力画面表示設定
                $this->arrForm = $this->lfSetViewParam_InputPage($objUpFile, $objDownFile, $arrForm);
                // ページonload時のJavaScript設定
                $anchor_hash = $this->getAnchorHash($arrForm['image_key']);
                $this->tpl_onload = $this->lfSetOnloadJavaScript_InputPage($anchor_hash);
                break;

            // ダウンロード商品ファイルアップロード
            case 'upload_down':
            case 'delete_down':
                // パラメーター初期化
                $this->lfInitFormParam_UploadDown($objFormParam);
                $this->lfInitFormParam($objFormParam, $_POST);
                $arrForm = $objFormParam->getHashArray();

                switch ($mode) {
                    case 'upload_down':
                        // ファイルを一時ディレクトリにアップロード
                        $this->arrErr[$arrForm['down_key']] = $objDownFile->makeTempDownFile();
                        break;
                    case 'delete_down':
                        // ファイル削除
                        $objDownFile->deleteFile($arrForm['down_key']);
                        break;
                    default:
                        break;
                }

                // 入力画面表示設定
                $this->arrForm = $this->lfSetViewParam_InputPage($objUpFile, $objDownFile, $arrForm);
                // ページonload時のJavaScript設定
                $anchor_hash = $this->getAnchorHash($arrForm['down_key']);
                $this->tpl_onload = $this->lfSetOnloadJavaScript_InputPage($anchor_hash);
                break;

            // 関連商品選択
            case 'recommend_select' :
                // パラメーター初期化
                $this->lfInitFormParam_RecommendSelect($objFormParam);
                $this->lfInitFormParam($objFormParam, $_POST);
                $arrForm = $objFormParam->getHashArray();
                // 入力画面表示設定
                $this->arrForm = $this->lfSetViewParam_InputPage($objUpFile, $objDownFile, $arrForm);

                // 選択された関連商品IDがすでに登録している関連商品と重複していないかチェック
                $this->lfCheckError_RecommendSelect($this->arrForm, $this->arrErr);

                // ページonload時のJavaScript設定
                $anchor_hash = $this->getAnchorHash($this->arrForm['anchor_key']);
                $this->tpl_onload = $this->lfSetOnloadJavaScript_InputPage($anchor_hash);
                break;

            // 確認ページからの戻り
            case 'confirm_return':
                // パラメーター初期化
                $this->lfInitFormParam($objFormParam, $_POST);
                $arrForm = $objFormParam->getHashArray();
                // 入力画面表示設定
                $this->arrForm = $this->lfSetViewParam_InputPage($objUpFile, $objDownFile, $arrForm);
                // ページonload時のJavaScript設定
                $this->tpl_onload = $this->lfSetOnloadJavaScript_InputPage();
                break;

            default:
                // 入力画面表示設定
                $arrForm = array();
                $this->arrForm = $this->lfSetViewParam_InputPage($objUpFile, $objDownFile, $arrForm);
                // ページonload時のJavaScript設定
                $this->tpl_onload = $this->lfSetOnloadJavaScript_InputPage();
                break;
        }

        // 関連商品の読み込み
        $this->arrRecommend = $this->lfGetRecommendProducts($this->arrForm);
    }

    /**
     * パラメーター情報の初期化
     * - 編集/複製モード
     *
     * @param  FormParam $objFormParam FormParamインスタンス
     * @param  array  $arrPost      $_POSTデータ
     * @return void
     */
    public function lfInitFormParam_PreEdit(&$objFormParam, $arrPost)
    {
        $objFormParam->addParam('商品ID', 'product_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->setParam($arrPost);
        $objFormParam->convParam();
    }

    /**
     * パラメーター情報の初期化
     *
     * @param  FormParam $objFormParam FormParamインスタンス
     * @param  array  $arrPost      $_POSTデータ
     * @return void
     */
    public function lfInitFormParam(&$objFormParam, $arrPost)
    {
        $objFormParam->addParam('商品ID', 'product_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品名', 'name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品カテゴリ', 'category_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('公開・非公開', 'status', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品ステータス', 'product_status', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

        if (!$arrPost['has_product_class']) {
            // 新規登録, 規格なし商品の編集の場合
            $objFormParam->addParam('商品種別', 'product_type_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
            $objFormParam->addParam('ダウンロード商品ファイル名', 'down_filename', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
            $objFormParam->addParam('ダウンロード商品実ファイル名', 'down_realfilename', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
            $objFormParam->addParam('temp_down_file', 'temp_down_file', '', '', array());
            $objFormParam->addParam('save_down_file', 'save_down_file', '', '', array());
            $objFormParam->addParam('商品コード', 'product_code', STEXT_LEN, 'KVna', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
            $objFormParam->addParam(NORMAL_PRICE_TITLE, 'price01', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
            $objFormParam->addParam(SALE_PRICE_TITLE, 'price02', PRICE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
            if (OPTION_PRODUCT_TAX_RULE) {
                $objFormParam->addParam('消費税率', 'tax_rate', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
            }
            $objFormParam->addParam('在庫数', 'stock', AMOUNT_LEN, 'n', array('SPTAB_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
            $objFormParam->addParam('在庫無制限', 'stock_unlimited', INT_LEN, 'n', array('SPTAB_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        }
        $objFormParam->addParam('商品送料', 'deliv_fee', PRICE_LEN, 'n', array('NUM_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ポイント付与率', 'point_rate', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('発送日目安', 'deliv_date_id', INT_LEN, 'n', array('NUM_CHECK'));
        $objFormParam->addParam('販売制限数', 'sale_limit', AMOUNT_LEN, 'n', array('SPTAB_CHECK', 'ZERO_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('メーカー', 'maker_id', INT_LEN, 'n', array('NUM_CHECK'));
        $objFormParam->addParam('メーカーURL', 'comment1', URL_LEN, 'a', array('SPTAB_CHECK', 'URL_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('検索ワード', 'comment3', LLTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('備考欄(SHOP専用)', 'note', LLTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('一覧-メインコメント', 'main_list_comment', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('詳細-メインコメント', 'main_comment', LLTEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('save_main_list_image', 'save_main_list_image', '', '', array());
        $objFormParam->addParam('save_main_image', 'save_main_image', '', '', array());
        $objFormParam->addParam('save_main_large_image', 'save_main_large_image', '', '', array());
        $objFormParam->addParam('temp_main_list_image', 'temp_main_list_image', '', '', array());
        $objFormParam->addParam('temp_main_image', 'temp_main_image', '', '', array());
        $objFormParam->addParam('temp_main_large_image', 'temp_main_large_image', '', '', array());

        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $objFormParam->addParam('詳細-サブタイトル' . $cnt, 'sub_title' . $cnt, STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
            $objFormParam->addParam('詳細-サブコメント' . $cnt, 'sub_comment' . $cnt, LLTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
            $objFormParam->addParam('save_sub_image' . $cnt, 'save_sub_image' . $cnt, '', '', array());
            $objFormParam->addParam('save_sub_large_image' . $cnt, 'save_sub_large_image' . $cnt, '', '', array());
            $objFormParam->addParam('temp_sub_image' . $cnt, 'temp_sub_image' . $cnt, '', '', array());
            $objFormParam->addParam('temp_sub_large_image' . $cnt, 'temp_sub_large_image' . $cnt, '', '', array());
        }

        for ($cnt = 1; $cnt <= RECOMMEND_PRODUCT_MAX; $cnt++) {
            $objFormParam->addParam('関連商品コメント' . $cnt, 'recommend_comment' . $cnt, LTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
            $objFormParam->addParam('関連商品ID' . $cnt, 'recommend_id' . $cnt, INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
            $objFormParam->addParam('recommend_delete' . $cnt, 'recommend_delete' . $cnt, '', 'n', array());
        }

        $objFormParam->addParam('商品ID', 'copy_product_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

        $objFormParam->addParam('has_product_class', 'has_product_class', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('product_class_id', 'product_class_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

        $objFormParam->setParam($arrPost);
        $objFormParam->convParam();
    }

    /**
     * パラメーター情報の初期化
     * - 画像ファイルアップロードモード
     *
     * @param  FormParam $objFormParam FormParamインスタンス
     * @return void
     */
    public function lfInitFormParam_UploadImage(&$objFormParam)
    {
        $objFormParam->addParam('image_key', 'image_key', '', '', array());
    }

    /**
     * パラメーター情報の初期化
     * - ダウンロード商品ファイルアップロードモード
     *
     * @param  FormParam $objFormParam FormParamインスタンス
     * @return void
     */
    public function lfInitFormParam_UploadDown(&$objFormParam)
    {
        $objFormParam->addParam('down_key', 'down_key', '', '', array());
    }

    /**
     * パラメーター情報の初期化
     * - 関連商品追加モード
     *
     * @param  FormParam $objFormParam FormParamインスタンス
     * @return void
     */
    public function lfInitFormParam_RecommendSelect(&$objFormParam)
    {
        $objFormParam->addParam('anchor_key', 'anchor_key', '', '', array());
        $objFormParam->addParam('select_recommend_no', 'select_recommend_no', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * アップロードファイルパラメーター情報の初期化
     * - 画像ファイル用
     *
     * @param  UploadFile $objUpFile UploadFileインスタンス
     * @return void
     */
    public function lfInitFile(&$objUpFile)
    {
        $objUpFile->addFile('一覧-メイン画像', 'main_list_image', array('jpg', 'gif', 'png'), IMAGE_SIZE, false, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
        $objUpFile->addFile('詳細-メイン画像', 'main_image', array('jpg', 'gif', 'png'), IMAGE_SIZE, false, NORMAL_IMAGE_WIDTH, NORMAL_IMAGE_HEIGHT);
        $objUpFile->addFile('詳細-メイン拡大画像', 'main_large_image', array('jpg', 'gif', 'png'), IMAGE_SIZE, false, LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT);
        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $objUpFile->addFile("詳細-サブ画像$cnt", "sub_image$cnt", array('jpg', 'gif', 'png'), IMAGE_SIZE, false, NORMAL_SUBIMAGE_WIDTH, NORMAL_SUBIMAGE_HEIGHT);
            $objUpFile->addFile("詳細-サブ拡大画像$cnt", "sub_large_image$cnt", array('jpg', 'gif', 'png'), IMAGE_SIZE, false, LARGE_SUBIMAGE_WIDTH, LARGE_SUBIMAGE_HEIGHT);
        }
    }

    /**
     * アップロードファイルパラメーター情報の初期化
     * - ダウンロード商品ファイル用
     *
     * @param  UploadFile $objDownFile UploadFileインスタンス
     * @return void
     */
    public function lfInitDownFile(&$objDownFile)
    {
        $objDownFile->addFile('ダウンロード販売用ファイル', 'down_file', explode(',', DOWNLOAD_EXTENSION), DOWN_SIZE, true, 0, 0);
    }

    /**
     * フォーム入力パラメーターのエラーチェック
     *
     * @param  object $objFormParam FormParamインスタンス
     * @param  UploadFile $objUpFile    UploadFileインスタンス
     * @param  UploadFile $objDownFile  UploadFileインスタンス
     * @param  array  $arrForm      フォーム入力パラメーター配列
     * @return array  エラー情報を格納した連想配列
     */
    public function lfCheckError_Edit(&$objFormParam, &$objUpFile, &$objDownFile, $arrForm)
    {
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $arrForm);
        $arrErr = array();

        // 入力パラメーターチェック
        $arrErr = $objFormParam->checkError();

        // アップロードファイル必須チェック
        $arrErr = array_merge((array) $arrErr, (array) $objUpFile->checkExists());

        // HTMLタグ許可チェック
        $objErr->doFunc(array('詳細-メインコメント', 'main_comment', $this->arrAllowedTag), array('HTML_TAG_CHECK'));
        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $objErr->doFunc(array('詳細-サブコメント' . $cnt, 'sub_comment' . $cnt, $this->arrAllowedTag), array('HTML_TAG_CHECK'));
        }

        // 規格情報がない商品の場合のチェック
        if ($arrForm['has_product_class'] != true) {
            // 在庫必須チェック(在庫無制限ではない場合)
            if ($arrForm['stock_unlimited'] != UNLIMITED_FLG_UNLIMITED) {
                $objErr->doFunc(array('在庫数', 'stock'), array('EXIST_CHECK'));
            }
            // ダウンロード商品ファイル必須チェック(ダウンロード商品の場合)
            if ($arrForm['product_type_id'] == PRODUCT_TYPE_DOWNLOAD) {
                $arrErr = array_merge((array) $arrErr, (array) $objDownFile->checkExists());
                $objErr->doFunc(array('ダウンロード商品ファイル名', 'down_filename'), array('EXIST_CHECK'));
            }
        }

        $arrErr = array_merge((array) $arrErr, (array) $objErr->arrErr);

        return $arrErr;
    }

    /**
     * 関連商品の重複登録チェック、エラーチェック
     *
     * 関連商品の重複があった場合はエラーメッセージを格納し、該当の商品IDをリセットする
     *
     * @param  array $arrForm 入力値の配列
     * @param  array $arrErr  エラーメッセージの配列
     * @return void
     */
    public function lfCheckError_RecommendSelect(&$arrForm, &$arrErr)
    {
        $select_recommend_no = $arrForm['select_recommend_no'];
        $select_recommend_id = $arrForm['recommend_id' . $select_recommend_no];

        foreach ($arrForm as $key => $value) {
            if (preg_match('/^recommend_id/', $key)) {
                if ($select_recommend_no == preg_replace('/^recommend_id/', '', $key)) {
                    continue;
                }
                $delete_key = 'recommend_delete'.intval(str_replace('recommend_id', '', $key));
                if ($select_recommend_id == $arrForm[$key] && $arrForm[$delete_key] != 1) {
                    // 重複した場合、選択されたデータをリセットする
                    $arrForm['recommend_id' . $select_recommend_no] = '';
                    $arrErr['recommend_comment' . $select_recommend_no] = '※ すでに登録されている関連商品です。<br />';
                    break;
                }
            }
        }
    }

    /**
     * 検索パラメーター引き継ぎ用配列取得
     *
     * @param  array $arrPost $_POSTデータ
     * @return array 検索パラメーター配列
     */
    public function lfGetSearchParam($arrPost)
    {
        $arrSearchParam = array();
        $objFormParam = Application::alias('eccube.form_param');

        parent::lfInitParam($objFormParam);
        $objFormParam->setParam($arrPost);
        $arrSearchParam = $objFormParam->getSearchArray();

        return $arrSearchParam;
    }

    /**
     * フォームパラメーター取得
     * - 編集/複製モード
     *
     * @param  UploadFile  $objUpFile   UploadFileインスタンス
     * @param  UploadFile  $objDownFile UploadFileインスタンス
     * @param  integer $product_id  商品ID
     * @return array   フォームパラメーター配列
     */
    public function lfGetFormParam_PreEdit(&$objUpFile, &$objDownFile, $product_id)
    {
        $arrForm = array();

        // DBから商品データ取得
        $arrForm = $this->lfGetProductData_FromDB($product_id);
        // DBデータから画像ファイル名の読込
        $objUpFile->setDBFileList($arrForm);
        // DBデータからダウンロードファイル名の読込
        $objDownFile->setDBDownFile($arrForm);

        return $arrForm;
    }

    /**
     * フォームパラメーター取得
     * - 登録モード
     *
     * @param  FormParam $objFormParam FormParamインスタンス
     * @return array  フォームパラメーター配列
     */
    public function lfGetFormParam_Complete(&$objFormParam)
    {
        $arrForm = $objFormParam->getHashArray();
        $arrForm['category_id'] = Utils::jsonDecode($arrForm['category_id']);
        $objFormParam->setValue('category_id', $arrForm['category_id']);

        return $arrForm;
    }

    /**
     * 表示用フォームパラメーター取得
     * - 入力画面
     *
     * @param  UploadFile $objUpFile   UploadFileインスタンス
     * @param  UploadFile $objDownFile UploadFileインスタンス
     * @param  array  $arrForm     フォーム入力パラメーター配列
     * @return array  表示用フォームパラメーター配列
     */
    public function lfSetViewParam_InputPage(&$objUpFile, &$objDownFile, &$arrForm)
    {
        // カテゴリマスターデータ取得
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        list($this->arrCatVal, $this->arrCatOut) = $objDb->getLevelCatList(false);

        if (isset($arrForm['category_id']) && !is_array($arrForm['category_id'])) {
            $arrForm['category_id'] = Utils::jsonDecode($arrForm['category_id']);
        }
        $this->tpl_json_category_id = !empty($arrForm['category_id']) ? Utils::jsonEncode($arrForm['category_id']) : Utils::jsonEncode(array());
        if ($arrForm['status'] == '') {
            $arrForm['status'] = DEFAULT_PRODUCT_DISP;
        }
        if ($arrForm['product_type_id'] == '') {
            $arrForm['product_type_id'] = DEFAULT_PRODUCT_DOWN;
        }
        if (OPTION_PRODUCT_TAX_RULE) {
            // 編集の場合は設定された税率、新規の場合はデフォルトの税率を取得
            if ($arrForm['product_id'] == '') {
                $arrRet = TaxRuleHelper::getTaxRule();
            } else {
                $arrRet = TaxRuleHelper::getTaxRule($arrForm['product_id'], $arrForm['product_class_id']);
            }
            $arrForm['tax_rate'] = $arrRet['tax_rate'];
        }
        // アップロードファイル情報取得(Hidden用)
        $arrHidden = $objUpFile->getHiddenFileList();
        $arrForm['arrHidden'] = array_merge((array) $arrHidden, (array) $objDownFile->getHiddenFileList());

        // 画像ファイル表示用データ取得
        $arrForm['arrFile'] = $objUpFile->getFormFileList(IMAGE_TEMP_URLPATH, IMAGE_SAVE_URLPATH);

        // ダウンロード商品実ファイル名取得
        $arrForm['down_realfilename'] = $objDownFile->getFormDownFile();

        // 基本情報(デフォルトポイントレート用)
        $arrForm['arrInfo'] = Application::alias('eccube.helper.db')->getBasisData();

        // サブ情報ありなしフラグ
        $arrForm['sub_find'] = $this->hasSubProductData($arrForm);

        return $arrForm;
    }

    /**
     * 表示用フォームパラメーター取得
     * - 確認画面
     *
     * @param  UploadFile $objUpFile   UploadFileインスタンス
     * @param  UploadFile $objDownFile UploadFileインスタンス
     * @param  array  $arrForm     フォーム入力パラメーター配列
     * @return array  表示用フォームパラメーター配列
     */
    public function lfSetViewParam_ConfirmPage(&$objUpFile, &$objDownFile, &$arrForm)
    {
        // カテゴリ表示用
        $arrForm['arrCategoryId'] = $arrForm['category_id'];
        // hidden に渡す値は serialize する
        $arrForm['category_id'] = Utils::jsonEncode($arrForm['category_id']);
        // 画像ファイル用データ取得
        $arrForm['arrFile'] = $objUpFile->getFormFileList(IMAGE_TEMP_URLPATH, IMAGE_SAVE_URLPATH);
        // ダウンロード商品実ファイル名取得
        $arrForm['down_realfilename'] = $objDownFile->getFormDownFile();

        return $arrForm;
    }

    /**
     * 縮小した画像をセットする
     *
     * @param  UploadFile $objUpFile UploadFileインスタンス
     * @param  string $image_key 画像ファイルキー
     * @return void
     */
    public function lfSetScaleImage(&$objUpFile, $image_key)
    {
        $subno = str_replace('sub_large_image', '', $image_key);
        switch ($image_key) {
        case 'main_large_image':
            // 詳細メイン画像
            $this->lfMakeScaleImage($objUpFile, $image_key, 'main_image');
        case 'main_image':
            // 一覧メイン画像
            $this->lfMakeScaleImage($objUpFile, $image_key, 'main_list_image');
            break;
        case 'sub_large_image' . $subno:
            // サブメイン画像
            $this->lfMakeScaleImage($objUpFile, $_POST['image_key'], 'sub_image' . $subno);
            break;
        default:
            break;
        }
    }

    /**
     * 画像ファイルのコピー
     *
     * @param  object $objUpFile UploadFileインスタンス
     * @return void
     */
    public function lfCopyProductImageFiles(&$objUpFile)
    {
        $arrKey = $objUpFile->keyname;
        $arrSaveFile = $objUpFile->save_file;

        foreach ($arrSaveFile as $key => $val) {
            $this->lfMakeScaleImage($objUpFile, $arrKey[$key], $arrKey[$key], true);
        }
    }

    /**
     * 縮小画像生成
     *
     * @param  object  $objUpFile UploadFileインスタンス
     * @param  string  $from_key  元画像ファイルキー
     * @param  string  $to_key    縮小画像ファイルキー
     * @param  boolean $forced
     * @return void
     */
    public function lfMakeScaleImage(&$objUpFile, $from_key, $to_key, $forced = false)
    {
        $arrImageKey = array_flip($objUpFile->keyname);
        $from_path = '';

        if ($objUpFile->temp_file[$arrImageKey[$from_key]]) {
            $from_path = $objUpFile->temp_dir . $objUpFile->temp_file[$arrImageKey[$from_key]];
        } elseif ($objUpFile->save_file[$arrImageKey[$from_key]]) {
            $from_path = $objUpFile->save_dir . $objUpFile->save_file[$arrImageKey[$from_key]];
        }

        if (file_exists($from_path)) {
            // 生成先の画像サイズを取得
            $to_w = $objUpFile->width[$arrImageKey[$to_key]];
            $to_h = $objUpFile->height[$arrImageKey[$to_key]];

            if ($forced) {
                $objUpFile->save_file[$arrImageKey[$to_key]] = '';
            }

            if (empty($objUpFile->temp_file[$arrImageKey[$to_key]])
                && empty($objUpFile->save_file[$arrImageKey[$to_key]])
            ) {
                // リネームする際は、自動生成される画像名に一意となるように、Suffixを付ける
                $dst_file = $objUpFile->lfGetTmpImageName(IMAGE_RENAME, '', $objUpFile->temp_file[$arrImageKey[$from_key]]) . $this->lfGetAddSuffix($to_key);
                $path = $objUpFile->makeThumb($from_path, $to_w, $to_h, $dst_file);
                $objUpFile->temp_file[$arrImageKey[$to_key]] = basename($path);
            }
        }
    }

    /**
     * アップロードファイルパラメーター情報から削除
     * 一時ディレクトリに保存されている実ファイルも削除する
     *
     * @param  UploadFile $objUpFile UploadFileインスタンス
     * @param  string $image_key 画像ファイルキー
     * @return void
     */
    public function lfDeleteTempFile(&$objUpFile, $image_key)
    {
        // TODO: UploadFile::deleteFileの画像削除条件見直し要
        $arrTempFile = $objUpFile->temp_file;
        $arrKeyName = $objUpFile->keyname;

        foreach ($arrKeyName as $key => $keyname) {
            if ($keyname != $image_key) continue;

            if (!empty($arrTempFile[$key])) {
                $temp_file = $arrTempFile[$key];
                $arrTempFile[$key] = '';

                if (!in_array($temp_file, $arrTempFile)) {
                    $objUpFile->deleteFile($image_key);
                } else {
                    $objUpFile->temp_file[$key] = '';
                    $objUpFile->save_file[$key] = '';
                }
            } else {
                $objUpFile->temp_file[$key] = '';
                $objUpFile->save_file[$key] = '';
            }
        }
    }

    /**
     * アップロードファイルを保存する
     *
     * @param  object  $objUpFile   UploadFileインスタンス
     * @param  object  $objDownFile UploadFileインスタンス
     * @param  integer $product_id  商品ID
     * @return void
     */
    public function lfSaveUploadFiles(&$objUpFile, &$objDownFile, $product_id)
    {
        // TODO: UploadFile::moveTempFileの画像削除条件見直し要
        $objImage = new Image($objUpFile->temp_dir);
        $arrKeyName = $objUpFile->keyname;
        $arrTempFile = $objUpFile->temp_file;
        $arrSaveFile = $objUpFile->save_file;
        $arrImageKey = array();
        foreach ($arrTempFile as $key => $temp_file) {
            if ($temp_file) {
                $objImage->moveTempImage($temp_file, $objUpFile->save_dir);
                $arrImageKey[] = $arrKeyName[$key];
                if (!empty($arrSaveFile[$key])
                    && !$this->lfHasSameProductImage($product_id, $arrImageKey, $arrSaveFile[$key])
                    && !in_array($temp_file, $arrSaveFile)
                ) {
                    $objImage->deleteImage($arrSaveFile[$key], $objUpFile->save_dir);
                }
            }
        }
        $objDownFile->moveTempDownFile();
    }

    /**
     * 同名画像ファイル登録の有無を確認する.
     *
     * 画像ファイルの削除可否判定用。
     * 同名ファイルの登録がある場合には画像ファイルの削除を行わない。
     * 戻り値： 同名ファイル有り(true) 同名ファイル無し(false)
     *
     * @param  string  $product_id      商品ID
     * @param  string  $arrImageKey     対象としない画像カラム名
     * @param  string  $image_file_name 画像ファイル名
     * @return boolean
     */
    public function lfHasSameProductImage($product_id, $arrImageKey, $image_file_name)
    {
        if (!Utils::sfIsInt($product_id)) return false;
        if (!$arrImageKey) return false;
        if (!$image_file_name) return false;

        $arrWhere = array();
        $sqlval = array('0', $product_id);
        foreach ($arrImageKey as $image_key) {
            $arrWhere[] = "{$image_key} = ?";
            $sqlval[] = $image_file_name;
        }
        $where = implode(' OR ', $arrWhere);
        $where = "del_flg = ? AND ((product_id <> ? AND ({$where}))";

        $arrKeyName = $this->objUpFile->keyname;
        foreach ($arrKeyName as $key => $keyname) {
            if (in_array($keyname, $arrImageKey)) continue;
            $where .= " OR {$keyname} = ?";
            $sqlval[] = $image_file_name;
        }
        $where .= ')';

        $objQuery = Application::alias('eccube.query');
        $exists = $objQuery->exists('dtb_products', $where, $sqlval);

        return $exists;
    }

    /**
     * DBから商品データを取得する
     *
     * @param  integer $product_id 商品ID
     * @return string   商品データ配列
     */
    public function lfGetProductData_FromDB($product_id)
    {
        $objQuery = Application::alias('eccube.query');
        $arrProduct = array();

        // 商品データ取得
        $col = '*';
        $table = <<< __EOF__
            dtb_products AS T1
            LEFT JOIN (
                SELECT product_id AS product_id_sub,
                    product_code,
                    price01,
                    price02,
                    deliv_fee,
                    stock,
                    stock_unlimited,
                    sale_limit,
                    point_rate,
                    product_type_id,
                    down_filename,
                    down_realfilename
                FROM dtb_products_class
            ) AS T2
                ON T1.product_id = T2.product_id_sub
__EOF__;
        $where = 'product_id = ?';
        $objQuery->setLimit('1');
        $arrProduct = $objQuery->select($col, $table, $where, array($product_id));

        // カテゴリID取得
        $col = 'category_id';
        $table = 'dtb_product_categories';
        $where = 'product_id = ?';
        $objQuery->setOption('');
        $arrProduct[0]['category_id'] = $objQuery->getCol($col, $table, $where, array($product_id));

        // 規格情報ありなしフラグ取得
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $arrProduct[0]['has_product_class'] = $objDb->hasProductClass($product_id);

        // 規格が登録されていなければ規格ID取得
        if ($arrProduct[0]['has_product_class'] == false) {
            $arrProduct[0]['product_class_id'] = Utils::sfGetProductClassId($product_id, '0', '0');
        }

        // 商品ステータス取得
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
        $productStatus = $objProduct->getProductStatus(array($product_id));
        $arrProduct[0]['product_status'] = $productStatus[$product_id];

        // 関連商品データ取得
        $arrRecommend = $this->lfGetRecommendProductsData_FromDB($product_id);
        $arrProduct[0] = array_merge($arrProduct[0], $arrRecommend);

        return $arrProduct[0];
    }

    /**
     * DBから関連商品データを取得する
     *
     * @param  integer $product_id 商品ID
     * @return array   関連商品データ配列
     */
    public function lfGetRecommendProductsData_FromDB($product_id)
    {
        $objQuery = Application::alias('eccube.query');
        $arrRecommendProducts = array();

        $col = 'recommend_product_id,';
        $col.= 'comment';
        $table = 'dtb_recommend_products';
        $where = 'product_id = ?';
        $objQuery->setOrder('rank DESC');
        $arrRet = $objQuery->select($col, $table, $where, array($product_id));

        $no = 1;
        foreach ($arrRet as $arrVal) {
            $arrRecommendProducts['recommend_id' . $no] = $arrVal['recommend_product_id'];
            $arrRecommendProducts['recommend_comment' . $no] = $arrVal['comment'];
            $no++;
        }

        return $arrRecommendProducts;
    }

    /**
     * 関連商品データ表示用配列を取得する
     *
     * @param  string $arrForm フォーム入力パラメーター配列
     * @return array  関連商品データ配列
     */
    public function lfGetRecommendProducts(&$arrForm)
    {
        $arrRecommend = array();

        for ($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
            $keyname = 'recommend_id' . $i;
            $delkey = 'recommend_delete' . $i;
            $commentkey = 'recommend_comment' . $i;

            if (!isset($arrForm[$delkey])) $arrForm[$delkey] = null;

            if ((isset($arrForm[$keyname]) && !empty($arrForm[$keyname])) && $arrForm[$delkey] != 1) {
                /* @var $objProduct Product */
                $objProduct = Application::alias('eccube.product');
                $arrRecommend[$i] = $objProduct->getDetail($arrForm[$keyname]);
                $arrRecommend[$i]['product_id'] = $arrForm[$keyname];
                $arrRecommend[$i]['comment'] = $arrForm[$commentkey];
            }
        }

        return $arrRecommend;
    }

    /**
     * 表示用カテゴリマスターデータ配列を取得する
     * - 編集モード
     *
     * @param void
     * @return array カテゴリマスターデータ配列
     */
    public function lfGetCategoryList_Edit()
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $arrCategoryList = array();

        list($arrCatVal, $arrCatOut) = $objDb->getLevelCatList(false);
        for ($i = 0; $i < count($arrCatVal); $i++) {
            $arrCategoryList[$arrCatVal[$i]] = $arrCatOut[$i];
        }

        return $arrCategoryList;
    }

    /**
     * ページonload用JavaScriptを取得する
     * - 入力画面
     *
     * @param  string $anchor_hash アンカー用ハッシュ文字列(省略可)
     * @return string ページonload用JavaScript
     */
    public function lfSetOnloadJavaScript_InputPage($anchor_hash = '')
    {
        return "eccube.checkStockLimit('" . DISABLED_RGB . "');fnInitSelect('category_id_unselect'); fnMoveSelect('category_id_unselect', 'category_id');" . $anchor_hash;
    }

    /**
     * DBに商品データを登録する
     *
     * @param  UploadFile  $objUpFile   UploadFileインスタンス
     * @param  UploadFile  $objDownFile UploadFileインスタンス
     * @param  array   $arrList     フォーム入力パラメーター配列
     * @return integer 登録商品ID
     */
    public function lfRegistProduct(&$objUpFile, &$objDownFile, $arrList)
    {
        $objQuery = Application::alias('eccube.query');
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');

        // 配列の添字を定義
        $checkArray = array('name', 'status',
                            'main_list_comment', 'main_comment',
                            'deliv_fee', 'comment1', 'comment2', 'comment3',
                            'comment4', 'comment5', 'comment6',
                            'sale_limit', 'deliv_date_id', 'maker_id', 'note');
        $arrList = Utils::arrayDefineIndexes($arrList, $checkArray);

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
        $sqlval['deliv_date_id'] = $arrList['deliv_date_id'];
        $sqlval['maker_id'] = $arrList['maker_id'];
        $sqlval['note'] = $arrList['note'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['creator_id'] = $_SESSION['member_id'];
        $arrRet = $objUpFile->getDBFileList();
        $sqlval = array_merge($sqlval, $arrRet);

        for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $sqlval['sub_title'.$cnt] = $arrList['sub_title'.$cnt];
            $sqlval['sub_comment'.$cnt] = $arrList['sub_comment'.$cnt];
        }

        $objQuery->begin();

        // 新規登録(複製時を含む)
        if ($arrList['product_id'] == '') {
            $product_id = $objQuery->nextVal('dtb_products_product_id');
            $sqlval['product_id'] = $product_id;

            // INSERTの実行
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->insert('dtb_products', $sqlval);

            $arrList['product_id'] = $product_id;

            // カテゴリを更新
            $objDb->updateProductCategories($arrList['category_id'], $product_id);

            // 複製商品の場合には規格も複製する
            if ($arrList['copy_product_id'] != '' && Utils::sfIsInt($arrList['copy_product_id'])) {
                if (!$arrList['has_product_class']) {
                    //規格なしの場合、複製は価格等の入力が発生しているため、その内容で追加登録を行う
                    $this->lfCopyProductClass($arrList, $objQuery);
                } else {
                    //規格がある場合の複製は複製元の内容で追加登録を行う
                    // dtb_products_class のカラムを取得
                    /* @var $dbFactory DBFactory */
                    $dbFactory = Application::alias('eccube.db.factory');
                    $arrColList = $objQuery->listTableFields('dtb_products_class');
                    $arrColList_tmp = array_flip($arrColList);

                    // 複製しない列
                    unset($arrColList[$arrColList_tmp['product_class_id']]);     //規格ID
                    unset($arrColList[$arrColList_tmp['product_id']]);           //商品ID
                    unset($arrColList[$arrColList_tmp['create_date']]);

                    // 複製元商品の規格データ取得
                    $col = Utils::sfGetCommaList($arrColList);
                    $table = 'dtb_products_class';
                    $where = 'product_id = ?';
                    $objQuery->setOrder('product_class_id');
                    $arrProductsClass = $objQuery->select($col, $table, $where, array($arrList['copy_product_id']));

                    // 規格データ登録
                    $objQuery = Application::alias('eccube.query');
                    foreach ($arrProductsClass as $arrData) {
                        $sqlval = $arrData;
                        $sqlval['product_class_id'] = $objQuery->nextVal('dtb_products_class_product_class_id');
                        $sqlval['deliv_fee'] = $arrList['deliv_fee'];
                        $sqlval['point_rate'] = $arrList['point_rate'];
                        $sqlval['sale_limit'] = $arrList['sale_limit'];
                        $sqlval['product_id'] = $product_id;
                        $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
                        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
                        $objQuery->insert($table, $sqlval);
                    }
                }
            }
        // 更新
        } else {
            $product_id = $arrList['product_id'];
            // 削除要求のあった既存ファイルの削除
            $arrRet = $this->lfGetProductData_FromDB($arrList['product_id']);
            // TODO: UploadFile::deleteDBFileの画像削除条件見直し要
            $objImage = new Image($objUpFile->temp_dir);
            $arrKeyName = $objUpFile->keyname;
            $arrSaveFile = $objUpFile->save_file;
            $arrImageKey = array();
            foreach ($arrKeyName as $key => $keyname) {
                if ($arrRet[$keyname] && !$arrSaveFile[$key]) {
                    $arrImageKey[] = $keyname;
                    $has_same_image = $this->lfHasSameProductImage($arrList['product_id'], $arrImageKey, $arrRet[$keyname]);
                    if (!$has_same_image) {
                        $objImage->deleteImage($arrRet[$keyname], $objUpFile->save_dir);
                    }
                }
            }
            $objDownFile->deleteDBDownFile($arrRet);
            // UPDATEの実行
            $where = 'product_id = ?';
            $objQuery->update('dtb_products', $sqlval, $where, array($product_id));

            // カテゴリを更新
            $objDb->updateProductCategories($arrList['category_id'], $product_id);
        }

        // 商品登録の時は規格を生成する。複製の場合は規格も複製されるのでこの処理は不要。
        if ($arrList['copy_product_id'] == '') {
            // 規格登録
            if ($objDb->hasProductClass($product_id)) {
                // 規格あり商品（商品規格テーブルのうち、商品登録フォームで設定するパラメーターのみ更新）
                $this->lfUpdateProductClass($arrList);
            } else {
                // 規格なし商品（商品規格テーブルの更新）
                $arrList['product_class_id'] = $this->lfInsertDummyProductClass($arrList);
            }
        }

        // 商品ステータス設定
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
        $objProduct->setProductStatus($product_id, $arrList['product_status']);

        // 税情報設定
        if (OPTION_PRODUCT_TAX_RULE && !$objDb->hasProductClass($product_id)) {
            TaxRuleHelper::setTaxRuleForProduct($arrList['tax_rate'], $arrList['product_id'], $arrList['product_class_id']);
        }

        // 関連商品登録
        $this->lfInsertRecommendProducts($objQuery, $arrList, $product_id);

        $objQuery->commit();

        return $product_id;
    }

    /**
     * 規格を設定していない商品を商品規格テーブルに登録
     *
     * @param  array $arrList
     * @return void
     */
    public function lfInsertDummyProductClass($arrList)
    {
        $objQuery = Application::alias('eccube.query');
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');

        // 配列の添字を定義
        $checkArray = array('product_class_id', 'product_id', 'product_code', 'stock', 'stock_unlimited', 'price01', 'price02', 'sale_limit', 'deliv_fee', 'point_rate', 'product_type_id', 'down_filename', 'down_realfilename');
        $sqlval = Utils::sfArrayIntersectKeys($arrList, $checkArray);
        $sqlval = Utils::arrayDefineIndexes($sqlval, $checkArray);

        $sqlval['stock_unlimited'] = $sqlval['stock_unlimited'] ? UNLIMITED_FLG_UNLIMITED : UNLIMITED_FLG_LIMITED;
        $sqlval['creator_id'] = strlen($_SESSION['member_id']) >= 1 ? $_SESSION['member_id'] : '0';

        if (strlen($sqlval['product_class_id']) == 0) {
            $sqlval['product_class_id'] = $objQuery->nextVal('dtb_products_class_product_class_id');
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
            // INSERTの実行
            $objQuery->insert('dtb_products_class', $sqlval);
        } else {
            $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
            // UPDATEの実行
            $objQuery->update('dtb_products_class', $sqlval, 'product_class_id = ?', array($sqlval['product_class_id']));
        }
        return $sqlval['product_class_id'];
    }

    /**
     * 規格を設定している商品の商品規格テーブルを更新
     * (deliv_fee, point_rate, sale_limit)
     *
     * @param  array $arrList
     * @return void
     */
    public function lfUpdateProductClass($arrList)
    {
        $objQuery = Application::alias('eccube.query');
        $sqlval = array();

        $sqlval['deliv_fee'] = $arrList['deliv_fee'];
        $sqlval['point_rate'] = $arrList['point_rate'];
        $sqlval['sale_limit'] = $arrList['sale_limit'];
        $where = 'product_id = ?';
        $objQuery->update('dtb_products_class', $sqlval, $where, array($arrList['product_id']));
    }

    /**
     * DBに関連商品データを登録する
     *
     * @param  Query  $objQuery   Queryインスタンス
     * @param  string  $arrList    フォーム入力パラメーター配列
     * @param  integer $product_id 登録する商品ID
     * @return void
     */
    public function lfInsertRecommendProducts(&$objQuery, $arrList, $product_id)
    {
        // 一旦関連商品を全て削除する
        $objQuery->delete('dtb_recommend_products', 'product_id = ?', array($product_id));
        $sqlval['product_id'] = $product_id;
        $rank = RECOMMEND_PRODUCT_MAX;
        for ($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
            $keyname = 'recommend_id' . $i;
            $commentkey = 'recommend_comment' . $i;
            $deletekey = 'recommend_delete' . $i;

            if (!isset($arrList[$deletekey])) $arrList[$deletekey] = null;

            if ($arrList[$keyname] != '' && $arrList[$deletekey] != '1') {
                $sqlval['recommend_product_id'] = $arrList[$keyname];
                $sqlval['comment'] = $arrList[$commentkey];
                $sqlval['rank'] = $rank;
                $sqlval['creator_id'] = $_SESSION['member_id'];
                $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
                $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
                $objQuery->insert('dtb_recommend_products', $sqlval);
                $rank--;
            }
        }
    }

    /**
     * 規格データをコピーする
     *
     * @param  array   $arrList  フォーム入力パラメーター配列
     * @param  Query  $objQuery Queryインスタンス
     * @return boolean エラーフラグ
     */
    public function lfCopyProductClass($arrList, &$objQuery)
    {
        // 複製元のdtb_products_classを取得（規格なしのため、1件のみの取得）
        $col = '*';
        $table = 'dtb_products_class';
        $where = 'product_id = ?';
        $arrProductClass = $objQuery->select($col, $table, $where, array($arrList['copy_product_id']));

        //トランザクション開始
        $objQuery->begin();
        $err_flag = false;
        //非編集項目は複製、編集項目は上書きして登録
        foreach ($arrProductClass as $records) {
            foreach ($records as $key => $value) {
                if (isset($arrList[$key])) {
                    switch ($key) {
                    case 'stock_unlimited':
                        $records[$key] = (int) $arrList[$key];
                        break;
                    default:
                        $records[$key] = $arrList[$key];
                        break;
                    }
                }
            }

            $records['product_class_id'] = $objQuery->nextVal('dtb_products_class_product_class_id');
            $records['update_date'] = 'CURRENT_TIMESTAMP';
            $records['create_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->insert($table, $records);
            //エラー発生時は中断
            if ($objQuery->isError()) {
                $err_flag = true;
                continue;
            }
        }
        //トランザクション終了
        if ($err_flag) {
            $objQuery->rollback();
        } else {
            $objQuery->commit();
        }

        return !$err_flag;
    }

    /**
     * リネームする際は、自動生成される画像名に一意となるように、Suffixを付ける
     *
     * @param  string $to_key
     * @return string
     */
    public function lfGetAddSuffix($to_key)
    {
        if ( IMAGE_RENAME === true) return;

        // 自動生成される画像名
        $dist_name = '';
        switch ($to_key) {
        case 'main_list_image':
            $dist_name = '_s';
            break;
        case 'main_image':
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
     * サブ情報の登録があるかを取得する
     * タイトル, コメント, 画像のいずれかに登録があれば「あり」と判定する
     *
     * @param  array   $arrSubProductData サブ情報配列
     * @return boolean true: サブ情報あり, false: サブ情報なし
     */
    public function hasSubProductData($arrSubProductData)
    {
        $has_subproduct_data = false;

        for ($i = 1; $i <= PRODUCTSUB_MAX; $i++) {
            if (Utils::isBlank($arrSubProductData['sub_title'.$i]) == false
                || Utils::isBlank($arrSubProductData['sub_comment'.$i]) == false
                || Utils::isBlank($arrSubProductData['sub_image'.$i]) == false
                || Utils::isBlank($arrSubProductData['sub_large_image'.$i]) == false
                || Utils::isBlank($arrSubProductData['temp_sub_image'.$i]) == false
                || Utils::isBlank($arrSubProductData['temp_sub_large_image'.$i]) == false
            ) {
                $has_subproduct_data = true;
                break;
            }
        }

        return $has_subproduct_data;
    }

    /**
     * アンカーハッシュ文字列を取得する
     * アンカーキーをサニタイジングする
     *
     * @param  string $anchor_key フォーム入力パラメーターで受け取ったアンカーキー
     * @return <type>
     */
    public function getAnchorHash($anchor_key)
    {
        if ($anchor_key != '') {
            return "location.hash='#" . htmlspecialchars($anchor_key) . "'";
        } else {
            return '';
        }
    }
}
