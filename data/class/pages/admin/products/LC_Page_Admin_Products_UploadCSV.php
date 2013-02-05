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
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 商品登録CSVのページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Admin_Products_UploadCSV.php 15532 2007-08-31 14:39:46Z nanasess $
 *
 * FIXME 同一商品IDで商品規格違いを登録できない。(更新は可能)
 */
class LC_Page_Admin_Products_UploadCSV extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /** TAGエラーチェックフィールド情報 */
    var $arrTagCheckItem;

    /** 商品テーブルカラム情報 (登録処理用) **/
    var $arrProductColumn;

    /** 商品規格テーブルカラム情報 (登録処理用) **/
    var $arrProductClassColumn;

    /** 登録フォームカラム情報 **/
    var $arrFormKeyList;

    var $arrRowErr;

    var $arrRowResult;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/upload_csv.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'upload_csv';
        $this->tpl_maintitle = t('c_Products_01');
        $this->tpl_subtitle = t('c_Product registration CSV_01');
        $this->csv_id = '1';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrDISP = $masterData->getMasterData('mtb_disp');
        $this->arrSTATUS = $masterData->getMasterData('mtb_status');
        $this->arrDELIVERYDATE = $masterData->getMasterData('mtb_delivery_date');
        $this->arrProductType = $masterData->getMasterData('mtb_product_type');
        $this->arrMaker = SC_Helper_DB_Ex::sfGetIDValueList('dtb_maker', 'maker_id', 'name');
        $this->arrPayments = SC_Helper_DB_Ex::sfGetIDValueList('dtb_payment', 'payment_id', 'payment_method');
        $this->arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
        $this->arrAllowedTag = $masterData->getMasterData('mtb_allowed_tag');
        $this->arrTagCheckItem = array();
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

        $this->objDb = new SC_Helper_DB_Ex();

        // CSV管理ヘルパー
        $objCSV = new SC_Helper_CSV_Ex();
        // CSV構造読み込み
        $arrCSVFrame = $objCSV->sfGetCsvOutput($this->csv_id);

        // CSV構造がインポート可能かのチェック
        if (!$objCSV->sfIsImportCSVFrame($arrCSVFrame)) {
            // 無効なフォーマットなので初期状態に強制変更
            $arrCSVFrame = $objCSV->sfGetCsvOutput($this->csv_id, '', array(), 'no');
            $this->tpl_is_format_default = true;
        }
        // CSV構造は更新可能なフォーマットかのフラグ取得
        $this->tpl_is_update = $objCSV->sfIsUpdateCSVFrame($arrCSVFrame);

        // CSVファイルアップロード情報の初期化
        $objUpFile = new SC_UploadFile_Ex(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        $this->lfInitFile($objUpFile);

        // パラメーター情報の初期化
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam, $arrCSVFrame);

        $objFormParam->setHtmlDispNameArray();
        $this->arrTitle = $objFormParam->getHtmlDispNameArray();

        switch ($this->getMode()) {
            case 'csv_upload':
                $this->doUploadCsv($objFormParam, $objUpFile);
                break;
            default:
                break;
        }

    }

    /**
     * 登録/編集結果のメッセージをプロパティへ追加する
     *
     * @param integer $line_count 行数
     * @param stirng $message メッセージ
     * @return void
     */
    function addRowResult($line_count, $message) {
        $this->arrRowResult[] = t('c_Line T_ARG1: T_ARG2_01', array('T_ARG1' => $line_count, 'T_ARG2' => $message));
    }

    /**
     * 登録/編集結果のエラーメッセージをプロパティへ追加する
     *
     * @param integer $line_count 行数
     * @param stirng $message メッセージ
     * @return void
     */
    function addRowErr($line_count, $message) {
        $this->arrRowErr[] = t('c_Line T_ARG1: T_ARG2_01', array('T_ARG1' => $line_count, 'T_ARG2' => $message));
    }

    /**
     * CSVアップロードを実行します.
     *
     * @return void
     */
    function doUploadCsv(&$objFormParam, &$objUpFile) {
        // ファイルアップロードのチェック
        $this->arrErr['csv_file'] = $objUpFile->makeTempFile('csv_file');
        if (strlen($this->arrErr['csv_file']) >= 1) {
            return;
        }
        $arrErr = $objUpFile->checkExists();
        if (count($arrErr) > 0) {
            $this->arrErr = $arrErr;
            return;
        }
        // 一時ファイル名の取得
        $filepath = $objUpFile->getTempFilePath('csv_file');
        // CSVファイルの文字コード変換
        $enc_filepath = SC_Utils_Ex::sfEncodeFile($filepath, CHAR_CODE, CSV_TEMP_REALDIR);
        // CSVファイルのオープン
        $fp = fopen($enc_filepath, 'r');
        // 失敗した場合はエラー表示
        if (!$fp) {
            SC_Utils_Ex::sfDispError('');
        }

        // 登録先テーブル カラム情報の初期化
        $this->lfInitTableInfo();

        // 登録フォーム カラム情報
        $this->arrFormKeyList = $objFormParam->getKeyList();

        // 登録対象の列数
        $col_max_count = $objFormParam->getCount();
        // 行数
        $line_count = 0;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        $errFlag = false;
        $all_line_checked = false;

        while (!feof($fp)) {
            $arrCSV = fgetcsv($fp, CSV_LINE_MAX);

            // 全行入力チェック後に、ファイルポインターを先頭に戻す
            if (feof($fp) && !$all_line_checked) {
                rewind($fp);
                $line_count = 0;
                $all_line_checked = true;
                continue;
            }

            // 行カウント
            $line_count++;
            // ヘッダ行はスキップ
            if ($line_count == 1) {
                continue;
            }
            // 空行はスキップ
            if (empty($arrCSV)) {
                continue;
            }
            // 列数が異なる場合はエラー
            $col_count = count($arrCSV);
            if ($col_max_count != $col_count) {
                $this->addRowErr($line_count, t('c_* T_ARG1 was detected for the item quantity. The item quantity is T_ARG2._01', array('T_ARG1' => $col_count, 'T_ARG2' => $col_max_count)));
                $errFlag = true;
                break;
            }
            // シーケンス配列を格納する。
            $objFormParam->setParam($arrCSV, true);
            $arrRet = $objFormParam->getHashArray();
            $objFormParam->setParam($arrRet);
            // 入力値の変換
            $objFormParam->convParam();
            // <br>なしでエラー取得する。
            $arrCSVErr = $this->lfCheckError($objFormParam);

            // 入力エラーチェック
            if (count($arrCSVErr) > 0) {
                foreach ($arrCSVErr as $err) {
                    $this->addRowErr($line_count, $err);
                }
                $errFlag = true;
                break;
            }

            if ($all_line_checked) {
                $this->lfRegistProduct($objQuery, $line_count, $objFormParam);
                $arrParam = $objFormParam->getHashArray();

                $this->addRowResult($line_count, t('c_Product ID: T_ARG1 / Product name: T_ARG2_01', array('T_ARG1' => $arrParam['product_id'], 'T_ARG2' => $arrParam['name'])));
            }
            SC_Utils_Ex::extendTimeOut();
        }

        // 実行結果画面を表示
        $this->tpl_mainpage = 'products/upload_csv_complete.tpl';

        fclose($fp);

        if ($errFlag) {
            $objQuery->rollback();
            return;
        }

        $objQuery->commit();

        // 商品件数カウント関数の実行
        $this->objDb->sfCountCategory($objQuery);
        $this->objDb->sfCountMaker($objQuery);
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
     * ファイル情報の初期化を行う.
     *
     * @return void
     */
    function lfInitFile(&$objUpFile) {
        $objUpFile->addFile(t('c_CSV file_01'), 'csv_file', array('csv'), CSV_SIZE, true, 0, 0, false);
    }

    /**
     * 入力情報の初期化を行う.
     *
     * @param array CSV構造設定配列
     * @return void
     */
    function lfInitParam(&$objFormParam, &$arrCSVFrame) {
        // 固有の初期値調整
        $arrCSVFrame = $this->lfSetParamDefaultValue($arrCSVFrame);
        // CSV項目毎の処理
        foreach ($arrCSVFrame as $item) {
            if ($item['status'] == CSV_COLUMN_STATUS_FLG_DISABLE) continue;
            //サブクエリ構造の場合は AS名 を使用
            if (preg_match_all('/\(.+\)\s+as\s+(.+)$/i', $item['col'], $match, PREG_SET_ORDER)) {
                $col = $match[0][1];
            } else {
                $col = $item['col'];
            }
            // HTML_TAG_CHECKは別途実行なので除去し、別保存しておく
            if (strpos(strtoupper($item['error_check_types']), 'HTML_TAG_CHECK') !== FALSE) {
                $this->arrTagCheckItem[] = $item;
                $error_check_types = str_replace('HTML_TAG_CHECK', '', $item['error_check_types']);
            } else {
                $error_check_types = $item['error_check_types'];
            }
            $arrErrorCheckTypes = explode(',', $error_check_types);
            foreach ($arrErrorCheckTypes as $key => $val) {
                if (trim($val) == '') {
                    unset($arrErrorCheckTypes[$key]);
                } else {
                    $arrErrorCheckTypes[$key] = trim($val);
                }
            }
            // パラメーター登録
            $objFormParam->addParam(
                    $item['disp_name']
                    , $col
                    , constant($item['size_const_type'])
                    , $item['mb_convert_kana_option']
                    , $arrErrorCheckTypes
                    , $item['default']
                    , ($item['rw_flg'] != CSV_COLUMN_RW_FLG_READ_ONLY) ? true : false
                    );
        }
    }

    /**
     * 入力チェックを行う.
     *
     * @return void
     */
    function lfCheckError(&$objFormParam) {
        // 入力データを渡す。
        $arrRet =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrRet);
        $objErr->arrErr = $objFormParam->checkError(false);
        // HTMLタグチェックの実行
        foreach ($this->arrTagCheckItem as $item) {
            $objErr->doFunc(array($item['disp_name'], $item['col'], $this->arrAllowedTag), array('HTML_TAG_CHECK'));
        }
        // このフォーム特有の複雑系のエラーチェックを行う
        if (count($objErr->arrErr) == 0) {
            $objErr->arrErr = $this->lfCheckErrorDetail($arrRet, $objErr->arrErr);
        }
        return $objErr->arrErr;
    }

    /**
     * 保存先テーブル情報の初期化を行う.
     *
     * @return void
     */
    function lfInitTableInfo() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->arrProductColumn = $objQuery->listTableFields('dtb_products');
        $this->arrProductClassColumn = $objQuery->listTableFields('dtb_products_class');
    }

    /**
     * 商品登録を行う.
     *
     * FIXME: 商品登録の実処理自体は、LC_Page_Admin_Products_Productと共通化して欲しい。
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistProduct($objQuery, $line = '', &$objFormParam) {
        $objProduct = new SC_Product_Ex();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();
        // 登録時間を生成(DBのCURRENT_TIMESTAMPだとcommitした際、すべて同一の時間になってしまう)
        $arrList['update_date'] = $this->lfGetDbFormatTimeWithLine($line);

        // 商品登録情報を生成する。
        // 商品テーブルのカラムに存在しているもののうち、Form投入設定されていないデータは上書きしない。
        $sqlval = SC_Utils_Ex::sfArrayIntersectKeys($arrList, $this->arrProductColumn);

        // 必須入力では無い項目だが、空文字では問題のある特殊なカラム値の初期値設定
        $sqlval = $this->lfSetProductDefaultData($sqlval);

        if ($sqlval['product_id'] != '') {
            // 同じidが存在すればupdate存在しなければinsert
            $where = 'product_id = ?';
            $product_exists = $objQuery->exists('dtb_products', $where, array($sqlval['product_id']));
            if ($product_exists) {
                $objQuery->update('dtb_products', $sqlval, $where, array($sqlval['product_id']));
            } else {
                $sqlval['create_date'] = $arrList['update_date'];
                // INSERTの実行
                $objQuery->insert('dtb_products', $sqlval);
                // シーケンスの調整
                $seq_count = $objQuery->currVal('dtb_products_product_id');
                if ($seq_count < $sqlval['product_id']) {
                    $objQuery->setVal('dtb_products_product_id', $sqlval['product_id'] + 1);
                }
            }
            $product_id = $sqlval['product_id'];
        } else {
            // 新規登録
            $sqlval['product_id'] = $objQuery->nextVal('dtb_products_product_id');
            $product_id = $sqlval['product_id'];
            $sqlval['create_date'] = $arrList['update_date'];
            // INSERTの実行
            $objQuery->insert('dtb_products', $sqlval);
        }

        // カテゴリ登録
        if (isset($arrList['category_ids'])) {
            $arrCategory_id = explode(',', $arrList['category_ids']);
            $this->objDb->updateProductCategories($arrCategory_id, $product_id);
        }
        // 商品ステータス登録
        if (isset($arrList['product_statuses'])) {
            $arrStatus_id = explode(',', $arrList['product_statuses']);
            $objProduct->setProductStatus($product_id, $arrStatus_id);
        }

        // 商品規格情報を登録する
        $this->lfRegistProductClass($objQuery, $arrList, $product_id, $arrList['product_class_id']);

        // 関連商品登録
        $this->lfRegistReccomendProducts($objQuery, $arrList, $product_id);
    }

    /**
     * 商品規格登録を行う.
     *
     * FIXME: 商品規格登録の実処理自体は、LC_Page_Admin_Products_Productと共通化して欲しい。
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param array $arrList 商品規格情報配列
     * @param integer $product_id 商品ID
     * @param integer $product_class_id 商品規格ID
     * @return void
     */
    function lfRegistProductClass($objQuery, $arrList, $product_id, $product_class_id) {
        $objProduct = new SC_Product_Ex();
        // 商品規格登録情報を生成する。
        // 商品規格テーブルのカラムに存在しているもののうち、Form投入設定されていないデータは上書きしない。
        $sqlval = SC_Utils_Ex::sfArrayIntersectKeys($arrList, $this->arrProductClassColumn);

        if ($product_class_id == '') {
            // 新規登録
            // 必須入力では無い項目だが、空文字では問題のある特殊なカラム値の初期値設定
            $sqlval = $this->lfSetProductClassDefaultData($sqlval);
            $sqlval['product_id'] = $product_id;
            $sqlval['product_class_id'] = $objQuery->nextVal('dtb_products_class_product_class_id');
            $sqlval['create_date'] = $arrList['update_date'];
            // INSERTの実行
            $objQuery->insert('dtb_products_class', $sqlval);
            $product_class_id = $sqlval['product_class_id'];
        } else {
            // UPDATEの実行
            // 必須入力では無い項目だが、空文字では問題のある特殊なカラム値の初期値設定
            $sqlval = $this->lfSetProductClassDefaultData($sqlval, true);
            $where = 'product_class_id = ?';
            $objQuery->update('dtb_products_class', $sqlval, $where, array($product_class_id));
        }
    }

    /**
     * 関連商品登録を行う.
     *
     * FIXME: 商品規格登録の実処理自体は、LC_Page_Admin_Products_Productと共通化して欲しい。
     *        DELETE/INSERT ではなく UPDATEへの変更も・・・
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param array $arrList 商品規格情報配列
     * @param integer $product_id 商品ID
     * @return void
     */
    function lfRegistReccomendProducts($objQuery, $arrList, $product_id) {
        $objQuery->delete('dtb_recommend_products', 'product_id = ?', array($product_id));
        for ($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
            $keyname = 'recommend_product_id' . $i;
            $comment_key = 'recommend_comment' . $i;
            if ($arrList[$keyname] != '') {
                $arrProduct = $objQuery->select('product_id', 'dtb_products', 'product_id = ?', array($arrList[$keyname]));
                if ($arrProduct[0]['product_id'] != '') {
                    $arrWhereVal = array();
                    $arrWhereVal['product_id'] = $product_id;
                    $arrWhereVal['recommend_product_id'] = $arrProduct[0]['product_id'];
                    $arrWhereVal['comment'] = $arrList[$comment_key];
                    $arrWhereVal['update_date'] = $arrList['update_date'];
                    $arrWhereVal['create_date'] = $arrList['update_date'];
                    $arrWhereVal['creator_id'] = $_SESSION['member_id'];
                    $arrWhereVal['rank'] = RECOMMEND_PRODUCT_MAX - $i + 1;
                    $objQuery->insert('dtb_recommend_products', $arrWhereVal);
                }
            }
        }
    }

    /**
     * 初期値の設定
     *
     * @param array $arrCSVFrame CSV構造配列
     * @return array $arrCSVFrame CSV構造配列
     */
    function lfSetParamDefaultValue(&$arrCSVFrame) {
        foreach ($arrCSVFrame as $key => $val) {
            switch ($val['col']) {
                case 'status':
                    $arrCSVFrame[$key]['default'] = DEFAULT_PRODUCT_DISP;
                    break;
                case 'del_flg':
                    $arrCSVFrame[$key]['default'] = '0';
                    break;
                case 'point_rate':
                    $arrCSVFrame[$key]['default'] = $this->arrInfo['point_rate'];
                    break;
                case 'product_type_id':
                    $arrCSVFrame[$key]['default'] = DEFAULT_PRODUCT_DOWN;
                    break;
                case 'stock_unlimited':
                    $arrCSVFrame[$key]['default'] = UNLIMITED_FLG_LIMITED;
                default:
                    break;
            }
        }
        return $arrCSVFrame;
    }

    /**
     * 商品データ登録前に特殊な値の持ち方をする部分のデータ部分の初期値補正を行う
     *
     * @param array $sqlval 商品登録情報配列
     * @return $sqlval 登録情報配列
     */
    function lfSetProductDefaultData(&$sqlval) {
        //新規登録時のみ設定する項目
        if ($sqlval['product_id'] == '') {
            if ($sqlval['status'] == '') {
                $sqlval['status'] = DEFAULT_PRODUCT_DISP;
            }
        }
        //共通で空欄時に上書きする項目
        if ($sqlval['del_flg'] == '') {
            $sqlval['del_flg'] = '0'; //有効
        }
        if ($sqlval['creator_id'] == '') {
            $sqlval['creator_id'] = $_SESSION['member_id'];
        }
        return $sqlval;
    }

    /**
     * 商品規格データ登録前に特殊な値の持ち方をする部分のデータ部分の初期値補正を行う
     *
     * @param array $sqlval 商品登録情報配列
     * @param boolean $upload_flg 更新フラグ(更新の場合true)
     * @return $sqlval 登録情報配列
     */
    function lfSetProductClassDefaultData(&$sqlval, $upload_flg) {
        //新規登録時のみ設定する項目
        if ($sqlval['product_class_id'] == '') {
            if ($sqlval['point_rate'] == '') {
                $sqlval['point_rate'] = $this->arrInfo['point_rate'];
            }
            if ($sqlval['product_type_id'] == '') {
                $sqlval['product_type_id'] = DEFAULT_PRODUCT_DOWN;
            }
        }
        //共通で設定する項目
        if ($sqlval['del_flg'] == '') {
            $sqlval['del_flg'] = '0'; //有効
        }
        if ($sqlval['creator_id'] == '') {
            $sqlval['creator_id'] = $_SESSION['member_id'];
        }

        // 在庫無制限フラグ列を利用する場合、
        if (array_key_exists('stock_unlimited', $sqlval) and $sqlval['stock_unlimited'] != '') {
            // 在庫無制限フラグ = 無制限の場合、
            if ($sqlval['stock_unlimited'] == UNLIMITED_FLG_UNLIMITED) {
                $sqlval['stock'] = null;
            }
        } else {
            // 初期登録の場合は、在庫数設定がされていない場合、在庫無制限フラグ = 無制限。
            if (strlen($sqlval['stock']) === 0){
                //更新の場合は、sqlvalのキーにstockがある場合のみ対象
                if (!$upload_flg or ($upload_flg and array_key_exists('stock', $sqlval))) {
                    $sqlval['stock_unlimited'] = UNLIMITED_FLG_UNLIMITED;
                }
            }
            // 在庫数を入力している場合、在庫無制限フラグ = 制限有り
            elseif (strlen($sqlval['stock']) >= 1) {
                $sqlval['stock_unlimited'] = UNLIMITED_FLG_LIMITED;
            }
            // いずれにも該当しない場合、例外エラー
            else {
                trigger_error('', E_USER_ERROR);
            }
        }
        return $sqlval;
    }

    /**
     * このフォーム特有の複雑な入力チェックを行う.
     *
     * @param array 確認対象データ
     * @param array エラー配列
     * @return array エラー配列
     */
    function lfCheckErrorDetail($item, $arrErr) {
        // 規格IDの存在チェック
        // FIXME 規格分類ID自体のが有効かを主眼においたチェックをすべきと感じる。
        if (!$this->lfIsDbRecord('dtb_products_class', 'product_class_id', $item)) {
            $arrErr['product_class_id'] = t('c_* The designated product specification ID is not registered._01');
        }
        // 商品ID、規格IDの組合せチェック
        if (array_search('product_class_id', $this->arrFormKeyList) !== FALSE
            && $item['product_class_id'] != ''
        ) {
            if ($item['product_id'] == '') {
                $arrErr['product_class_id'] = t('c_* During product specification ID designation, it is necessary to designate a product ID._01');
            } else {
                if (!$this->objDb->sfIsRecord('dtb_products_class', 'product_id, product_class_id'
                        , array($item['product_id'], $item['product_class_id']))
                ) {
                    $arrErr['product_class_id'] = t('c_* The designated product ID and production specification ID combination is not correct._01');
                }
            }
        }
        // 表示ステータスの存在チェック
        if (!$this->lfIsArrayRecord($this->arrDISP, 'status', $item)) {
            $arrErr['status'] = t('c_* The designated display status is not registered._01');
        }
        // メーカーIDの存在チェック
        if (!$this->lfIsArrayRecord($this->arrMaker, 'maker_id', $item)) {
            $arrErr['maker_id'] = t('c_* The designated manufacturer ID is not registered._01');
        }
        // 発送日目安IDの存在チェック
        if (!$this->lfIsArrayRecord($this->arrDELIVERYDATE, 'deliv_date_id', $item)) {
            $arrErr['deliv_date_id'] = t('c_* The designated target delivery date ID is not registered._01');
        }
        // 発送日目安IDの存在チェック
        if (!$this->lfIsArrayRecord($this->arrProductType, 'product_type_id', $item)) {
            $arrErr['product_type_id'] = t('c_* The specified product ID does not exist._01');
        }
        // 関連商品IDのチェック
        $arrRecommendProductUnique = array();
        for ($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
            $recommend_product_id_key = 'recommend_product_id' . $i;
            if ((array_search($recommend_product_id_key, $this->arrFormKeyList) !== FALSE)
             && ($item[$recommend_product_id_key] != '')) {

                // 商品IDの存在チェック
                if (!$this->objDb->sfIsRecord('dtb_products', 'product_id', (array)$item[$recommend_product_id_key])) {
                    $arrErr[$recommend_product_id_key] = t('c_* The designated relevant product ID (T_ARG1) is not registered._01', array('T_ARG1' => $i));
                    continue;
                }
                // 商品IDの重複チェック
                $recommend_product_id = $item[$recommend_product_id_key];
                if (isset($arrRecommendProductUnique[$recommend_product_id])) {
                    $arrErr[$recommend_product_id_key] = t('c_* The designated relevant product ID (T_ARG1) is already registered._01', array('T_ARG1' => $i));
                    
                } else {
                    $arrRecommendProductUnique[$recommend_product_id] = true;
                }
            }
        }
        // カテゴリIDの存在チェック
        if (!$this->lfIsDbRecordMulti('dtb_category', 'category_id', 'category_ids', $item, ',')) {
            $arrErr['category_ids'] = t('c_* The designated category ID is not registered._01');
        }
        // 商品ステータスIDの存在チェック
        if (!$this->lfIsArrayRecordMulti($this->arrSTATUS, 'product_statuses', $item, ',')) {
            $arrErr['product_statuses'] = t('c_* The designated product status ID is not registered._01');
        }
        // 削除フラグのチェック
        if (array_search('del_flg', $this->arrFormKeyList) !== FALSE
            && $item['del_flg'] != ''
        ) {
            if (!($item['del_flg'] == '0' or $item['del_flg'] == '1')) {
                $arrErr['del_flg'] = t("c_* Only '0' (active) and '1' (delete) are effective for the deletion flag. _01");
            }
        }
/*
    TODO: 在庫数の扱いが2.4仕様ではぶれているのでどうするか・・
        // 在庫数/在庫無制限フラグの有効性に関するチェック
        if ($item['stock'] == '') {
            if (array_search('stock_unlimited', $this->arrFormKeyList) === FALSE) {
                $arrErr['stock'] = '※ 在庫数は必須です（無制限フラグ項目がある場合のみ空欄許可）。';
            } else if ($item['stock_unlimited'] != UNLIMITED_FLG_UNLIMITED) {
                $arrErr['stock'] = '※ 在庫数または在庫無制限フラグのいずれかの入力が必須です。';
            }
        }
*/
        // ダウンロード商品チェック
        if (array_search('product_type_id', $this->arrFormKeyList) !== FALSE
            && $item['product_type_id'] == PRODUCT_TYPE_NORMAL
        ) {
            //実商品の場合
            if ($item['down_filename'] != '') {
                $arrErr['down_filename'] = t('c_* For actual products, a download file name cannot be used._01');
            }
            if ($item['down_realfilename'] != '') {
                $arrErr['down_realfilename'] = t('c_* For actual products,  file upload for the downloaded product cannot be used._01');
            }
        } elseif (array_search('product_type_id', $this->arrFormKeyList) !== FALSE
                  && $item['product_type_id'] == PRODUCT_TYPE_DOWNLOAD
        ) {
            //ダウンロード商品の場合
            if ($item['down_filename'] == '') {
                $arrErr['down_filename'] = t('c_* For downloaded products, the name of the downloaded file is required._01');
            }
            if ($item['down_realfilename'] == '') {
                $arrErr['down_realfilename'] = t('c_* For downloaded products, it is necessary to upload the file for the downloaded product._01');
            }
        }
        return $arrErr;
    }

    // TODO: ここから下のルーチンは汎用ルーチンとして移動が望ましい

    /**
     * 指定された行番号をmicrotimeに付与してDB保存用の時間を生成する。
     * トランザクション内のCURRENT_TIMESTAMPは全てcommit()時の時間に統一されてしまう為。
     *
     * @param string $line_no 行番号
     * @return string $time DB保存用の時間文字列
     */
    function lfGetDbFormatTimeWithLine($line_no = '') {
        $time = date('Y-m-d H:i:s');
        // 秒以下を生成
        if ($line_no != '') {
            $microtime = sprintf('%06d', $line_no);
            $time .= ".$microtime";
        }
        return $time;
    }

    /**
     * 指定されたキーと複数値の有効性の配列内確認
     *
     * @param string $arr チェック対象配列
     * @param string $keyname フォームキー名
     * @param array  $item 入力データ配列
     * @param string $delimiter 分割文字
     * @return boolean true:有効なデータがある false:有効ではない
     */
    function lfIsArrayRecordMulti($arr, $keyname, $item, $delimiter = ',') {
        if (array_search($keyname, $this->arrFormKeyList) === FALSE) {
            return true;
        }
        if ($item[$keyname] == '') {
            return true;
        }
        $arrItems = explode($delimiter, $item[$keyname]);
        //空項目のチェック 1つでも空指定があったら不正とする。
        if (array_search('', $arrItems) !== FALSE) {
            return false;
        }
        //キー項目への存在チェック
        foreach ($arrItems as $item) {
            if (!array_key_exists($item, $arr)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 指定されたキーと複数値の有効性のDB確認
     *
     * @param string $table テーブル名
     * @param string $tblkey テーブルキー名
     * @param string $keyname フォームキー名
     * @param array  $item 入力データ配列
     * @param string $delimiter 分割文字
     * @return boolean true:有効なデータがある false:有効ではない
     */
    function lfIsDbRecordMulti($table, $tblkey, $keyname, $item, $delimiter = ',') {
        if (array_search($keyname, $this->arrFormKeyList) === FALSE) {
            return true;
        }
        if ($item[$keyname] == '') {
            return true;
        }
        $arrItems = explode($delimiter, $item[$keyname]);
        //空項目のチェック 1つでも空指定があったら不正とする。
        if (array_search('', $arrItems) !== FALSE) {
            return false;
        }
        $count = count($arrItems);
        $where = $tblkey .' IN (' . SC_Utils_Ex::repeatStrWithSeparator('?', $count) . ')';

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $db_count = $objQuery->count($table, $where, $arrItems);
        if ($count != $db_count) {
            return false;
        }
        return true;
    }

    /**
     * 指定されたキーと値の有効性のDB確認
     *
     * @param string $table テーブル名
     * @param string $keyname キー名
     * @param array  $item 入力データ配列
     * @return boolean true:有効なデータがある false:有効ではない
     */
    function lfIsDbRecord($table, $keyname, $item) {
        if (array_search($keyname, $this->arrFormKeyList) !== FALSE  //入力対象である
            && $item[$keyname] != ''   // 空ではない
            && !$this->objDb->sfIsRecord($table, $keyname, (array)$item[$keyname]) //DBに存在するか
        ) {
            return false;
        }
        return true;
    }

    /**
     * 指定されたキーと値の有効性の配列内確認
     *
     * @param string $arr チェック対象配列
     * @param string $keyname キー名
     * @param array  $item 入力データ配列
     * @return boolean true:有効なデータがある false:有効ではない
     */
    function lfIsArrayRecord($arr, $keyname, $item) {
        if (array_search($keyname, $this->arrFormKeyList) !== FALSE //入力対象である
            && $item[$keyname] != '' // 空ではない
            && !array_key_exists($item[$keyname], $arr) //配列に存在するか
        ) {
            return false;
        }
        return true;
    }
}
