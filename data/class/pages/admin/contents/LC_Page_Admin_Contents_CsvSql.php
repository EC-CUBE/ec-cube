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
require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * CSV 出力項目設定(高度な設定)のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_CsvSql extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contents/csv_sql.tpl';
        $this->tpl_subnavi = 'contents/subnavi.tpl';
        $this->tpl_subno = 'csv';
        $this->tpl_subno_csv = 'csv_sql';
        $this->tpl_mainno = "contents";
        $this->tpl_subtitle = 'CSV出力設定';
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
        $objDbFactory = SC_DB_DBFactory_Ex::getInstance();

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        if (!isset($_POST['mode'])) $_POST['mode'] = "";
        if (!isset($_POST['sql_id'])) $_POST['sql_id'] = "";
        if (!isset($_GET['sql_id'])) $_GET['sql_id'] = "";
        if (!isset($_POST['selectTable'])) $_POST['selectTable'] = "";

        // SQL_IDの取得
        if ($_POST['sql_id'] != "") {
            $sql_id = $_POST['sql_id'];
        }elseif($_GET['sql_id'] != ""){
            $sql_id = $_GET['sql_id'];
        }else{
            $sql_id = "";
        }

        $mode = $_POST['mode'];

        switch($_POST['mode']) {
            // データの登録
        case "confirm":
            // エラーチェック
            $this->arrErr = $this->lfCheckError($_POST);

            if (count($this->arrErr) <= 0){
                // データの更新
                $sql_id = $this->lfUpdData($sql_id, $_POST);
                // 完了メッセージ表示
                $this->tpl_onload = "alert('登録が完了しました。');";
            }
            break;

            // 確認画面
        case "preview":
            // SQL文表示
            $sql = "SELECT \n" . $_POST['csv_sql']; // FIXME
            $this->sql = $sql;

            // エラー表示
            $objErrMsg = $this->lfCheckSQL($_POST);
            if ($objErrMsg != "") {
                $errMsg = $objErrMsg->message . "\n" . $objErrMsg->userinfo;
            }

            $this->sqlerr = isset($errMsg) ? $errMsg : "" ;

            // 画面の表示
            $this->setTemplate('contents/csv_sql_view.tpl');
            return;
            break;

            // 新規作成
        case "new_page":
            $this->objDisplay->redirect($this->getLocation("./csv_sql.php"));
            exit;
            break;

            // データ削除
        case "delete":
            $this->lfDelData($sql_id);
            $this->objDisplay->redirect($this->getLocation("./csv_sql.php"));
            exit;
            break;

        case "csv_output":
            // CSV出力データ取得
            $arrCsvData = $this->lfGetSqlList(" WHERE sql_id = ?", array($_POST['csv_output_id']));

            $objQuery = new SC_Query();

            $arrCsvOutputData = $objQuery->getAll("SELECT " . $arrCsvData[0]['csv_sql']);

            if (count($arrCsvOutputData) > 0) {

                $arrKey = array_keys(SC_Utils_Ex::sfSwapArray($arrCsvOutputData));
                $i = 0;
                $header = "";
                foreach($arrKey as $data) {
                    if ($i != 0) $header .= ",";
                    $header .= $data;
                    $i ++;
                }
                $header .= "\n";

                $data = SC_Utils_Ex::getCSVData($arrCsvOutputData, $arrKey);
                // CSV出力
                SC_Utils_Ex::sfCSVDownload($header.$data);
                exit;
            }else{
                $this->tpl_onload = "alert('出力データがありません。');";
                $sql_id = "";
                $_POST="";
            }
            break;
        }

        // mode が confirm 以外のときは完了メッセージは出力しない
        if ($mode != "confirm" and $mode != "csv_output") {
            $this->tpl_onload = "";
        }

        // 登録済みSQL一覧取得
        $arrSqlList = $this->lfGetSqlList();

        // 編集用SQLデータの取得
        if ($sql_id != "") {
            $arrSqlData = $this->lfGetSqlList(" WHERE sql_id = ?", array($sql_id));
        }

        // テーブル一覧を取得する
        $arrTableList = $this->lfGetTableList();
        $arrTableList = SC_Utils_Ex::sfSwapArray($arrTableList);

        // 現在選択されているテーブルを取得する
        if ($_POST['selectTable'] == ""){
            $selectTable = $arrTableList['table_name'][0];
        }else{
            $selectTable = $_POST['selectTable'];
        }

        // カラム一覧を取得する
        $arrColList = $this->lfGetColumnList($selectTable);
        $arrColList =  SC_Utils_Ex::sfSwapArray($arrColList);

        // 表示させる内容を編集
        foreach ($arrTableList['description'] as $key => $val) {
            $arrTableList['description'][$key] = $arrTableList['table_name'][$key] . "：" . $arrTableList['description'][$key];
        }
        foreach ($arrColList['description'] as $key => $val) {
            $arrColList['description'][$key] = $arrColList['column_name'][$key] . "：" . $arrColList['description'][$key];
        }


        $arrDiff = array_diff($objDbFactory->sfGetColumnList($selectTable), $arrColList["column_name"]);
        $arrColList["column_name"] = array_merge($arrColList["column_name"], $arrDiff);
        $arrColList["description"] = array_merge($arrColList["description"], $arrDiff);

        // テンプレートに出力するデータをセット
        $this->arrSqlList = $arrSqlList;	// SQL一覧
        $this->arrTableList = SC_Utils_Ex::sfarrCombine($arrTableList['table_name'], $arrTableList['description']);	// テーブル一覧
        $this->arrColList = SC_Utils_Ex::sfarrCombine($arrColList['column_name'],$arrColList['description']);			// カラム一覧
        $this->selectTable = $selectTable;	// 選択されているテーブル
        $this->sql_id = $sql_id;			// 選択されているSQL

        // POSTされたデータをセットする
        if (isset($_POST['sql_name']) && isset($_POST['csv_sql'])){
            $arrSqlData[0]['sql_name'] = isset($_POST['sql_name']) ? $_POST['sql_name'] : "";
            $arrSqlData[0]['csv_sql'] = isset($_POST['csv_sql']) ? $_POST['csv_sql'] : "";
        }
        $this->arrSqlData = $arrSqlData[0];	// 選択されているSQLデータ
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
     * テーブル一覧を取得する.
     *
     * @return void
     */
    function lfGetTableList(){
        $objQuery = new SC_Query();
        $arrRet = array();		// 結果取得用

        $sql = "";
        $sql .= "SELECT table_name, description FROM dtb_table_comment WHERE column_name IS NULL ORDER BY table_name";
        $arrRet = $objQuery->getAll($sql);

        return $arrRet;
    }

    /**
     * テーブルのカラム一覧を取得する.
     *
     * @param string $selectTable テーブル名
     * @return array カラム一覧の配列
     */
    function lfGetColumnList($selectTable){
        $objQuery = new SC_Query();
        $arrRet = array();		// 結果取得用
        $sql = "";
        $sql .= " SELECT column_name, description FROM dtb_table_comment WHERE table_name = ? AND column_name IS NOT NULL";
        $arrRet = $objQuery->getAll($sql, array($selectTable));

        return $arrRet;
    }

    /**
     * 登録済みSQL一覧を取得する.
     *
     * @param string $where Where句
     * @param array $arrData 絞り込みデータ
     * @return array 取得結果の配列
     */
    function lfGetSqlList($where = "" , $arrData = array()){
        $objQuery = new SC_Query();
        $arrRet = array();		// 結果取得用

        $sql = "";
        $sql .= " SELECT";
        $sql .= "     sql_id,";
        $sql .= "     sql_name,";
        $sql .= "     csv_sql,";
        $sql .= "     update_date,";
        $sql .= "     create_date";
        $sql .= " FROM";
        $sql .= "     dtb_csv_sql";

        // Where句の指定があれば結合する
        if ($where != "") {
            $sql .= " $where ";
        }else{
            $sql .= " ORDER BY sql_id ";
        }
        $sql .= " ";

        // データを引数で渡されている場合にはセットする
        if (count($arrData) > 0) {
            $arrRet = $objQuery->getAll($sql, $arrData);
        }else{
            $arrRet = $objQuery->getAll($sql);
        }

        return $arrRet;
    }

    /**
     * 入力項目のエラーチェックを行う.
     *
     * @param array POSTデータ
     * @return array エラー内容の配列
     */
    function lfCheckError($data){
        $objErr = new SC_CheckError();
        $objErr->doFunc( array("名称", "sql_name"), array("EXIST_CHECK") );
        $objErr->doFunc( array("SQL文", "csv_sql", "30000"), array("EXIST_CHECK", "MAX_LENGTH_CHECK") );

        // SQLの妥当性チェック
        if ($objErr->arrErr['csv_sql'] == "") {
            $objsqlErr = $this->lfCheckSQL($data);
            if ($objsqlErr != "") {
                $objErr->arrErr["csv_sql"] = "SQL文が不正です。SQL文を見直してください";
            }
        }

        return $objErr->arrErr;
    }

    /**
     * 入力されたSQL文が正しいかチェックを行う.
     *
     * @param array POSTデータ
     * @return array エラー内容
     */
    function lfCheckSQL($data){
        $err = "";
        $objQuery = new SC_Query();
        $sql = "SELECT " . $data['csv_sql'] . " ";
        $ret = $objQuery->query($sql);
        if (PEAR::isError($ret)){
            $err = $ret;
        }

        return $err;
    }

    /**
     * DBにデータを保存する.
     *
     * @param integer $sql_id 更新するデータのSQL_ID
     * @param array $arrData 更新データの配列
     * @return integer $sql_id SQL_IDを返す
     */
    function lfUpdData($sql_id = "", $arrData = array()){
        $objQuery = new SC_Query();		// DB操作オブジェクト
        $sql = "";						// データ取得SQL生成用
        $arrRet = array();				// データ取得用(更新判定)
        $arrVal = array();				// データ更新

        // sql_id が指定されている場合にはUPDATE
        if ($sql_id != "") {
            // 存在チェック
            $arrSqlData = $this->lfGetSqlList(" WHERE sql_id = ?", array($sql_id));
            if (count($arrSqlData) > 0) {
                // データ更新
                $sql = "UPDATE dtb_csv_sql SET sql_name = ?, csv_sql = ?, update_date = now() WHERE sql_id = ? ";
                $arrVal= array($arrData['sql_name'], $arrData['csv_sql'], $sql_id);
            }else{
                // データの新規作成
                $sql_id = "";
                $sql = "INSERT INTO dtb_csv_sql (sql_id, sql_name, csv_sql, create_date, update_date) values (?, ?, ?, now(), now()) ";
                $arrVal= array($objQuery->nextVal('dtb_csv_sql_sql_id'), $arrData['sql_name'], $arrData['csv_sql']);

            }
        }else{
            // データの新規作成
            $sql = "INSERT INTO dtb_csv_sql (sql_id, sql_name, csv_sql, create_date, update_date) values (?, ?, ?, now(), now()) ";
            $arrVal= array($objQuery->nextVal('dtb_csv_sql_sql_id'), $arrData['sql_name'], $arrData['csv_sql']);
        }
        // SQL実行
        $arrRet = $objQuery->query($sql,$arrVal);

        // 新規作成時は$sql_idを取得
        if ($sql_id == "") {
            $arrNewData = $this->lfGetSqlList(" ORDER BY create_date DESC");
            $sql_id = $arrNewData[0]['sql_id'];
        }

        return $sql_id;
    }


    /**
     * 登録済みデータを削除する.
     *
     * @param integer $sql_id 削除するデータのSQL_ID
     * @return bool 実行結果 TRUE：成功 FALSE：失敗
     */
    function lfDelData($sql_id = ""){
        $objQuery = new SC_Query();		// DB操作オブジェクト
        $sql = "";						// データ取得SQL生成用
        $Ret = false;					// 実行結果

        // sql_id が指定されている場合のみ実行
        if ($sql_id != "") {
            // データの削除
            $sql = "DELETE FROM dtb_csv_sql WHERE sql_id = ? ";
            // SQL実行
            $ret = $objQuery->query($sql,array($sql_id));
        }else{
            $ret = false;
        }

        // 結果を返す
        return $ret;
    }
}
?>
