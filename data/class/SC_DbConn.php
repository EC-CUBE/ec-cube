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

$current_dir = realpath(dirname(__FILE__));
require_once($current_dir . "/../module/DB.php");

$objDbConn = "";

class SC_DbConn {

    var $conn;
    var $result;
    var $includePath;
    var $dsn;
    var $err_disp = true;
    var $dbFactory;


    // コンストラクタ
    function SC_DbConn($dsn = "", $err_disp = true, $new = false){
        global $objDbConn;

        // Debugモード指定
        $options['debug'] = PEAR_DB_DEBUG;
        // 持続的接続オプション
        $options['persistent'] = PEAR_DB_PERSISTENT;

        // 既に接続されていないか、新規接続要望の場合は接続する。
        if(!isset($objDbConn->connection) || $new) {
            if($dsn != "") {
                $objDbConn = DB::connect($dsn, $options);
                $this->dsn = $dsn;
            } else {
                if(defined('DEFAULT_DSN')) {
                    $objDbConn = DB::connect(DEFAULT_DSN, $options);
                    $this->dsn = DEFAULT_DSN;
                } else {
                    return;
                }
            }
            }
            
            if (DB_TYPE == 'mysql') {
                $objDbConn->query('SET NAMES utf8');
            }
        
        $this->conn = $objDbConn;
        $this->err_disp = $err_disp;
        $this->dbFactory = SC_DB_DBFactory_Ex::getInstance();
    }

    // クエリの実行
    function query($n ,$arr = "", $ignore_err = false){
        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $n = $this->dbFactory->sfChangeMySQL($n);

        if ( $arr ) {
            $result = $this->conn->query($n, $arr);
        } else {
            $result = $this->conn->query($n);
        }

        if ($this->conn->isError($result) && !$ignore_err){
            $this->send_err_mail($result, $n);
        }

        $this->result = $result;
        return $this->result;
    }

    // 一件のみ取得
    function getOne($n, $arr = ""){

        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $n = $this->dbFactory->sfChangeMySQL($n);

        if ( $arr ) {
            $result = $this->conn->getOne($n, $arr);
        } else {
            $result = $this->conn->getOne($n);
        }
        if ($this->conn->isError($result)){
            $this->send_err_mail($result ,$n);
        }
        $this->result = $result;

        return $this->result;
    }
    
    /**
     * クエリを実行し、最初の行を返す
     *
     * @param string $sql SQL クエリ
     * @param array $arrVal プリペアドステートメントの実行時に使用される配列。配列の要素数は、クエリ内のプレースホルダの数と同じでなければなりません。 
     * @param integer $fetchmode 使用するフェッチモード。デフォルトは DB_FETCHMODE_ASSOC。
     * @return array データを含む1次元配列。失敗した場合に DB_Error オブジェクトを返します。
     */
    function getRow($sql, $arrVal = array(), $fetchmode = DB_FETCHMODE_ASSOC) {
        
        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $sql = $this->dbFactory->sfChangeMySQL($sql);
        
        $result = $this->conn->getRow($sql, $arrVal ,$fetchmode);
        
        if ($this->conn->isError($result)){
            $this->send_err_mail($result ,$sql);
        }
        $this->result = $result;
        return $this->result;
    }

    function getCol($n, $col, $arr = "") {

        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $n = $this->dbFactory->sfChangeMySQL($n);

        if ($arr) {
            $result = $this->conn->getCol($n, $col, $arr);
        } else {
            $result = $this->conn->getCol($n, $col);
        }
        if ($this->conn->isError($result)) {
            $this->send_err_mail($result, $n);
        }
        $this->result = $result;
        return $this->result;
    }

    // SELECT文の実行結果を全て取得
    function getAll($n, $arr = ""){

        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $n = $this->dbFactory->sfChangeMySQL($n);

        if(PEAR::isError($this->conn)) {
            if(ADMIN_MODE){
                SC_Utils_Ex::sfErrorHeader("DBへの接続に失敗しました。:" . $this->dsn);
            }else{
                SC_Utils_Ex::sfErrorHeader("DBへの接続に失敗しました。:");
            }
            return 0;
        }

        if ( $arr ){
            $result = $this->conn->getAll($n, $arr, DB_FETCHMODE_ASSOC);
        } else {
            $result = $this->conn->getAll($n, DB_FETCHMODE_ASSOC);
        }

        if ($this->conn->isError($result)){
            $this->send_err_mail($result, $n);
        }
        $this->result = $result;

        return $this->result;
    }

    function autoExecute($table_name, $fields_values, $sql_where = null){

        if ( $sql_where ) {
            $result = $this->conn->autoExecute( $table_name, $fields_values, DB_AUTOQUERY_UPDATE, $sql_where);
        } else {
            $result = $this->conn->autoExecute( $table_name, $fields_values, DB_AUTOQUERY_INSERT);
        }

        if ($this->conn->isError($result)){
            $this->send_err_mail($result, $n);
        }
        $this->result = $result;
        return $this->result;
    }


    function prepare($n){
        global $sql;
        $sql = $n;
        $result = $this->conn->prepare($n);
        $this->result = $result;
        return $this->result;
    }

    function execute($n, $obj){
        global $sql;
        $sql = $n;
        $result = $this->conn->execute($n, $obj);
        $this->result = $result;
        return $this->result;
    }

    function reset(){
        $this->conn->disconnect();
    }

    function send_err_mail($pearResult, $sql){
        require_once(CLASS_EX_PATH . "page_extends/error/LC_Page_Error_SystemError_Ex.php");
        
        $objPage = new LC_Page_Error_SystemError_Ex();
        register_shutdown_function(array($objPage, "destroy"));
        $objPage->init();
        $objPage->addDebugMsg($sql);
        $objPage->pearResult = $pearResult;
        GC_Utils_Ex::gfPrintLog($objPage->sfGetErrMsg());
        $objPage->process();
        
        exit();
    }

    /**
     * 直前に実行されたSQL文を取得する.
     *
     * @param boolean $disp trueの場合、画面出力を行う.
     * @return string SQL文
     */
    function getLastQuery($disp = true) {
        $sql = $this->conn->last_query;
        if($disp) {
            print($sql.";<br />\n");
        }
        return $sql;
    }
}

?>
