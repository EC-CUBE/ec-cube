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
require_once(DATA_PATH. "module/Tar.php");
/**
 * バックアップ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System_Bkup extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'system/bkup.tpl';
        $this->tpl_subnavi = 'system/subnavi.tpl';
        $this->tpl_mainno = 'system';
        $this->tpl_subno = 'bkup';
        $this->tpl_subtitle = 'バックアップ管理';

        $this->bkup_dir = DATA_PATH . "downloads/backup/";

    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
        $objQuery = new SC_Query();

        // セッションクラス
        $objSess = new SC_Session();
        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // バックアップテーブルがなければ作成する
        $this->lfCreateBkupTable();

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
            // バックアップを作成する
        case 'bkup':
            // 入力文字列の変換
            $arrData = $this->lfConvertParam($_POST);

            // エラーチェック
            $arrErr = $this->lfCheckError($arrData);

            // エラーがなければバックアップ処理を行う
            if (count($arrErr) <= 0) {
                // バックアップファイル作成
                $arrErr = $this->lfCreateBkupData($arrData['bkup_name']);

                // DBにデータ更新
                if (count($arrErr) <= 0) {
                    $this->lfUpdBkupData($arrData);
                }else{
                    $arrForm = $arrData;
                }

                $this->tpl_onload = "alert('バックアップ完了しました');";
            }else{
                $arrForm = $arrData;
            }

            break;

            // リストア
        case 'restore':
        case 'restore_config':
            if ($_POST['mode'] == 'restore_config') {
                $this->mode = "restore_config";
            }

            $this->lfRestore($_POST['list_name']);

            break;

            // 削除
        case 'delete':
            $del_file = $this->bkup_dir.$_POST['list_name'] . ".tar.gz";
            // ファイルの削除
            if(is_file($del_file)){
                $ret = unlink($del_file);
            }

            // DBから削除
            $delsql = "DELETE FROM dtb_bkup WHERE bkup_name = ?";
            $objQuery->query($delsql, array($_POST['list_name']));

            break;

            // ダウンロード
        case 'download' :
            $filename = $_POST['list_name'] . ".tar.gz";
            $dl_file = $this->bkup_dir.$_POST['list_name'] . ".tar.gz";

            // ダウンロード開始
            Header("Content-disposition: attachment; filename=${filename}");
            Header("Content-type: application/octet-stream; name=${filename}");
            header("Content-Length: " .filesize($dl_file));
            readfile ($dl_file);
            exit();
            break;

        default:
            break;
        }

        // バックアップリストを取得する
        $arrBkupList = $this->lfGetBkupData("ORDER BY create_date DESC");
        // テンプレートファイルに渡すデータをセット
        $this->arrErr = isset($arrErr) ? $arrErr : "";
        $this->arrForm = isset($arrForm) ? $arrForm : "";
        $this->arrBkupList = $arrBkupList;

        $objView->assignobj($this);		//変数をテンプレートにアサインする
        $objView->display(MAIN_FRAME);		//テンプレートの出力
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* 取得文字列の変換 */
    function lfConvertParam($array) {
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         *  a :  全角英数字を半角英数字に変換する
         */
        $arrConvList['bkup_name'] = "a";
        $arrConvList['bkup_memo'] = "KVa";

        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(isset($array[$key])) {
                $array[$key] = mb_convert_kana($array[$key] ,$val);
            }
        }
        return $array;
    }

    // エラーチェック
    function lfCheckError($array){
        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("バックアップ名", "bkup_name", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK","NO_SPTAB","ALNUM_CHECK"));
        $objErr->doFunc(array("バックアップメモ", "bkup_memo", MTEXT_LEN), array("MAX_LENGTH_CHECK"));

        // 重複チェック
        $ret = $this->lfGetBkupData("WHERE bkup_name = ?", array($array['bkup_name']));
        if (count($ret) > 0) {
            $objErr->arrErr['bkup_name'] = "バックアップ名が重複しています。別名を入力してください。";
        }

        return $objErr->arrErr;
    }

    // バックアップファイル作成
    function lfCreateBkupData($bkup_name){
        $objQuery = new SC_Query();
        $csv_data = "";
        $csv_autoinc = "";
        $err = true;

        $bkup_dir = $this->bkup_dir;
        if (!is_dir(dirname($bkup_dir))) $err = mkdir(dirname($bkup_dir));
        $bkup_dir = $bkup_dir . $bkup_name . "/";

        // 全テーブル取得
        $arrTableList = $this->lfGetTableList();

        // 各テーブル情報を取得する
        foreach($arrTableList as $key => $val){

            if (!($val == "dtb_bkup" || $val == "mtb_zip")) {

                // 自動採番型の構成を取得する
                $csv_autoinc .= $this->lfGetAutoIncrement($val);

                // 全データを取得
                if ($val == "dtb_pagelayout"){
                    $arrData = $objQuery->getAll("SELECT * FROM $val ORDER BY page_id");
                }else{
                    $arrData = $objQuery->getAll("SELECT * FROM $val");
                }

                // CSVデータ生成
                if (count($arrData) > 0) {

                    // カラムをCSV形式に整える
                    $arrKyes = SC_Utils_Ex::sfGetCommaList(array_keys($arrData[0]), false);

                    // データをCSV形式に整える
                    $data = "";
                    foreach($arrData as $data_key => $data_val){
                        //$val = str_replace("\"", "\\\"", $val);
                        $data .= $this->lfGetCSVList($arrData[$data_key]);

                    }
                    // CSV出力データ生成
                    $csv_data .= $val . "\r\n";
                    $csv_data .= $arrKyes . "\r\n";
                    $csv_data .= $data;
                    $csv_data .= "\r\n";
                }

                // タイムアウトを防ぐ
                SC_Utils_Ex::sfFlush();
            }
        }

        $csv_file = $bkup_dir . "bkup_data.csv";
        $csv_autoinc_file = $bkup_dir . "autoinc_data.csv";
        mb_internal_encoding(CHAR_CODE);
        // CSV出力
        // ディレクトリが存在していなければ作成する
        if (!is_dir(dirname($csv_file))) {
            $err = mkdir(dirname($csv_file));
        }
        if ($err) {
            // dataをCSV出力
            $fp = fopen($csv_file,"w");
            if($fp) {
                if($csv_data != ""){
                    $err = fwrite($fp, $csv_data);
                }
                fclose($fp);
            }

            // 自動採番をCSV出力
            $fp = fopen($csv_autoinc_file,"w");
            if($fp) {
                if($csv_autoinc != ""){
                    $err = fwrite($fp, $csv_autoinc);
                }
                fclose($fp);
            }
        }

        // 各種ファイルコピー
        if ($err) {
            /**
            // 商品画像ファイルをコピー
            // ディレクトリが存在していなければ作成する
            $image_dir = $bkup_dir . "save_image/";
            if (!is_dir(dirname($image_dir))) $err = mkdir(dirname($image_dir));
            $copy_mess = "";
            $copy_mess = SC_Utils_Ex::sfCopyDir("../../upload/save_image/",$image_dir, $copy_mess);

            // テンプレートファイルをコピー
            // ディレクトリが存在していなければ作成する
            $templates_dir = $bkup_dir . "templates/";
            if (!is_dir(dirname($templates_dir))) $err = mkdir(dirname($templates_dir));
            $copy_mess = "";
            $copy_mess = SC_Utils_Ex::sfCopyDir("../../user_data/templates/",$templates_dir, $copy_mess);

            // インクルードファイルをコピー
            // ディレクトリが存在していなければ作成する
            $inc_dir = $bkup_dir . "include/";
            if (!is_dir(dirname($inc_dir))) $err = mkdir(dirname($inc_dir));
            $copy_mess = "";
            $copy_mess = SC_Utils_Ex::sfCopyDir("../../user_data/include/",$inc_dir, $copy_mess);

            // CSSファイルをコピー
            // ディレクトリが存在していなければ作成する
            $css_dir = $bkup_dir . "css/";
            if (!is_dir(dirname($css_dir))) $err = mkdir(dirname($css_dir));
            $copy_mess = "";
            $copy_mess = SC_Utils_Ex::sfCopyDir("../../user_data/css/",$css_dir, $copy_mess);
            **/
            //圧縮フラグTRUEはgzip圧縮をおこなう
            $tar = new Archive_Tar($this->bkup_dir . $bkup_name.".tar.gz", TRUE);

            //bkupフォルダに移動する
            chdir($this->bkup_dir);

            //圧縮をおこなう
            $zip = $tar->create("./" . $bkup_name . "/");

            // バックアップデータの削除
            if ($zip) SC_Utils_Ex::sfDelFile($bkup_dir);
        }

        if (!$err) {
            $arrErr['bkup_name'] = "バックアップに失敗しました。";
            // バックアップデータの削除
            SC_Utils_Ex::sfDelFile($bkup_dir);
        }

        return isset($arrErr) ? $arrErr : array();
    }

    /* 配列の要素をCSVフォーマットで出力する。*/
    function lfGetCSVList($array) {
        $line = '';
        if (count($array) > 0) {
            foreach($array as $key => $val) {
                $val = mb_convert_encoding($val, CHAR_CODE, CHAR_CODE);
                $val = str_replace("\"", "\\\"", $val);
                $line .= "\"".$val."\",";
            }
            $line = ereg_replace(",$", "\r\n", $line);
        }else{
            return false;
        }
        return $line;
    }

    // 全テーブルリストを取得する
    function lfGetTableList(){
        $objQuery = new SC_Query();

        if(DB_TYPE == "pgsql"){
            $sql = "SELECT tablename FROM pg_tables WHERE tableowner = ? ORDER BY tablename ; ";
            $arrRet = $objQuery->getAll($sql, array(DB_USER));
            $arrRet = SC_Utils_Ex::sfSwapArray($arrRet);
            $arrRet = $arrRet['tablename'];
        }else if(DB_TYPE == "mysql"){
            $sql = "SHOW TABLES;";
            $arrRet = $objQuery->getAll($sql);
            $arrRet = SC_Utils_Ex::sfSwapArray($arrRet);

            // キーを取得
            $arrKey = array_keys($arrRet);

            $arrRet = $arrRet[$arrKey[0]];
        }
        return $arrRet;
    }

    // 自動採番型をCSV出力形式に変換する
    function lfGetAutoIncrement($table_name){
        $arrColList = $this->lfGetColumnList($table_name);
        $ret = "";

        if(DB_TYPE == "pgsql"){
            $match = 'nextval(\'';
        }else if(DB_TYPE == "mysql"){
            $match = "auto_incr";
        }

        foreach($arrColList['col_def'] as $key => $val){

            if (substr($val,0,9) == $match) {
                $col = $arrColList['col_name'][$key];
                $autoVal = $this->lfGetAutoIncrementVal($table_name, $col);
                $ret .= "$table_name,$col,$autoVal\n";
            }
        }

        return $ret;
    }

    // テーブル構成を取得する
    Function LfgetColumnlist($table_name){
        $objQuery = new SC_Query();

        if(DB_TYPE == "pgsql"){
            $sql = "SELECT
                    a.attname, t.typname, a.attnotnull, d.adsrc as defval, a.atttypmod, a.attnum as fldnum, e.description
                FROM
                    pg_class c,
                    pg_type t,
                    pg_attribute a left join pg_attrdef d on (a.attrelid=d.adrelid and a.attnum=d.adnum)
                                   left join pg_description e on (a.attrelid=e.objoid and a.attnum=e.objsubid)
                WHERE (c.relname=?) AND (c.oid=a.attrelid) AND (a.atttypid=t.oid) AND a.attnum > 0
                ORDER BY fldnum";
            $arrColList = $objQuery->getAll($sql, array($table_name));
            $arrColList = SC_Utils_Ex::sfSwapArray($arrColList);

            $arrRet['col_def'] = $arrColList['defval'];
            $arrRet['col_name'] = $arrColList['attname'];
        }else if(DB_TYPE == "mysql"){
            $sql = "SHOW COLUMNS FROM $table_name";
            $arrColList = $objQuery->getAll($sql);
            $arrColList = SC_Utils_Ex::sfSwapArray($arrColList);

            $arrRet['col_def'] = $arrColList['Extra'];
            $arrRet['col_name'] = $arrColList['Field'];
        }
        return $arrRet;
    }

    // 自動採番型の値を取得する
    function lfGetAutoIncrementVal($table_name , $colname = ""){
        $objQuery = new SC_Query();
        $ret = "";

        if(DB_TYPE == "pgsql"){
            $ret = $objQuery->nextval($table_name, $colname) - 1;
        }else if(DB_TYPE == "mysql"){
            $sql = "SHOW TABLE STATUS LIKE ?";
            $arrData = $objQuery->getAll($sql, array($table_name));
            $ret = $arrData[0]['Auto_increment'];
        }
        return $ret;
    }

    // バックアップテーブルにデータを更新する
    function lfUpdBkupData($data){
        $objQuery = new SC_Query();

        $sql = "INSERT INTO dtb_bkup (bkup_name,bkup_memo,create_date) values (?,?,now())";
        $objQuery->query($sql, array($data['bkup_name'],$data['bkup_memo']));
    }

    // バックアップテーブルからデータを取得する
    function lfGetBkupData($where = "", $data = array()){
        $objQuery = new SC_Query();

        $sql = "SELECT bkup_name, bkup_memo, create_date FROM dtb_bkup ";
        if ($where != "")	$sql .= $where;

        $ret = $objQuery->getAll($sql,$data);

        return $ret;
    }

    // バックアップファイルをリストアする
    function lfRestore($bkup_name){
        $objQuery = new SC_Query("", false);
        $csv_data = "";
        $err = true;

        $bkup_dir = $this->bkup_dir . $bkup_name . "/";

        //バックアップフォルダに移動する
        chdir($this->bkup_dir);

        //圧縮フラグTRUEはgzip解凍をおこなう
        $tar = new Archive_Tar($bkup_name . ".tar.gz", TRUE);

        //指定されたフォルダ内に解凍する
        $err = $tar->extract("./");

        // 無事解凍できれば、リストアを行う
        if ($err) {

            // トランザクション開始
            $objQuery->begin();

            // DBをクリア
            $err = $this->lfDeleteAll($objQuery);

            // INSERT実行
            if ($err) $err = $this->lfExeInsertSQL($objQuery, $bkup_dir . "bkup_data.csv");

            // 自動採番の値をセット
            if ($err) $this->lfSetAutoInc($objQuery, $bkup_dir . "autoinc_data.csv");

            // 各種ファイルのコピー
            /**
            if ($err) {
                // 画像のコピー
                $image_dir = $bkup_dir . "save_image/";
                $copy_mess = "";
                $copy_mess = SC_Utils_Ex::sfCopyDir($image_dir, "../../upload/save_image/", $copy_mess, true);

                // テンプレートのコピー
                $tmp_dir = $bkup_dir . "templates/";
                $copy_mess = "";
                $copy_mess = SC_Utils_Ex::sfCopyDir($tmp_dir, "../../user_data/templates/", $copy_mess, true);

                // インクルードファイルのコピー
                $inc_dir = $bkup_dir . "include/";
                $copy_mess = "";
                $copy_mess = SC_Utils_Ex::sfCopyDir($inc_dir, "../../user_data/include/", $copy_mess, true);

                // CSSのコピー
                $css_dir = $bkup_dir . "css/";
                $copy_mess = "";
                $copy_mess = SC_Utils_Ex::sfCopyDir($css_dir, "../../user_data/css/", $copy_mess, true);

                // バックアップデータの削除
                SC_Utils_Ex::sfDelFile($bkup_dir);
            }**/

            // リストア成功ならコミット失敗ならロールバック
            if ($err) {
                $objQuery->commit();
                $this->restore_msg = "リストア終了しました。";
                $this->restore_err = true;
            }else{
                $objQuery->rollback();
                $this->restore_msg = "リストアに失敗しました。";
                $this->restore_name = $bkup_name;
                $this->restore_err = false;
            }
        }
    }

    // CSVファイルからインサート実行
    function lfExeInsertSQL($objQuery, $csv){

        $sql = "";
        $base_sql = "";
        $tbl_flg = false;
        $col_flg = false;
        $ret = true;
        $pagelayout_flg = false;
        $mode = $this->mode;

        // csvファイルからデータの取得
        $fp = fopen($csv, "r");
        while (!feof($fp)) {
            $data = fgetcsv($fp, 1000000);

            //空白行のときはテーブル変更
            if (count($data) <= 1 and $data[0] == "") {
                $base_sql = "";
                $tbl_flg = false;
                $col_flg = false;
                continue;
            }

            // テーブルフラグがたっていない場合にはテーブル名セット
            if (!$tbl_flg) {
                $base_sql = "INSERT INTO $data[0] ";
                $tbl_flg = true;

                if($data[0] == "dtb_pagelayout"){
                    $pagelayout_flg = true;
                }

                continue;
            }

            // カラムフラグがたっていない場合にはカラムセット
            if (!$col_flg) {
                if ($mode != "restore_config"){
                    $base_sql .= " ( $data[0] ";
                    for($i = 1; $i < count($data); $i++){
                        $base_sql .= "," . $data[$i];
                    }
                    $base_sql .= " ) ";
                }
                $col_flg = true;
                continue;
            }

            // インサートする値をセット
            $sql = $base_sql . "VALUES ( ? ";
            for($i = 1; $i < count($data); $i++){
                $sql .= ", ?";
            }
            $sql .= " );";
            $data = str_replace("\\\"", "\"", $data);
            $err = $objQuery->query($sql, $data);

            // エラーがあれば終了
            if ($err->message != ""){
                SC_Utils_Ex::sfErrorHeader(">> " . $objQuery->getlastquery(false));
                return false;
            }

            if ($pagelayout_flg) {
                // dtb_pagelayoutの場合には最初のデータはpage_id = 0にする
                $sql = "UPDATE dtb_pagelayout SET page_id = '0'";
                $objQuery->query($sql);
                $pagelayout_flg = false;
            }

            // タイムアウトを防ぐ
            SC_Utils_Ex::sfFlush();
        }
        fclose($fp);

        return $ret;
    }

    // 自動採番をセット
    function lfSetAutoInc($objQuery, $csv){
        // csvファイルからデータの取得
        $arrCsvData = file($csv);

        foreach($arrCsvData as $key => $val){
            $arrData = split(",", trim($val));

            if ($arrData[2] == 0)	$arrData[2] = 1;
            $objQuery->setval($arrData[0], $arrData[1], $arrData[2]);
        }
    }

    // DBを全てクリアする
    function lfDeleteAll($objQuery){
        $ret = true;

        $arrTableList = $this->lfGetTableList();

        foreach($arrTableList as $key => $val){
            // バックアップテーブルは削除しない
            if ($val != "dtb_bkup") {
                $trun_sql = "DELETE FROM $val;";
                $ret = $objQuery->query($trun_sql);

                if (!$ret) return $ret;
            }
        }

        return $ret;
    }

    // バックアップテーブルを作成する
    function lfCreateBkupTable(){
        $objQuery = new SC_Query();

        // テーブルの存在チェック
        $arrTableList = $this->lfGetTableList();

        if(!in_array("dtb_bkup", $arrTableList)){
            // 存在していなければ作成
            $cre_sql = "
            create table dtb_bkup
            (
                bkup_name   text,
                bkup_memo   text,
                create_date timestamp
            );
        ";

            $objQuery->query($cre_sql);
        }
    }
}
?>
