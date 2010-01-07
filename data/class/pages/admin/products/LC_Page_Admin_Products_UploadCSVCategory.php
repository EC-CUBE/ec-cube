<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * カテゴリ登録CSVのページクラス
 *
 * LC_Page_Admin_Products_UploadCSV をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $$Id$$
 */
class LC_Page_Admin_Products_UploadCSVCategory extends LC_Page {

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
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'upload_csv_category';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $conn = new SC_DBConn();
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objDb = new SC_Helper_DB_Ex();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // ファイル管理クラス
        $this->objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
        // ファイル情報の初期化
        $this->lfInitFile();
        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        $colmax = $this->objFormParam->getCount();
        $this->objFormParam->setHtmlDispNameArray();
        $this->arrTitle = $this->objFormParam->getHtmlDispNameArray();

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch ($_POST['mode']) {
            case 'csv_upload':
                $err = false;
                // エラーチェック
                $arrErr['csv_file'] = $this->objUpFile->makeTempFile('csv_file');

                if($arrErr['css_file'] == "") {
                    $arrErr = $this->objUpFile->checkEXISTS();
                }

                // 実行時間を制限しない
                set_time_limit(0);

                // 出力をバッファリングしない(==日本語自動変換もしない)
                ob_end_clean();

                // IEのために256バイト空文字出力
                echo str_pad('',256);

                if (empty($arrErr['csv_file'])) {
                    // 一時ファイル名の取得
                    $filepath = $this->objUpFile->getTempFilePath('csv_file');
                    // エンコード
                    $enc_filepath = SC_Utils_Ex::sfEncodeFile($filepath,
                    CHAR_CODE, CSV_TEMP_DIR);

                    // レコード数を得る
                    $rec_count = $this->lfCSVRecordCount($enc_filepath);
                    $fp = fopen($enc_filepath, "r");

                    if ($rec_count === false || $fp === false) {
                        $err = false;
                        $arrErr['bad_file_pointer'] = "※ 不正なファイルポインタが検出されました";
                    }

                    $line = 0;      // 行数
                    $regist = 0;    // 登録数

                    $objQuery = new SC_Query();
                    $objQuery->begin();

                    echo "■　CSV登録進捗状況 <br/><br/>\n";
                    if ($fp !== false) {
                        while (!feof($fp) && !$err) {
                            $arrCSV = fgetcsv($fp, CSV_LINE_MAX);

                            // 行カウント
                            $line++;

                            if ($line <= 1) {
                                continue;
                            }

                            // 項目数カウント
                            $max = count($arrCSV);

                            // 項目数が1以下の場合は無視する
                            if ($max <= 1) {
                                continue;
                            }

                            // 項目数チェック
                            if ($max != $colmax) {
                                echo "※ 項目数が" . $max . "個検出されました。項目数は" . $colmax . "個になります。</br>\n";
                                $err = true;
                            } else {
                                // シーケンス配列を格納する。
                                $this->objFormParam->setParam($arrCSV, true);
                                $arrRet = $this->objFormParam->getHashArray();
                                $this->objFormParam->setParam($arrRet);
                                // 入力値の変換
                                $this->objFormParam->convParam();
                                // <br>なしでエラー取得する。
                                $arrCSVErr = $this->lfCheckError();
                            }

                            // 入力エラーチェック
                            if (count_chars(string[, int mode])($arrCSVErr) > 0) {
                                echo "<font color=\"red\">■" . $line . "行目でエラーが発生しました。</font></br>\n";
                                foreach($arrCSVErr as $val) {
                                    $this->printError($val);
                                }
                                $err = true;
                            }

                            if (!$err) {
                                $this->lfRegistProduct($objQuery, $rec_count, $line);
                                $regist++;
                            }
                            $arrParam = $this->objFormParam->getHashArray();

                            if (!$err) echo $line." / ".$rec_count. "行目　（カテゴリID：".$arrParam['category_id']." / カテゴリ名：".$arrParam['category_name'].")\n<br />";
                            flush();
                        }
                        fclose($fp);
                    }

                    if (!$err) {
                        $objQuery->commit();
                        echo "■" . $regist . "件のレコードを登録しました。";
                        // カテゴリ件数カウント関数の実行
                        $objDb->sfCategory_Count($objQuery);
                    } else {
                        $objQuery->rollback();
                    }
                } else {
                    foreach($arrErr as $val) {
                        $this->printError($val);
                    }
                }
                echo "<br/><a href=\"javascript:window.close()\">→閉じる</a>";
                flush();
                exit;
                break;
        default:
            break;
        }

        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
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
    function lfInitFile() {
        $this->objUpFile->addFile("CSVファイル", 'csv_file', array('csv'), CSV_SIZE, true, 0, 0, false);
    }

