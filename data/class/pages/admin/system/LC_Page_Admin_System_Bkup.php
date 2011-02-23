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
require_once(DATA_REALDIR. "module/Tar.php");
/**
 * バックアップ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System_Bkup extends LC_Page_Admin_Ex {

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

        $this->bkup_dir = DATA_REALDIR . "downloads/backup/";
        $this->bkup_ext = '.tar.gz';
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
        $objQuery =& SC_Query::getSingletonInstance();


        $objFormParam = new SC_FormParam;

        // パラメータの初期化
        $this->initParam($objFormParam, $_POST);

        $arrErr  = array();
        $arrForm = array();

        switch($this->getMode()) {

        // バックアップを作成する
        case 'bkup':

            // データ型エラーチェック
            $arrErr[1] = $objFormParam->checkError();

            // データ型に問題がない場合
            if(SC_Utils_Ex::isBlank($arrErr[1])) {
                // データ型以外のエラーチェック
                $arrErr[2] = $this->lfCheckError($objFormParam->getHashArray());
            }

            // エラーがなければバックアップ処理を行う
            if (SC_Utils_Ex::isBlank($arrErr[1]) && SC_Utils_Ex::isBlank($arrErr[2])) {
            
                $arrData = $objFormParam->getHashArray();
            
                // バックアップファイル作成
                $arrErr[3] = $this->lfCreateBkupData($arrData['bkup_name'], $this->bkup_dir);

                // DBにデータ更新
                if (SC_Utils_Ex::isBlank($arrErr[3])) {
                    $this->lfUpdBkupData($arrData);
                }else{
                    $arrForm = $arrData;
                }

                $this->tpl_onload = "alert('バックアップ完了しました');";
            }else{
                $arrForm = $objFormParam->getHashArray();
            }

            break;

        // リストア
        case 'restore_config':
        	$this->mode = "restore_config";

        case 'restore':
            $this->lfRestore($_POST['list_name'], $this->bkup_dir, $this->bkup_ext, $this->mode);
            break;

        // 削除
        case 'delete':
            $del_file = $this->bkup_dir.$_POST['list_name'] . $this->bkup_ext;
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
            $filename = $_POST['list_name'] . $this->bkup_ext;
            $dl_file = $this->bkup_dir.$_POST['list_name'] . $this->bkup_ext;

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
        $this->arrErr = isset($arrErr) ? $arrErr : array();
        $this->arrForm = isset($arrForm) ? $arrForm : array();
        $this->arrBkupList = $arrBkupList;
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
     * パラメータ初期化.
     *
     * @param object $objFormParam
     * @param array  $arrParams  $_POST値
     * @return void
     */
    function initParam(&$objFormParam, &$arrParams) {

        $objFormParam->addParam('バックアップ名', 'bkup_name', STEXT_LEN, 'a', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NO_SPTAB', 'ALNUM_CHECK'));
        $objFormParam->addParam('バックアップメモ', 'bkup_memo', MTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->setParam($arrParams);
        $objFormParam->convParam();

    }

    /**
     * データ型以外のエラーチェック.
     *
     * @param array $arrForm
     * @return $arrErr
     */
    function lfCheckError(&$arrForm){

        // 重複チェック
        $ret = $this->lfGetBkupData("WHERE bkup_name = ?", array($arrForm['bkup_name']));
        if (count($ret) > 0) {
            $arrErr['bkup_name'] = "バックアップ名が重複しています。別名を入力してください。";
        }

        return $arrErr;
    }

    /**
     * バックアップファイル作成.
     *
     * @param string $bkup_name
     * @return array $arrErr
     */
    function lfCreateBkupData($bkup_name, $bkup_dir){
        // 実行時間を制限しない
        set_time_limit(0);

        $objQuery =& SC_Query::getSingletonInstance();
        $csv_data = "";
        $csv_autoinc = "";
        $arrData = array();
        $success = true;

        if (!is_dir(dirname($bkup_dir))) $success = mkdir(dirname($bkup_dir));
        $bkup_dir = $bkup_dir . $bkup_name . "/";

        // 全テーブル取得
        $arrTableList = $objQuery->listTables();

        // 各テーブル情報を取得する
        foreach($arrTableList as $key => $val){

            if (!($val == "dtb_bkup" || $val == "mtb_zip")) {

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
                        $data .= $this->lfGetCSVList($arrData[$data_key]);
                    }
                    // CSV出力データ生成
                    $csv_data .= $val . "\r\n";
                    $csv_data .= $arrKyes . "\r\n";
                    $csv_data .= $data . "\r\n";
                }

                // タイムアウトを防ぐ
                SC_Utils_Ex::sfFlush();
            }
        }

        // 自動採番型の構成を取得する
        $csv_autoinc = $this->lfGetAutoIncrement();

        $csv_file = $bkup_dir . "bkup_data.csv";
        $csv_autoinc_file = $bkup_dir . "autoinc_data.csv";
        mb_internal_encoding(CHAR_CODE);
        // CSV出力
        // ディレクトリが存在していなければ作成する
        if (!is_dir(dirname($csv_file))) {
            $success = mkdir(dirname($csv_file));
        }
        if ($success) {
            // dataをCSV出力
            $fp = fopen($csv_file,"w");
            if($fp) {
                if($csv_data != ""){
                    $success = fwrite($fp, $csv_data);
                }
                fclose($fp);
            }

            // 自動採番をCSV出力
            $fp = fopen($csv_autoinc_file,"w");
            if($fp) {
                if($csv_autoinc != ""){
                    $success = fwrite($fp, $csv_autoinc);
                }
                fclose($fp);
            }
        }

        if ($success) {
            //圧縮フラグTRUEはgzip圧縮をおこなう
            $tar = new Archive_Tar($this->bkup_dir . $bkup_name . $this->bkup_ext, TRUE);

            //bkupフォルダに移動する
            chdir($this->bkup_dir);

            //圧縮をおこなう
            $zip = $tar->create("./" . $bkup_name . "/");

            // バックアップデータの削除
            if ($zip) SC_Utils_Ex::sfDelFile($bkup_dir);
        }

        if (!$success) {
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

    /**
     * シーケンス一覧をCSV出力形式に変換する.
     *
     * シーケンス名,シーケンス値 の形式に出力する.
     *
     * @return string シーケンス一覧の文字列
     * @return string $ret
     */
    function lfGetAutoIncrement() {
        $objQuery =& SC_Query::getSingletonInstance();
        $arrSequences = $objQuery->listSequences();

        foreach($arrSequences as $val){
            $seq = $objQuery->currVal($val);

            $ret .= $val . ",";
            $ret .= is_null($seq) ? "0" : $seq;
            $ret .= "\r\n";
        }
        return $ret;
    }

    // バックアップテーブルにデータを更新する
    function lfUpdBkupData($data){
        $objQuery =& SC_Query::getSingletonInstance();

        $sql = "INSERT INTO dtb_bkup (bkup_name,bkup_memo,create_date) values (?,?,now())";
        $objQuery->query($sql, array($data['bkup_name'],$data['bkup_memo']));
    }

    // バックアップテーブルからデータを取得する
    function lfGetBkupData($where = "", $data = array()){
        $objQuery =& SC_Query::getSingletonInstance();

        $sql = "SELECT bkup_name, bkup_memo, create_date FROM dtb_bkup ";
        if ($where != "")	$sql .= $where;

        $ret = $objQuery->getAll($sql,$data);

        return $ret;
    }

    /**
     * バックアップファイルをリストアする
     *
     * @param string $bkup_name
     * @param string $bkup_dir
     * @param string $bkup_ext
     * @return void
     */
    function lfRestore($bkup_name, $bkup_dir, $bkup_ext, $mode){
        // 実行時間を制限しない
        set_time_limit(0);

        $objQuery =& SC_Query::getSingletonInstance();
        $csv_data = "";
        $success = true;

        $bkup_dir = $bkup_dir . $bkup_name . "/";

        //バックアップフォルダに移動する
        chdir($bkup_dir);

        //圧縮フラグTRUEはgzip解凍をおこなう
        $tar = new Archive_Tar($bkup_name . $bkup_ext, TRUE);

        //指定されたフォルダ内に解凍する
        $success = $tar->extract("./");

        // 無事解凍できれば、リストアを行う
        if ($success) {

            // トランザクション開始
            $objQuery->begin();

            // DBをクリア
            $success = $this->lfDeleteAll($objQuery);

            // INSERT実行
            if ($success) $success = $this->lfExeInsertSQL($objQuery, $bkup_dir . "bkup_data.csv", $mode);

            // 自動採番の値をセット
            if ($success) $this->lfSetAutoInc($objQuery, $bkup_dir . "autoinc_data.csv");

            // リストア成功ならコミット失敗ならロールバック
            if ($success) {
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

    /**
     * CSVファイルからインサート実行.
     *
     * @param object $objQuery
     * @param string $csv
     * @param string $mode
     * @return void
     */
    function lfExeInsertSQL(&$objQuery, $csv, $mode){

        $sql = "";
        $base_sql = "";
        $tbl_flg = false;
        $col_flg = false;
        $ret = true;
        $pagelayout_flg = false;

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
            if (PEAR::isError($err)){
                SC_Utils_Ex::sfErrorHeader(">> " . $objQuery->getlastquery(false));
                return false;
            }

            if ($pagelayout_flg) {
                // dtb_pagelayoutの場合には最初のデータはpage_id = 0にする
                $arrVal['page_id'] = '0';
                $objQuery->update("dtb_pagelayout", $arrVal);
                $pagelayout_flg = false;
            }

            // タイムアウトを防ぐ
            SC_Utils_Ex::sfFlush();
        }
        fclose($fp);

        return $ret;
    }

    // 自動採番をセット
    function lfSetAutoInc(&$objQuery, $csv){
        // csvファイルからデータの取得
        $arrCsvData = file($csv);

        foreach($arrCsvData as $val){
            $arrData = split(",", trim($val));

             $objQuery->setval($arrData[0], $arrData[1]);
        }
    }

    // DBを全てクリアする
    function lfDeleteAll(&$objQuery){
        $ret = true;

        $arrTableList = $objQuery->listTables();

        foreach($arrTableList as $val){
            // バックアップテーブルは削除しない
            if ($val != "dtb_bkup") {
                $ret = $objQuery->delete($val);
                if (PEAR::isError($ret)) return false;
            }
        }
        return true;
    }
}
?>
