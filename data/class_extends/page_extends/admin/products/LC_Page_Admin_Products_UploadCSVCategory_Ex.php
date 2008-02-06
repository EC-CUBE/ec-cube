<?php

// {{{ requires
require_once(CLASS_PATH . "pages/admin/products/LC_Page_Admin_Products_UploadCSV.php");

/**
 * CSV アップロード のページクラス(拡張)
 *
 * LC_Page_Admin_Products_UploadCSV をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $$Id: LC_Page_Admin_Products_UploadCSV_Ex.php 16741 2007-11-08 00:43:24Z adachi $$
 */
class LC_Page_Admin_Products_UploadCSVCategory_Ex extends LC_Page_Admin_Products_UploadCSV {

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

        switch($_POST['mode']) {
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

                if(empty($arrErr['csv_file'])) {
                    // 一時ファイル名の取得
                    $filepath = $this->objUpFile->getTempFilePath('csv_file');
                    // エンコード
                    $enc_filepath = SC_Utils_Ex::sfEncodeFile($filepath,
                    CHAR_CODE, CSV_TEMP_DIR);

                    // レコード数を得る
                    $rec_count = $this->lfCSVRecordCount($enc_filepath);

                    $fp = fopen($enc_filepath, "r");
                    $line = 0;      // 行数
                    $regist = 0;    // 登録数

                    $objQuery = new SC_Query();
                    $objQuery->begin();

                    echo "■　CSV登録進捗状況 <br/><br/>\n";

                    while(!feof($fp) && !$err) {
                        $arrCSV = fgetcsv($fp, CSV_LINE_MAX);

                        // 行カウント
                        $line++;

                        if($line <= 1) {
                            continue;
                        }

                        // 項目数カウント
                        $max = count($arrCSV);

                        // 項目数が1以下の場合は無視する
                        if($max <= 1) {
                            continue;
                        }

                        // 項目数チェック
                        if($max != $colmax) {
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
                        if(count($arrCSVErr) > 0) {
                            echo "<font color=\"red\">■" . $line . "行目でエラーが発生しました。</font></br>\n";
                            foreach($arrCSVErr as $val) {
                                $this->printError($val);
                            }
                            $err = true;
                        }

                        if(!$err) {
                            $this->lfRegistProduct($objQuery, $line);
                            $regist++;
                        }
                        $arrParam = $this->objFormParam->getHashArray();

                        if(!$err) echo $line." / ".$rec_count. "行目　（カテゴリID：".$arrParam['category_id']." / カテゴリ名：".$arrParam['category_name'].")\n<br />";
                        flush();
                    }
                    fclose($fp);

                    if(!$err) {
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
        $this->objUpFile->addFile("CSVファイル", 'csv_file', array('csv'),
        CSV_SIZE, true, 0, 0, false);
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
        $this->objFormParam->addParam("階層","level",INT_LEN,"n",array("MAX_LENGTH_CHECK","NUM_CHECK"));
        $this->objFormParam->addParam("表示順","rank",INT_LEN,"n",array("MAX_LENGTH_CHECK","NUM_CHECK"));
        //     $this->objFormParam->addParam("削除フラグ","del_flg",INT_LEN,"n",array());
    }

    /**
     * カテゴリ登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistProduct($objQuery, $line = "") {
        $objDb = new SC_Helper_DB_Ex();
        $arrRet = $this->objFormParam->getHashArray();
        foreach($arrRet as $key => $val) {
            switch($key) {
                //             case 'recommend_comment6':
                //                 break;
                default:
                    if(!ereg("^dummy", $key)) {
                        $sqlval[$key] = $val;
                    }
                    break;
            }
        }
            // 登録時間を生成(DBのnow()だとcommitした際、すべて同一の時間になってしまう)
        $time = date("Y-m-d H:i:s");
        // 秒以下を生成
        if($line != "") {
            $microtime = sprintf("%06d", $line);
            $time .= ".$microtime";
        }
        $sqlval['update_date'] = $time;
        $sqlval['creator_id'] = $_SESSION['member_id'];
        $count = $objQuery->count("dtb_category", "category_id = ?", array($arrRet['category_id']));
        if($count != 0) {
            // UPDATEの実行
            echo "UPDATEの実行";
            $where = "category_id = ?";
            $objQuery->update("dtb_category", $sqlval, $where, array($sqlval['category_id']));
        } else {
            // 新規登録
            $sqlval['category_id'] = $arrRet['category_id'];
            $sqlval['create_date'] = $time;
            if($arrRet['rank'] == ""){
                // カテゴリ内で最大のランクを割り当てる
                $sqlval['rank'] = $objQuery->max("dtb_category", "rank", "parent_category_id = ?", array($arrRet['parent_category_id'])) + 1;
            }
            // INSERTの実行
            $objQuery->insert("dtb_category", $sqlval);
        }
    }

    /**
     * 入力チェックを行う.
     *
     * @return void
     */
    function lfCheckError() {
        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError(false);
        if(count($objErr->arrErr) == 0) {
            $objQuery = new SC_Query();
            // 存在する親カテゴリIDかチェック
            if($arrRet['parent_category_id'] != 0){
                $count = $objQuery->count("dtb_category", "category_id = ?", array($arrRet['parent_category_id']));
                if($count == 0) {
                    $objErr->arrErr['parent_category_id'] = "※ 指定の親カテゴリID(".$arrRet['parent_category_id'].")は、存在しません。";
                }
            }
        }
        return $objErr->arrErr;
    }

    /**
     * CSVのカウント数を得る.
     *
     * @param string $file_name ファイルパス
     * @return integer CSV のカウント数
     */
    function lfCSVRecordCount($file_name) {

        $count = 0;
        $fp = fopen($file_name, "r");
        while(!feof($fp)) {
            $arrCSV = fgetcsv($fp, CSV_LINE_MAX);
            $count++;
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
