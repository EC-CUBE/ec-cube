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
require_once(CLASS_PATH . "pages/admin/LC_Page_Admin.php");

/**
 * カテゴリ登録CSVのページクラス
 *
 * LC_Page_Admin_Products_UploadCSV をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $$Id$$
 */
class LC_Page_Admin_Products_UploadCSVCategory extends LC_Page_Admin {

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

                if($arrErr['csv_file'] == "") {
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

                    $fp = fopen($enc_filepath, "r");

                    // 無効なファイルポインタが渡された場合はエラー表示
                    if ($fp === false) {
                        SC_Utils_Ex::sfDispError("");
                    }

                    // レコード数を得る
                    $rec_count = $this->lfCSVRecordCount($fp);

                    $line = 0;      // 行数
                    $regist = 0;    // 登録数

                    $objQuery = new SC_Query();
                    $objQuery->begin();

                    echo "■　CSV登録進捗状況 <br/><br/>\n";
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
                            $arrCSVErr = $this->lfCheckError($arrCSV);
                        }

                        // 入力エラーチェック
                        if (count($arrCSVErr) > 0) {
                            echo "<font color=\"red\">■" . $line . "行目でエラーが発生しました。</font></br>\n";
                            foreach($arrCSVErr as $val) {
                                $this->printError($val);
                            }
                            $err = true;
                        }

                        if (!$err) {
                            $this->lfRegistProduct($objQuery, $line,$arrCSV);
                            $regist++;
                        }
                        $arrParam = $this->objFormParam->getHashArray();

                        if (!$err) echo $line." / ".$rec_count. "行目　（カテゴリID：".$arrParam['category_id']." / カテゴリ名：".$arrParam['category_name'].")\n<br />";
                        SC_Utils_Ex::sfFlush();
                    }
                    fclose($fp);

                    if (!$err) {
                        $objQuery->commit();
                        echo "■" . $regist . "件のレコードを登録しました。";
                        // カテゴリ件数カウント関数の実行
                        $objDb->sfCategory_Count($objQuery);
                        $objDb->sfMaker_Count($objQuery);
                    } else {
                        $objQuery->rollback();
                    }
                } else {
                    foreach($arrErr as $val) {
                        $this->printError($val);
                    }
                }
                echo "<br/><a href=\"javascript:window.close()\">→閉じる</a>";
                exit;
            default:
                break;
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
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistProduct($objQuery, $line = "",$arrCSV) {
        
        $objDb = new SC_Helper_DB_Ex();
        $sqlval['category_id'] = $arrCSV[0];
        $sqlval['category_name'] = $arrCSV[1];
        $sqlval['parent_category_id'] = strlen($arrCSV[2]) ? $arrCSV[2] : 0;
        
        //存在確認
        $count = $objQuery->count("dtb_category","category_id = ?",array($sqlval['category_id']));
        $update = $count != 0;

        // 親カテゴリID、レベル
        if ($sqlval['parent_category_id'] == 0) {
            $sqlval['level'] = 1;
        } else {
            $parent_level = $objQuery->get("level", "dtb_category", "category_id = ?", array($sqlval['parent_category_id']));
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
            echo "更新　";
            $where = "category_id = ?";
            $objQuery->update("dtb_category", $sqlval, $where, array($sqlval['category_id']));
        
        // 新規登録
        } else {
            echo "登録　";
            $sqlval['create_date'] = $time;
            // ランク
            if ($sqlval['parent_category_id'] == 0) {
                // ROOT階層で最大のランクを取得する。
                $where = "parent_category_id = ?";
                $sqlval['rank'] = $objQuery->max("dtb_category", "rank", $where, array($sqlval['parent_category_id'])) + 1;
            } else {
                // 親のランクを自分のランクとする。
                $where = "category_id = ?";
                $sqlval['rank'] = $objQuery->get("rank", "dtb_category", $where, array($sqlval['parent_category_id']));
                // 追加レコードのランク以上のレコードを一つあげる。
                $sqlup = "UPDATE dtb_category SET rank = rank + 1 WHERE rank >= ?";
                $objQuery->exec($sqlup, array($sqlval['rank']));
                
            }
            $sqlval['category_id'] = $objQuery->nextVal('dtb_category_category_id');
            $objQuery->insert("dtb_category", $sqlval);
        }
    }

    /**
     * 入力チェックを行う.
     *
     * @return void
     */
    function lfCheckError($arrCSV) {
//        $arrRet =  $this->objFormParam->getHashArray();
        $arrRet['category_id'] = $arrCSV[0];
        $arrRet['category_name'] = $arrCSV[1];
        $arrRet['parent_category_id'] = $arrCSV[2];
        
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
            $level = $objQuery->get("level", "dtb_category", "category_id = ?", array($parent_category_id));
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
            }
            // 編集中のレコード以外に同じ名称が存在する場合
            if ($arrCat[0]['category_id'] != $arrRet['category_id'] && $arrCat[0]['category_name'] == $arrRet['category_name']) {
                echo $arrCat[0]['category_id'];
                echo "#######--------- line is ".__LINE__." on ".__FILE__."--------########<br/>";
                        
                echo $arrRet['category_id'];
                echo "#######--------- line is ".__LINE__." on ".__FILE__."--------########<br/>";
                        
                echo  $arrCat[0]['category_name'] ;
                echo "#######--------- line is ".__LINE__." on ".__FILE__."--------########<br/>";
                        echo  $arrRet['category_name'];
                        echo "#######--------- line is ".__LINE__." on ".__FILE__."--------########<br/>";
                                
                
                $objErr->arrErr['category_name'] = "※ 既に同じ内容の登録が存在します。</br>";
                
            }
        }
        return $objErr->arrErr;
    }

    /**
     * CSVのカウント数を得る.
     *
     * @param resource $fp fopenを使用して作成したファイルポインタ
     * @return integer CSV のカウント数
     */
    function lfCSVRecordCount($fp) {
        $count = 0;
        while(!feof($fp)) {
            $arrCSV = fgetcsv($fp, CSV_LINE_MAX);
            $count++;
        }
        // ファイルポインタを戻す
        if (rewind($fp)) {
            return $count-1;
        } else {
            SC_Utils_Ex::sfDispError("");
        }
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
