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
 * カテゴリ登録CSVのページクラス
 *
 * LC_Page_Admin_Products_UploadCSV をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $$Id$$
 */
class LC_Page_Admin_Products_UploadCSVCategory extends LC_Page_Admin_Ex {

    // {{{ properties
    /** エラー情報 **/
    var $arrErr;

    /** 表示用項目 **/
    var $arrTitle;

    /** 結果行情報 **/
    var $arrRowResult;

    /** エラー行情報 **/
    var $arrRowErr;

    /** TAGエラーチェックフィールド情報 */
    var $arrTagCheckItem;

    /** テーブルカラム情報 (登録処理用) **/
    var $arrRegistColumn;

    /** 登録フォームカラム情報 **/
    var $arrFormKeyList;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/upload_csv_category.tpl';
        $this->tpl_mainno   = 'products';
        $this->tpl_subno    = 'upload_csv_category';
        $this->tpl_maintitle = t('TPL_MAINTITLE_007');
        $this->tpl_subtitle = t('LC_Page_Admin_Products_UploadCSVCategory_001');
        $this->csv_id = '5';

        $masterData = new SC_DB_MasterData_Ex();
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
        $this->arrRowResult[] = t('LC_Page_Admin_Products_UploadCSVCategory_002', array('T_ARG1' => $line_count, 'T_ARG2' => $message));
    }

    /**
     * 登録/編集結果のエラーメッセージをプロパティへ追加する
     *
     * @param integer $line_count 行数
     * @param stirng $message メッセージ
     * @return void
     */
    function addRowErr($line_count, $message) {
        $this->arrRowErr[] = t('LC_Page_Admin_Products_UploadCSVCategory_002', array('T_ARG1' => $line_count, 'T_ARG2' => $message));
    }

    /**
     * CSVアップロードを実行する
     *
     * @param SC_FormParam  $objFormParam
     * @param SC_UploadFile $objUpFile
     * @param SC_Helper_DB  $objDb
     * @return void
     */
    function doUploadCsv(&$objFormParam, &$objUpFile) {
        // ファイルアップロードのチェック
        $objUpFile->makeTempFile('csv_file');
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

        while (!feof($fp)) {
            $arrCSV = fgetcsv($fp, CSV_LINE_MAX);
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
                $this->addRowErr($line_count, t('LC_Page_Admin_Products_UploadCSVCategory_003', array('T_ARG1' => $col_count, 'T_ARG2' => $col_max_count)));
                
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

            $category_id = $this->lfRegistCategory($objQuery, $line_count, $objFormParam);
            $this->addRowResult($line_count, t('LC_Page_Admin_Products_UploadCSVCategory_004', array('T_ARG1' => $category_id, 'T_ARG2' => $objFormParam->getValue('category_name'))));
        }

        // 実行結果画面を表示
        $this->tpl_mainpage = 'products/upload_csv_category_complete.tpl';

        fclose($fp);

        if ($errFlag) {
            $objQuery->rollback();
            return;
        }

        $objQuery->commit();

        // カテゴリ件数を更新
        SC_Helper_DB_EX::sfCountCategory($objQuery);
        return;
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
            if (preg_match_all('/\(.+\) as (.+)$/i', $item['col'], $match, PREG_SET_ORDER)) {
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
        $this->arrRegistColumn = $objQuery->listTableFields('dtb_category');
    }

    /**
     * カテゴリ登録を行う.
     *
     * FIXME: 登録の実処理自体は、LC_Page_Admin_Products_Categoryと共通化して欲しい。
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return integer カテゴリID
     */
    function lfRegistCategory($objQuery, $line, &$objFormParam) {
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();
        // 登録時間を生成(DBのCURRENT_TIMESTAMPだとcommitした際、すべて同一の時間になってしまう)
        $arrList['update_date'] = $this->lfGetDbFormatTimeWithLine($line);

        // 登録情報を生成する。
        // テーブルのカラムに存在しているもののうち、Form投入設定されていないデータは上書きしない。
        $sqlval = SC_Utils_Ex::sfArrayIntersectKeys($arrList, $this->arrRegistColumn);

        // 必須入力では無い項目だが、空文字では問題のある特殊なカラム値の初期値設定
        $sqlval = $this->lfSetCategoryDefaultData($sqlval);

        if ($sqlval['category_id'] != '') {
            // 同じidが存在すればupdate存在しなければinsert
            $where = 'category_id = ?';
            $category_exists = $objQuery->exists('dtb_category', $where, array($sqlval['category_id']));
            if ($category_exists) {
                // UPDATEの実行
                $where = 'category_id = ?';
                $objQuery->update('dtb_category', $sqlval, $where, array($sqlval['category_id']));
            } else {
                $sqlval['create_date'] = $arrList['update_date'];
                // 新規登録
                $category_id = $this->registerCategory($sqlval['parent_category_id'],
                                        $sqlval['category_name'],
                                        $_SESSION['member_id'],
                                        $sqlval['category_id']);
            }
            $category_id = $sqlval['category_id'];
            // TODO: 削除時処理
        } else {
            // 新規登録
            $category_id = $this->registerCategory($sqlval['parent_category_id'],
                                        $sqlval['category_name'],
                                        $_SESSION['member_id']);
        }
        return $category_id;
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
                case 'parent_category_id':
                    $arrCSVFrame[$key]['default'] = '0';
                    break;
                case 'del_flg':
                    $arrCSVFrame[$key]['default'] = '0';
                    break;
                default:
                    break;
            }
        }
        return $arrCSVFrame;
    }

    /**
     * データ登録前に特殊な値の持ち方をする部分のデータ部分の初期値補正を行う
     *
     * @param array $sqlval 商品登録情報配列
     * @return $sqlval 登録情報配列
     */
    function lfSetCategoryDefaultData(&$sqlval) {
        if ($sqlval['del_flg'] == '') {
            $sqlval['del_flg'] = '0'; //有効
        }
        if ($sqlval['creator_id'] == '') {
            $sqlval['creator_id'] = $_SESSION['member_id'];
        }
        if ($sqlval['parent_category_id'] == '') {
            $sqlval['parent_category_id'] = (string)'0';
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
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        /*
        // カテゴリIDの存在チェック
        if (!$this->lfIsDbRecord('dtb_category', 'category_id', $item)) {
            $arrErr['category_id'] = '※ 指定のカテゴリIDは、登録されていません。';
        }
        */
        // 親カテゴリIDの存在チェック
        if (array_search('parent_category_id', $this->arrFormKeyList) !== FALSE
            && $item['parent_category_id'] != ''
            && $item['parent_category_id'] != '0'
            && !SC_Helper_DB_Ex::sfIsRecord('dtb_category', 'category_id', array($item['parent_category_id']))
        ) {
            $arrErr['parent_category_id'] = t('LC_Page_Admin_Products_UploadCSVCategory_005', array('T_ARG1' => $item['parent_category_id']));
        }
        // 削除フラグのチェック
        if (array_search('del_flg', $this->arrFormKeyList) !== FALSE
            && $item['del_flg'] != ''
        ) {
            if (!($item['del_flg'] == '0' or $item['del_flg'] == '1')) {
                $arrErr['del_flg'] = t('LC_Page_Admin_Products_UploadCSVCategory_006');
            }
        }
        // 重複チェック 同じカテゴリ内に同名の存在は許可されない
        if (array_search('category_name', $this->arrFormKeyList) !== FALSE
            && $item['category_name'] != ''
        ) {
            $parent_category_id = $item['parent_category_id'];
            if ($parent_category_id == '') {
                $parent_category_id = (string)'0';
            }
            $where = 'parent_category_id = ? AND category_id <> ? AND category_name = ?';
            $exists = $objQuery->exists('dtb_category',
                        $where,
                        array($parent_category_id,
                                $item['category_id'],
                                $item['category_name']));
            if ($exists) {
                $arrErr['category_name'] = t('LC_Page_Admin_Products_UploadCSVCategory_007');
            }
        }
        // 登録数上限チェック
        $where = 'del_flg = 0';
        $count = $objQuery->count('dtb_category', $where);
        if ($count >= CATEGORY_MAX) {
            $item['category_name'] = t('LC_Page_Admin_Products_UploadCSVCategory_008');
        }
        // 階層上限チェック
        if (array_search('parent_category_id', $this->arrFormKeyList) !== FALSE
                and $item['parent_category_id'] != '') {
            $level = $objQuery->get('level', 'dtb_category', 'category_id = ?', array($parent_category_id));
            if ($level >= LEVEL_MAX) {
                $arrErr['parent_category_id'] = t('LC_Page_Admin_Products_UploadCSVCategory_009', array('T_ARG1' => LEVEL_MAX));
            }
        }
        return $arrErr;
    }

    /**
     * カテゴリを登録する
     *
     * @param integer 親カテゴリID
     * @param string カテゴリ名
     * @param integer 作成者のID
     * @param integer 指定カテゴリID
     * @return integer カテゴリID
     */
    function registerCategory($parent_category_id, $category_name, $creator_id, $category_id = null) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $rank = null;
        if ($parent_category_id == 0) {
            // ROOT階層で最大のランクを取得する。
            $where = 'parent_category_id = ?';
            $rank = $objQuery->max('rank', 'dtb_category', $where, array($parent_category_id)) + 1;
        } else {
            // 親のランクを自分のランクとする。
            $where = 'category_id = ?';
            $rank = $objQuery->get('rank', 'dtb_category', $where, array($parent_category_id));
            // 追加レコードのランク以上のレコードを一つあげる。
            $where = 'rank >= ?';
            $arrRawSql = array(
                'rank' => '(rank + 1)',
            );
            $objQuery->update('dtb_category', array(), $where, array($rank), $arrRawSql);
        }

        $where = 'category_id = ?';
        // 自分のレベルを取得する(親のレベル + 1)
        $level = $objQuery->get('level', 'dtb_category', $where, array($parent_category_id)) + 1;

        $arrCategory = array();
        $arrCategory['category_name'] = $category_name;
        $arrCategory['parent_category_id'] = $parent_category_id;
        $arrCategory['create_date'] = 'CURRENT_TIMESTAMP';
        $arrCategory['update_date'] = 'CURRENT_TIMESTAMP';
        $arrCategory['creator_id']  = $creator_id;
        $arrCategory['rank']        = $rank;
        $arrCategory['level']       = $level;
        //カテゴリIDが指定されていればそれを利用する
        if (isset($category_id)) {
            $arrCategory['category_id'] = $category_id;
            // シーケンスの調整
            $seq_count = $objQuery->currVal('dtb_category_category_id');
            if ($seq_count < $arrCategory['category_id']) {
                $objQuery->setVal('dtb_category_category_id', $arrCategory['category_id'] + 1);
            }
        } else {
            $arrCategory['category_id'] = $objQuery->nextVal('dtb_category_category_id');
        }
        $objQuery->insert('dtb_category', $arrCategory);

        return $arrCategory['category_id'];
    }

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
            && !SC_Helper_DB_EX::sfIsRecord($table, $keyname, (array)$item[$keyname]) //DBに存在するか
        ) {
            return false;
        }
        return true;
    }

}
