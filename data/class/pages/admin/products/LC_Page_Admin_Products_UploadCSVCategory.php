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
 * カテゴリ登録CSVのページクラス
 *
 * LC_Page_Admin_Products_UploadCSV をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $$Id$$
 */
class LC_Page_Admin_Products_UploadCSVCategory extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    var $arrErr;

    var $arrTitle;

    var $arrRowResult;

    var $arrRowErr;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/upload_csv_category.tpl';
        $this->tpl_subnavi  = 'products/subnavi.tpl';
        $this->tpl_mainno   = 'products';
        $this->tpl_subno    = 'upload_csv_category';
        $this->tpl_subtitle = 'カテゴリ登録CSV';
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
        $objDb        = new SC_Helper_DB_Ex();
        $objUpFile    = new SC_UploadFile(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        $objFormParam = new SC_FormParam();

        // ファイルオブジェクト初期化
        $this->initFile($objUpFile);

        // 入力パラメータ初期化
        $this->initParam($objFormParam);
        $objFormParam->setHtmlDispNameArray();
        $this->arrTitle = $objFormParam->getHtmlDispNameArray();

        switch ($this->getMode()) {
        case 'csv_upload':
            $this->doUploadCsv($objFormParam, $objUpFile, $objDb);
        break;
        default:
        }
    }

    /**
     * CSVアップロードを実行する
     *
     * @param SC_FormParam  $objFormParam
     * @param SC_UploadFile $objUpFile
     * @param SC_Helper_DB  $objDb
     * @return void
     */
    function doUploadCsv(&$objFormParam, &$objUpFile, &$objDb) {
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
        $fp = fopen($enc_filepath, "r");
        // 失敗した場合はエラー表示
        if (!$fp) {
             SC_Utils_Ex::sfDispError("");
        }

        // 登録対象の列数
        $col_max_count = $objFormParam->getCount();
        // 行数
        $line_count = 0;

        $objQuery = SC_Query::getSingletonInstance();
        $objQuery->begin();

        $errFlg = false;
        
        while (!feof($fp)) {
            $arrRow = fgetcsv($fp, CSV_LINE_MAX);
            $line_count++;

            // ヘッダ行はスキップ
            if ($line_count == 1) {
                continue;
            }
            // 空行はスキップ
            if (empty($arrRow)) {
                continue;
            }
            // 列数が異なる場合はエラー
            if ($col_max_count != count($arrRow)) {
                $errFlg = true;
                break;
            }
            // 数値インデックスから, カラム名 => 値の連想配列へ変換
            $objFormParam->setParam($arrRow, true);
            $arrRow = $objFormParam->getHashArray();
            $objFormParam->setParam($arrRow);
            $objFormParam->convParam();
            // 入力項目チェック
            $arrErr = $objFormParam->checkError();
            if (count($arrErr) > 0) {
                foreach ($arrErr as $err) {
                    $this->addRowErr($line_count, $err);
                }
                $errFlg = true;
                break;
            }

            // 親カテゴリIDがない場合はルートのカテゴリIDをセット
            if ($arrRow['parent_category_id'] == '') {
                $arrRow['parent_category_id'] = 0;
            }

            // 親カテゴリIDの存在チェック
            $count = $objQuery->count("dtb_category", "category_id = ?", array($arrRow['parent_category_id']));
            if ($arrRow['parent_category_id'] != 0 && $count == 0) {
                $errFlg = true;
                $this->addRowErr($line_count, "指定の親カテゴリID(" . $arrRow['parent_category_id'] . ")は、存在しません。");
                break;
            }
            
            $count = $objQuery->count("dtb_category", "category_id = ?", array($arrRow['category_id']));

            // 編集
            if ($count > 0) {
                // 重複チェック
                $where = "parent_category_id = ? AND category_id <> ? AND category_name = ?";
                $count = $objQuery->count("dtb_category",
                                $where,
                                array($arrRow['parent_category_id'],
                                      $arrRow['category_id'],
                                      $arrRow['category_name']));
                if ($count > 0) {
                    $errFlg = true;
                    $this->addRowErr($line_count, "既に同じ内容の登録が存在します。");
                    break;
                }

                // カテゴリ更新
                $arrCategory = array();
                $arrCategory['category_name'] = $arrRow['category_name'];
                $arrCategory['update_date'] = 'NOW()';
                $where = "category_id = ?";
                $objQuery->update("dtb_category", $arrCategory, $where, array($arrRow['category_id']));
                
                $message = "[更新] カテゴリID: " . $arrRow['category_id'] . " カテゴリ名 : " . $arrRow['category_name'];
                $this->addRowResult($line_count, $message);
            // 登録
            } else {
                // 登録数上限チェック
                $where = "del_flg = 0";
                $count = $objQuery->count("dtb_category", $where);
                if ($count >= CATEGORY_MAX) {
                    $errFlg = true;
                    $this->addRowErr($line_count, "カテゴリの登録最大数を超えました。");
                    break;
                }
                // 階層上限チェック
                if ($this->isOverLevel($arrRow['parent_category_id'])) {
                    $errFlg = true;
                    $this->addRowErr($line_count, LEVEL_MAX . "階層以上の登録はできません。");
                    break;
                }
                // 重複チェック
                $where = "parent_category_id = ? AND category_name = ?";
                $count = $objQuery->count("dtb_category",
                                $where,
                                array($arrRow['parent_category_id'],
                                      $arrRow['category_name']));
                if ($count > 0) {
                    $errFlg = true;
                    $this->addRowErr($line_count, "既に同じ内容の登録が存在します。");
                    break;
                }
                // カテゴリ登録
                $this->registerCategory($arrRow['parent_category_id'],
                                        $arrRow['category_name'],
                                        $_SESSION['member_id']);

                $message = "[登録] カテゴリ名 : " . $arrRow['category_name'];
                $this->addRowResult($line_count, $message);
            }
        }
        
        fclose($fp);

        if ($errFlg) {
            $objQuery->rollback();
            return;
        }

        $objQuery->commit();

        // カテゴリ件数を更新
        $objDb->sfCountCategory($objQuery);
        $objDb->sfCountMaker($objQuery);
    }

    /**
     * 登録/編集結果のメッセージをプロパティへ追加する
     *
     * @param integer $line_count 行数
     * @param stirng $message メッセージ
     * @return void
     */
    function addRowResult($line_count, $message) {
        $this->arrRowResult[] = $line_count . "行目：" . $message;
    }

    /**
     * 登録/編集結果のエラーメッセージをプロパティへ追加する
     *
     * @param integer $line_count 行数
     * @param stirng $message メッセージ
     * @return void
     */
    function addRowErr($line_count, $message) {
        $this->arrRowErr[] = $line_count . "行目：" . $message;
    }

    /**
     * カテゴリの階層が上限を超えているかを判定する
     *
     * @param integer 親カテゴリID
     * @param 超えている場合 true
     */
    function isOverLevel($parent_category_id) {
        $objQuery =& SC_Query::getSingletonInstance();
        $level = $objQuery->get("level", "dtb_category", "category_id = ?", array($parent_category_id));
        return $level >= LEVEL_MAX;
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
    function initFile(&$objUpFile) {
        $objUpFile->addFile("CSVファイル", 'csv_file', array('csv'), CSV_SIZE, true, 0, 0, false);
    }

    /**
     * 入力情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam 
     * @return void
     */
    function initParam(&$objFormParam) {
        $objFormParam->addParam("カテゴリID","category_id",INT_LEN,"n",array("MAX_LENGTH_CHECK","NUM_CHECK"));
        $objFormParam->addParam("カテゴリ名","category_name",STEXT_LEN,"KVa",array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam("親カテゴリID","parent_category_id",INT_LEN,"n",array("MAX_LENGTH_CHECK","NUM_CHECK"));
    }

    /**
     * カテゴリを登録する
     *
     * @param integer 親カテゴリID
     * @param string カテゴリ名
     * @param integer 作成者のID
     * @return void
     */
    function registerCategory($parent_category_id, $category_name, $creator_id) {
        $objQuery =& SC_Query::getSingletonInstance();

        $rank = null;
        if ($parent_category_id == 0) {
            // ROOT階層で最大のランクを取得する。
            $where = "parent_category_id = ?";
            $rank = $objQuery->max("rank", "dtb_category", $where, array($parent_category_id)) + 1;
        } else {
            // 親のランクを自分のランクとする。
            $where = "category_id = ?";
            $rank = $objQuery->get("rank", "dtb_category", $where, array($parent_category_id));
            // 追加レコードのランク以上のレコードを一つあげる。
            $sqlup = "UPDATE dtb_category SET rank = (rank + 1) WHERE rank >= ?";
            $objQuery->exec($sqlup, array($rank));
        }

        $where = "category_id = ?";
        // 自分のレベルを取得する(親のレベル + 1)
        $level = $objQuery->get("level", "dtb_category", $where, array($parent_category_id)) + 1;

        $arrCategory = array();
        $arrCategory['category_name'] = $category_name;
        $arrCategory['parent_category_id'] = $parent_category_id;
        $arrCategory['create_date'] = "Now()";
        $arrCategory['update_date'] = "Now()";
        $arrCategory['creator_id']  = $creator_id;
        $arrCategory['rank']        = $rank;
        $arrCategory['level']       = $level;
        $arrCategory['category_id'] = $objQuery->nextVal('dtb_category_category_id');
        $objQuery->insert("dtb_category", $arrCategory);
    }
}