    /**
     * 入力情報の初期化を行う.
     *
     * @return void
     */
    function lfInitParam() {
        $this->objFormParam->addParam("カテゴリID","category_id",INT_LEN,"n",array("MAX_LENGTH_CHECK","NUM_CHECK"));
        $this->objFormParam->addParam("カテゴリ名","category_name",STEXT_LEN,"KVa",array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("親カテゴリID","parent_category_id",INT_LEN,"n",array("MAX_LENGTH_CHECK","NUM_CHECK"));
    }

    /**
     * カテゴリ登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $rec_count 処理総数|integer $line 処理中の行数
     * @return void
     */
    function lfRegistProduct($objQuery, $rec_count, $line = "") {
        $objDb = new SC_Helper_DB_Ex();
        $arrRet = $this->objFormParam->getHashArray();

        //カテゴリID
        if ($arrRet['category_id'] == 0) {
            $category_id = $objQuery->max("dtb_category", "category_id") + 1;
            $sqlval['category_id'] = $category_id;
            $update = false;
        } else {
            $sqlval['category_id'] = $arrRet['category_id'];
            $update = true;
        }

        // カテゴリ名
        $sqlval['category_name'] = $arrRet['category_name'];
        //表示ランク（上から順に表示順を自動割り当て）
        $sqlval['rank'] = ($rec_count + 1) - $line ;

        // 親カテゴリID、レベル
        if ($arrRet['parent_category_id'] == 0) {
            $sqlval['parent_category_id'] = "0";
            $sqlval['level'] = 1;
        } else {
            $sqlval['parent_category_id'] = $arrRet['parent_category_id'];
            $parent_level = $objQuery->get("dtb_category", "level", "category_id = ?", array($sqlval['parent_category_id']));
            $sqlval['level'] = $parent_level + 1;
        }

        // その他
        $time = date("Y-m-d H:i:s");
        if ($line != "") {
            $microtime = sprintf("%06d", $line);
            $time .= ".$microtime";
        }
        $sqlval['update_date'] = $time;
        $sqlval['creator_id'] = $_SESSION['member_id'];

        // 更新
        if ($update) {
            echo "UPDATE　";
            $where = "category_id = ?";
            $objQuery->update("dtb_category", $sqlval, $where, array($sqlval['category_id']));

        // 新規登録
        } else {
            echo "INSERT　";
            $sqlval['create_date'] = $time;
            $objQuery->insert("dtb_category", $sqlval);
        }
    }

    /**
     * 入力チェックを行う.
     *
     * @return void
     */
    function lfCheckError() {
        $arrRet =  $this->objFormParam->getHashArray();
        $objQuery = new SC_Query();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError(false);

        // 親カテゴリID設定
        if ($arrRet['parent_category_id'] == 0) {
            $parent_category_id = "0";
        } else {
            $parent_category_id = $arrRet['parent_category_id'];
        }

        // 存在する親カテゴリIDかチェック
        if (count($objErr->arrErr) == 0) {
            if ($parent_category_id != 0){
                $count = $objQuery->count("dtb_category", "category_id = ?", array($parent_category_id));
                if ($count == 0) {
                    $objErr->arrErr['parent_category_id'] = "※ 指定の親カテゴリID(".$parent_category_id.")は、存在しません。";
                }
            }
        }

        // 階層チェック
        if (!isset($objErr->arrErr['category_name']) && !isset($objErr->arrErr['parent_category_id'])) {
            $level = $objQuery->get("dtb_category", "level", "category_id = ?", array($parent_category_id));
            if ($level >= LEVEL_MAX) {
                $objErr->arrErr['category_name'] = "※ ".LEVEL_MAX."階層以上の登録はできません。<br>";
            }
        }

        // 重複チェック
        if (!isset($objErr->arrErr['category_name']) && !isset($objErr->arrErr['parent_category_id'])) {
            $where = "parent_category_id = ? AND category_name = ?";
            $arrCat = $objQuery->select("category_id, category_name", "dtb_category", $where, array($parent_category_id, $arrRet['category_name']));
            if (empty($arrCat)) {
                $arrCat = array(array("category_id" => "", "category_name" => ""));
            // 編集中のレコード以外に同じ名称が存在する場合
            }elseif ($arrCat[0]['category_id'] != $arrRet['category_id'] && $arrCat[0]['category_name'] == $arrRet['category_name']) {
                $objErr->arrErr['category_name'] = "※ 既に同じ内容の登録が存在します。\n";
            }
        }
        return $objErr->arrErr;
    }

    /**
     * CSVのカウント数を得る.
     *
     * @param string $file_name ファイルパス
     * @return mixed CSV のカウント数; $file_name が無効な場合は false
     */
    function lfCSVRecordCount($file_name) {
        $count = 0;
        $fp = fopen($file_name, "r");
        if ($fp !== false) {
            while(!feof($fp)) {
                $arrCSV = fgetcsv($fp, CSV_LINE_MAX);
                $count++;
            }
        } else {
            return false;
        }
        return $count-1;
    }

    /**
     * 引数の文字列をエラー出力する.
     *
     * 引数 $val の内容は, htmlspecialchars() によってサニタイズされ
     *
     * @param string $val 出力する文字列
     * @return void
     */
    function printError($val) {
        echo "<font color=\"red\">"
        . htmlspecialchars($val, ENT_QUOTES)
        . "</font></br>\n";
    }
}
?>