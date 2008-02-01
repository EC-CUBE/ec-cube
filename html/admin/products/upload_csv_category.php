<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
mb_language('Japanese');
ini_set("display_errors",1);


require_once("../require.php");

class LC_Page {
    var $arrSession;
    var $arrCSVErr;
    function LC_Page() {
        $this->tpl_mainpage = 'products/upload_csv_category.tpl';
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'upload_csv_category';
    }
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);


// ファイル管理クラス
$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
// ファイル情報の初期化
lfInitFile();
// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
$colmax = $objFormParam->getCount();
$objFormParam->setHtmlDispNameArray();
$objPage->arrTitle = $objFormParam->getHtmlDispNameArray();

switch($_POST['mode']) {
    case 'csv_upload':
        $err = false;
        // エラーチェック
        $arrErr['csv_file'] = $objUpFile->makeTempFile('csv_file');

        if($arrErr['css_file'] == "") {
            $arrErr = $objUpFile->checkEXISTS();
        }

        // 実行時間を制限しない
        set_time_limit(0);

        // 出力をバッファリングしない(==日本語自動変換もしない)
        ob_end_clean();

        // IEのために256バイト空文字出力
        echo str_pad('',256);

        if($arrErr['csv_file'] == "") {
            // 一時ファイル名の取得
            $filepath = $objUpFile->getTempFilePath('csv_file');
            // エンコード
            $enc_filepath = sfEncodeFile($filepath, CHAR_CODE, CSV_TEMP_DIR);

            // レコード数を得る
            $rec_count = lfCSVRecordCount($enc_filepath);

            $fp = fopen($enc_filepath, "r");
            $line = 0;		// 行数
            $regist = 0;	// 登録数

            $objQuery = new SC_Query();
            $objQuery->begin();

            echo "■　CSV登録進捗状況 <br/><br/>\n";				

            while(!feof($fp) && !$err) {

                $arrCSV = fgetcsv($fp, CSV_LINE_MAX);

                //                print "<br/>";
                //                var_dump($arrCSV);
                // 行カウント
                $line++;
                if($line <= 1) {
                    continue;
                }
                // 項目数カウント
                $max = count($arrCSV);
                //                print "項目数カウント".$max;
                // 項目数が1以下の場合は無視する
                if($max <= 1) {
                    continue;
                }
                //                echo "項目数チェック";
                // 項目数チェック
                if($max != $colmax) {
                    echo "※ 項目数が" . $max . "個検出されました。項目数は" . $colmax . "個になります。</br>\n";
                    $err = true;
                } else {
                    //                    echo "項目数が${max}個検出,項目数は${colmax}個です。";
                    // シーケンス配列を格納する。
                    $objFormParam->setParam($arrCSV, true);
                    //                    var_dump($objFormParam);
                    $arrRet = $objFormParam->getHashArray();
                    //                    var_dump($arrRet);
                    $objFormParam->setParam($arrRet);
                    //                    var_dump($objFormParam);
                    // 入力値の変換
                    $objFormParam->convParam();
                    //                    var_dump($objFormParam);
                    // <br>なしでエラー取得する。
                    $arrCSVErr = lfCheckError();
                    //                    var_dump($arrCSVErr);
                }
                // 入力エラーチェック
                if(count($arrCSVErr) > 0) {
                    echo "<font color=\"red\">■" . $line . "行目でエラーが発生しました。</font></br>\n";
                    foreach($arrCSVErr as $val) {
                        echo "<font color=\"red\">" . htmlspecialchars($val, ENT_QUOTES) . "</font></br>\n";
                    }

                    $err = true;
                }
                if(!$err) {
                    lfRegistProduct($objQuery, $line);
                    $regist++;
                }
                $arrParam = $objFormParam->getHashArray();

                if(!$err) echo $line." / ".$rec_count. "行目　（カテゴリID：".$arrParam['category_id']." / カテゴリ名：".$arrParam['category_name'].")\n<br />";
                flush();
            }
            fclose($fp);

            if(!$err) {
                $objQuery->commit();
                echo "■" . $regist . "件のレコードを登録しました。";
                // 商品件数カウント関数の実行
                sfCategory_Count($objQuery);
            } else {
                $objQuery->rollback();
            }
        } else {
            foreach($arrErr as $val) {
                echo "<font color=\"red\">$val</font></br>\n";
            }
        }
        echo "<br/><a href=\"javascript:window.close()\">→閉じる</a>";
        flush();
        exit;
        break;
    default:
        break;
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//--------------------------------------------------------------------------------------------------------------------------

/*
 * 関数名：lfInitFile
 * 説明　：ファイル情報の初期化
 */function lfInitFile() {
global $objUpFile;
$objUpFile->addFile("カテゴリCSV", 'csv_file', array('csv'), CSV_SIZE, true, 0, 0, false);
 }

 /*
  * 関数名：lfInitParam
  * 説明　：入力情報の初期化
  */
 function lfInitParam() {
     global $objFormParam;
     /*
      * +-----TABLE : dtb_category
      * |category_id  	int(11)
      * |category_name 	text
      * |parent_category_id 	int(11)
      * |level 	int(11)
      * |rank 	int(11)
      * |creator_id 	int(11)
      * |create_date 	datetime
      * |update_date 	datetime
      * |del_flg 	smallint(6) 			いいえ 	0
      * +-----TABLE : dtb_category_total_count
      * |category_id  	int(11)  	 	  	いいえ
      * |product_count 	int(11) 			はい 	NU
      * |create_date 	datetime 			いいえ 			
      * +-----TABLE : dtb_category_count
      * |category_id  	int(11)
      * |product_count 	int(11)
      * |create_date 	datetime
      * +---------------------------------
      */

     $objFormParam->addParam("カテゴリID","category_id",INT_LEN,"n",array("MAX_LENGTH_CHECK","NUM_CHECK"));
     $objFormParam->addParam("カテゴリ名","category_name",STEXT_LEN,"KVa",array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
     $objFormParam->addParam("親カテゴリID","parent_category_id",INT_LEN,"n",array("MAX_LENGTH_CHECK","NUM_CHECK"));
     $objFormParam->addParam("階層","level",INT_LEN,"n",array("MAX_LENGTH_CHECK","NUM_CHECK"));
     /*
      * これはログインされているユーザでやればOK
      * $objFormParam->addParam("登録者ID","creator_id",INT_LEN,"n",array());
      */
     $objFormParam->addParam("表示順","rank",INT_LEN,"n",array("MAX_LENGTH_CHECK","NUM_CHECK"));
     //     $objFormParam->addParam("削除フラグ","del_flg",INT_LEN,"n",array());

 }

 /*
  * 関数名：lfRegistProduct
  * 引数1 ：SC_Queryオブジェクト
  * 説明　：商品登録
  */
 function lfRegistProduct($objQuery, $line = "") {
     global $objFormParam;
     $arrRet = $objFormParam->getHashArray();
     //     echo " <br/>ここまでは実行されています。IfRegisProduct".$errrrrr++;
     // dtb_products以外に登録される値を除外する。
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
     //     echo " <br/>ここまでは実行されています。IfRegisProduct".$errrrrr++;

     // 登録時間を生成(DBのnow()だとcommitした際、すべて同一の時間になってしまう)
     $time = date("Y-m-d H:i:s");
     // 秒以下を生成
     if($line != "") {
         $microtime = sprintf("%06d", $line);
         $time .= ".$microtime";
     }
     $sqlval['update_date'] = $time;
     $sqlval['creator_id'] = $_SESSION['member_id'];
     //     echo " <br/>ここまでは実行されています。IfRegisProduct".$errrrrr++;

     //     $old_catid = $objQuery->get("dtb_category","category_id","category_id = ?",array($arrRet['category_id']));
     $count = $objQuery->count("dtb_category", "category_id = ?", array($arrRet['category_id']));
     if($count != 0) {
         //         echo " <br/>ここまでは実行されています。IfRegisProduct　count 0".$errrrrr++;
         // if($old_catid != "" || isset($old_catid)){
         // if($arrRet['category_id'] != "" && $arrRet['product_class_id'] != "") {
         // UPDATEの実行
         echo "UPDATEの実行";
         $where = "category_id = ?";
         $objQuery->update("dtb_category", $sqlval, $where, array($sqlval['category_id']));
     } else {
         //         echo " <br/>ここまでは実行されています。IfRegisProduct count / 0 ".$errrrrr++;
         // 新規登録
         $sqlval['category_id'] = $arrRet['category_id'];
         $sqlval['create_date'] = $time;
         if($arrRet['rank'] == ""){
             // カテゴリ内で最大のランクを割り当てる
             $sqlval['rank'] = $objQuery->max("dtb_products", "rank", "category_id = ?", array($arrRet['category_id'])) + 1;
         }
         // INSERTの実行
         $objQuery->insert("dtb_category", $sqlval);
     }

 }

 /*
  * 関数名：lfCheckError
  * 説明　：入力チェック
  */
 function lfCheckError() {
     global $objFormParam;
     // 入力データを渡す。
     $arrRet =  $objFormParam->getHashArray();
     $objErr = new SC_CheckError($arrRet);
     $objErr->arrErr = $objFormParam->checkError(false);
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

 /*
  * 関数名：lfCSVRecordCount "category
  * 説明　：CSVのカウント数を得る
  * 引数1 ：ファイルパス
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
 ?>