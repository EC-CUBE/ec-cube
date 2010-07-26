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

$current_dir = realpath(dirname(__FILE__));
require_once($current_dir . "/../module/MDB2.php");

$g_arr_objDbConn = array();

class SC_DbConn {

    var $conn;
    var $result;
    var $includePath;
    var $dsn;
    var $err_disp = true;
    var $dbFactory;


    // コンストラクタ
    function SC_DbConn($dsn = "", $err_disp = true, $new = false){
        global $g_arr_objDbConn;

        // Debugモード指定
        $options['debug'] = PEAR_DB_DEBUG;
        // 持続的接続オプション
        $options['persistent'] = PEAR_DB_PERSISTENT;

        if (strlen($dsn) >= 1) {
            $this->dsn = $dsn;
        } elseif (defined('DEFAULT_DSN')) {
            $this->dsn = DEFAULT_DSN;
        } else {
            // XXX 以前の仕様を継承しているが、意図が良く分からない。(2010/03/03 Seasoft 塚田)
            return;
        }

        // 既に接続されていないか、新規接続要望の場合は接続する。
        if (!isset($g_arr_objDbConn[$this->dsn]) || !isset($g_arr_objDbConn[$this->dsn]->connection)) {
            $new = true;
        }

        if ($new) {
            // TODO singleton の方が良いかも
            $this->conn = MDB2::connect($this->dsn, $options);
            $g_arr_objDbConn[$this->dsn] = $this->conn;

            // TODO MDB2::setCharset() を使った方が良い?
            if (DB_TYPE == 'mysql') {
                $g_arr_objDbConn[$this->dsn]->query('SET NAMES utf8'); // FIXME mysql_set_charset を使える環境では、その方が良さそう (2010/03/03 Seasoft 塚田)
                $g_arr_objDbConn[$this->dsn]->query("SET SESSION sql_mode = 'ANSI'");
            }
        } else {
            $this->conn = $g_arr_objDbConn[$this->dsn];
        }

        $this->conn->setFetchMode(MDB2_FETCHMODE_ASSOC);
        $this->err_disp = $err_disp;
        $this->dbFactory = SC_DB_DBFactory_Ex::getInstance();
    }

    // クエリの実行
    function query($n ,$arr = array(), $ignore_err = false){
        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $n = $this->dbFactory->sfChangeMySQL($n);

        $sth = $this->conn->prepare($n);
        if ( $arr ) {
            $result = $sth->execute($arr);
        } else {
            $result = $sth->execute();
        }

        if ($this->conn->isError($result) && !$ignore_err){
            $this->send_err_mail($result, $n);
        }

        $this->result = $result;
        return $this->result;
    }

    // 一件のみ取得
    function getOne($n, $arr = array()){

        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $n = $this->dbFactory->sfChangeMySQL($n);

        $sth = $this->conn->prepare($n);
        if ( $arr ) {
            $affected = $sth->execute($arr);
        } else {
            $affected = $sth->execute();
        }

        if ($this->conn->isError($affected)){
            $this->send_err_mail($affected ,$n);
        }
        $this->result = $affected->fetchOne();
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
    function getRow($sql, $arrVal = array(), $fetchmode = MDB2_FETCHMODE_ASSOC) {
        
        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $sql = $this->dbFactory->sfChangeMySQL($sql);

        $sth = $this->conn->prepare($sql);
        if ($arrVal) {
            $affected = $sth->execute($arrVal);
        } else {
            $affected = $sth->execute();
        }
        if ($this->conn->isError($affected)) {
            $this->send_err_mail($affected, $sql);
        }
        $this->result = $affected->fetchRow($fetchmode);
        
        return $this->result;
    }

    function getCol($n, $col, $arr = array()) {

        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $n = $this->dbFactory->sfChangeMySQL($n);

        $sth = $this->conn->prepare($n);
        if ($arr) {
            $affected = $sth->execute($arr);
        } else {
            $affected = $sth->execute();
        }
        if ($this->conn->isError($affected)) {
            $this->send_err_mail($affected, $n);
        }
        $this->result = $affected->fetchCol($col);
        return $this->result;
    }

    /**
     * クエリを実行し、全ての行を返す
     *
     * @param string $sql SQL クエリ
     * @param array $arrVal プリペアドステートメントの実行時に使用される配列。配列の要素数は、クエリ内のプレースホルダの数と同じでなければなりません。 
     * @param integer $fetchmode 使用するフェッチモード。デフォルトは DB_FETCHMODE_ASSOC。
     * @return array データを含む2次元配列。失敗した場合に 0 または DB_Error オブジェクトを返します。
     */
    function getAll($sql, $arrVal = array(), $fetchmode = MDB2_FETCHMODE_ASSOC) {

        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $sql = $this->dbFactory->sfChangeMySQL($sql);

        // XXX このエラー処理はここで行なうべきなのか疑問。また、戻り値も疑問(なお、変更時はドキュメントも変更を)。
        if (PEAR::isError($this->conn)) {
            if (ADMIN_MODE) {
                SC_Utils_Ex::sfErrorHeader("DBへの接続に失敗しました。:" . $this->dsn);
            } else {
                SC_Utils_Ex::sfErrorHeader("DBへの接続に失敗しました。:");
            }
            return 0;
        }

        $sth = $this->conn->prepare($sql);

        if ($arrVal) { // FIXME 判定が曖昧
            $affected = $sth->execute($arrVal);
        } else {
            $affected = $sth->execute();
        }

        if ($this->conn->isError($affected)) {
            $this->send_err_mail($affected, $sql);
        }
        $this->result = $affected->fetchAll($fetchmode);

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

        $errmsg = $sql . "\n\n";

        // PEAR エラーを伴う場合
        if (!is_null($pearResult)) {
            $errmsg .= $pearResult->message . "\n\n";
            $errmsg .= $pearResult->userinfo . "\n\n";
            $errmsg .= SC_Utils_Ex::sfBacktraceToString($pearResult->backtrace);
        }
        // (上に該当せず)バックトレースを生成できる環境(一般的には PHP 4 >= 4.3.0, PHP 5)の場合
        else if (function_exists("debug_backtrace")) {
            $errmsg .= SC_Utils_Ex::sfBacktraceToString(array_slice(debug_backtrace(), 2));
        }

        GC_Utils_Ex::gfPrintLog($errmsg);
        trigger_error($errmsg, E_USER_ERROR);
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
