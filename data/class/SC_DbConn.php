<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

$current_dir = realpath(dirname(__FILE__));
require_once($current_dir . "/../module/DB.php");

$objDbConn = "";

class SC_DbConn{

    var $conn;
    var $result;
    var $includePath;
    var $error_mail_to;
    var $error_mail_title;
    var $dsn;
    var $err_disp = true;

    // コンストラクタ
    function SC_DbConn($dsn = "", $err_disp = true, $new = false){
        global $objDbConn;

        // Debugモード指定
        $options['debug'] = PEAR_DB_DEBUG;
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
        $this->conn = $objDbConn;
        $this->error_mail_to = DB_ERROR_MAIL_TO;
        $this->error_mail_title = DB_ERROR_MAIL_SUBJECT;
        $this->err_disp = $err_disp;
    }

    // クエリの実行
    function query($n ,$arr = "", $ignore_err = false){
        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $n = sfChangeMySQL($n);

        if ( $arr ) {
            $result = $this->conn->query($n, $arr);
        } else {
            $result = $this->conn->query($n);
        }

        if ($this->conn->isError($result) && !$ignore_err){
            $this->send_err_mail ($result, $n);
        }

        $this->result = $result;
        return $this->result;
    }

    // 一件のみ取得
    function getOne($n, $arr = ""){

        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $n = sfChangeMySQL($n);

        if ( $arr ) {
            $result = $this->conn->getOne($n, $arr);
        } else {
            $result = $this->conn->getOne($n);
        }
        if ($this->conn->isError($result)){
            $this->send_err_mail ($result ,$n);
        }
        $this->result = $result;

        return $this->result;
    }

    function getRow($n, $arr = ""){

        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $n = sfChangeMySQL($n);

        if ( $arr ) {
            $result = $this->conn->getRow($n, $arr);
        } else {
            $result = $this->conn->getRow($n);
        }
        if ($this->conn->isError($result)){
            $this->send_err_mail ($result ,$n);
        }
        $this->result = $result;
        return $this->result;
    }

    // SELECT文の実行結果を全て取得
    function getAll($n, $arr = ""){

        // mysqlの場合にはビュー表を変換する
        if (DB_TYPE == "mysql") $n = sfChangeMySQL($n);

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
            $this->send_err_mail ($result, $n);
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
            $this->send_err_mail ($result, $n);
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

    function send_err_mail($result, $sql){
        $url = '';
        $errmsg = '';

        if ($_SERVER['HTTPS'] == "on") {
            $url = "https://";
        } else {
            $url = "http://";
        }
        $url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $errmsg  = $url."\n\n";
        $errmsg .= $sql . "\n";
        $errmsg .= $result->message . "\n\n";
        $errmsg .= $result->userinfo . "\n\n";

        if ($this->err_disp && DEBUG_MODE === true) {
            print('<pre>');
            print_r(htmlspecialchars($errmsg, ENT_QUOTES, CHAR_CODE));
            print('</pre>');
        }

        GC_Utils_Ex::gfDebugLog($errmsg);

        exit();
    }
}

?>
