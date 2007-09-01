<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * CSV アップロード のページクラス.
 *
 * :XXX: 要テスト
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Admin_Products_UploadCSV.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Admin_Products_UploadCSV extends LC_Page {

    // }}}
    // {{{ functions

    /** フォームパラメータ */
    var $objFormParam;

    /** SC_UploadFile インスタンス */
    var $objUpfile;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/upload_csv.tpl';
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'upload_csv';
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

                    if(!$err) echo $line." / ".$rec_count. "行目　（商品ID：".$arrParam['product_id']." / 商品名：".$arrParam['name'].")\n<br />";
                    flush();
                }
                fclose($fp);

                if(!$err) {
                    $objQuery->commit();
                    echo "■" . $regist . "件のレコードを登録しました。";
                    // 商品件数カウント関数の実行
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

        $this->objFormParam->addParam("商品ID", "product_id", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
        $this->objFormParam->addParam("商品規格ID", "product_class_id", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));

        $this->objFormParam->addParam("規格名1", "dummy1");
        $this->objFormParam->addParam("規格名2", "dummy2");

        $this->objFormParam->addParam("商品名", "name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("公開フラグ(1:公開 2:非公開)", "status", INT_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
        $this->objFormParam->addParam("商品ステータス", "product_flag", INT_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
        $this->objFormParam->addParam("商品コード", "product_code", STEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam(NORMAL_PRICE_TITLE, "price01", PRICE_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
        $this->objFormParam->addParam(SALE_PRICE_TITLE, "price02", PRICE_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
        $this->objFormParam->addParam("在庫数", "stock", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
        $this->objFormParam->addParam("送料", "deliv_fee", PRICE_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
        $this->objFormParam->addParam("ポイント付与率", "point_rate", PERCENTAGE_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
        $this->objFormParam->addParam("購入制限", "sale_limit", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
        $this->objFormParam->addParam("メーカーURL", "comment1", URL_LEN, "KVa", array("SPTAB_CHECK","URL_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("検索ワード", "comment3", LLTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("一覧-メインコメント", "main_list_comment", LTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("一覧-メイン画像", "main_list_image", LTEXT_LEN, "KVa", array("EXIST_CHECK","FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("メインコメント", "main_comment", LTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("メイン画像", "main_image", LTEXT_LEN, "KVa", array("EXIST_CHECK","FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("メイン拡大画像", "main_large_image", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("カラー比較画像", "file1", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("商品詳細ファイル", "file2", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブタイトル(1)", "sub_title1", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブコメント(1)", "sub_comment1", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブ画像(1)", "sub_image1", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブ拡大画像(1)", "sub_large_image1", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));

        $this->objFormParam->addParam("詳細-サブタイトル(2)", "sub_title2", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブコメント(2)", "sub_comment2", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブ画像(2)", "sub_image2", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブ拡大画像(2)", "sub_large_image2", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));

        $this->objFormParam->addParam("詳細-サブタイトル(3)", "sub_title3", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブコメント(3)", "sub_comment3", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブ画像(3)", "sub_image3", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブ拡大画像(3)", "sub_large_image3", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));

        $this->objFormParam->addParam("詳細-サブタイトル(4)", "sub_title4", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブコメント(4)", "sub_comment4", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブ画像(4)", "sub_image4", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブ拡大画像(4)", "sub_large_image4", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));

        $this->objFormParam->addParam("詳細-サブタイトル(5)", "sub_title5", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブコメント(5)", "sub_comment5", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブ画像(5)", "sub_image5", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("詳細-サブ拡大画像(5)", "sub_large_image5", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));

        $this->objFormParam->addParam("発送日目安", "deliv_date_id", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));

        for ($cnt = 1; $cnt <= RECOMMEND_PRODUCT_MAX; $cnt++) {
            $this->objFormParam->addParam("おすすめ商品($cnt)", "recommend_product_id$cnt", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
            $this->objFormParam->addParam("詳細-サブコメント($cnt)", "recommend_comment$cnt", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        }

        $this->objFormParam->addParam("商品カテゴリ", "category_id", STEXT_LEN, "n", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     * 商品登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistProduct($objQuery, $line = "") {

        $objDb = new SC_Helper_DB_Ex();

        $arrRet = $this->objFormParam->getHashArray();

        // dtb_products以外に登録される値を除外する。
        foreach($arrRet as $key => $val) {
            switch($key) {
            case 'product_code':
            case 'price01':
            case 'price02':
            case 'stock':
            case 'product_class_id':
            case 'recommend_product_id1':
            case 'recommend_product_id2':
            case 'recommend_product_id3':
            case 'recommend_product_id4':
            case 'recommend_product_id5':
            case 'recommend_product_id6':
            case 'recommend_comment1':
            case 'recommend_comment2':
            case 'recommend_comment3':
            case 'recommend_comment4':
            case 'recommend_comment5':
            case 'recommend_comment6':
                break;
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

        if($sqlval['sale_limit'] == "") {
            $sqlval['sale_unlimited'] = '1';
        } else {
            $sqlval['sale_unlimited'] = '0';
        }

        if($sqlval['status'] == "") {
            $sqlval['status'] = 2;
        }

        if($arrRet['product_id'] != "" && $arrRet['product_class_id'] != "") {
            // カテゴリ内ランクの調整処理
            $old_catid = $objQuery->get("dtb_products", "category_id", "product_id = ?", array($arrRet['product_id']));
            $objDb->sfMoveCatRank($objQuery, "dtb_products", "product_id", "category_id", $old_catid, $arrRet['category_id'], $arrRet['product_id']);

            // UPDATEの実行
            $where = "product_id = ?";
            $objQuery->update("dtb_products", $sqlval, $where, array($sqlval['product_id']));
        } else {

            // 新規登録
            // postgresqlとmysqlとで処理を分ける
            if (DB_TYPE == "pgsql") {
                $product_id = $objQuery->nextval("dtb_products","product_id");
            }elseif (DB_TYPE == "mysql") {
                $product_id = $objQuery->get_auto_increment("dtb_products");
            }
            $sqlval['product_id'] = $product_id;
            $sqlval['create_date'] = $time;

            // カテゴリ内で最大のランクを割り当てる
            $sqlval['rank'] = $objQuery->max("dtb_products", "rank", "category_id = ?", array($arrRet['category_id'])) + 1;

            // INSERTの実行
            $objQuery->insert("dtb_products", $sqlval);
        }

        // 規格登録
        $this->lfRegistProductClass($objQuery, $arrRet, $sqlval['product_id'], $arrRet['product_class_id']);

        // おすすめ商品登録
        $objQuery->delete("dtb_recommend_products", "product_id = ?", array($sqlval['product_id']));
        for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
            $keyname = "recommend_product_id" . $i;
            $comment_key = "recommend_comment" . $i;
            if($arrRet[$keyname] != "") {
                $arrProduct = $objQuery->select("product_id", "dtb_products", "product_id = ?", array($arrRet[$keyname]));
                if($arrProduct[0]['product_id'] != "") {
                    $arrval['product_id'] = $sqlval['product_id'];
                    $arrval['recommend_product_id'] = $arrProduct[0]['product_id'];
                    $arrval['comment'] = $arrRet[$comment_key];
                    $arrval['update_date'] = "Now()";
                    $arrval['create_date'] = "Now()";
                    $arrval['creator_id'] = $_SESSION['member_id'];
                    $arrval['rank'] = RECOMMEND_PRODUCT_MAX - $i + 1;
                    $objQuery->insert("dtb_recommend_products", $arrval);
                }
            }
        }
    }

    /**
     * 商品規格登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param array $arrList 商品規格情報配列
     * @param integer $product_id 商品ID
     * @param integer $product_class_id 商品規格ID
     * @return void
     */
    function lfRegistProductClass($objQuery, $arrList, $product_id, $product_class_id) {
        $sqlval['product_code'] = $arrList["product_code"];
        $sqlval['stock'] = $arrList["stock"];
        if($sqlval['stock'] == "") {
            $sqlval['stock_unlimited'] = '1';
        } else {
            $sqlval['stock_unlimited'] = '0';
        }
        $sqlval['price01'] = $arrList['price01'];
        $sqlval['price02'] = $arrList['price02'];
        $sqlval['creator_id'] = $_SESSION['member_id'];

        // FIXME $sqlval['member_id'] は何処から出てくる?
        if($sqlval['member_id'] == "") {
            $sqlval['creator_id'] = '0';
        }

        if($product_class_id == "") {
            // 新規登録
            $where = "product_id = ?";
            // 念のために既存の規格を削除
            $objQuery->delete("dtb_products_class", $where, array($product_id));
            $sqlval['product_id'] = $product_id;
            $sqlval['classcategory_id1'] = '0';
            $sqlval['classcategory_id2'] = '0';
            $sqlval['create_date'] = "now()";
            $objQuery->insert("dtb_products_class", $sqlval);
        } else {
            // 既存編集
            $where = "product_id = ? AND product_class_id = ?";
            $objQuery->update("dtb_products_class", $sqlval, $where, array($product_id, $product_class_id));
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
            // 商品ID、規格IDの存在チェック
            if($arrRet['product_id'] != "") {
                $count = $objQuery->count("dtb_products", "product_id = ?", array($arrRet['product_id']));
                if($count == 0) {
                    $objErr->arrErr['product_id'] = "※ 指定の商品IDは、登録されていません。";
                }
            }

            if($arrRet['product_class_id'] != "") {
                $count = 0;
                if($arrRet['product_id'] != "") {
                    $count = $objQuery->count("dtb_products_class", "product_id = ? AND product_class_id = ?", array($arrRet['product_id'], $arrRet['product_class_id']));
                }
                if($count == 0) {
                    $objErr->arrErr['product_class_id'] = "※ 指定の規格IDは、登録されていません。";
                }
            }

            // 存在するカテゴリIDかチェック
            $count = $objQuery->count("dtb_category", "category_id = ?", array($arrRet['category_id']));
            if($count == 0) {
                $objErr->arrErr['product_id'] = "※ 指定のカテゴリIDは、登録されていません。";
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
     * 引数 $val の内容は, htmlspecialchars() によってサニタイズされる
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
