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

require_once(realpath(dirname(__FILE__)) . "/../module/MDB2.php");

/**
 * SQLの構築・実行を行う
 *
 * TODO エラーハンドリング, ロギング方法を見直す
 *
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Query {

    var $instance;
    var $option;
    var $where;
    var $conn;
    var $groupby;
    var $order;
    var $force_run;

    /**
     * コンストラクタ.
     *
     * @param string $dsn データソース名
     * @param boolean $force_run エラーが発生しても処理を続行する場合 true
     * @param boolean $new 新規に接続を行うかどうか
     */
    function SC_Query($dsn = "", $force_run = false, $new = false) {

        if ($dsn == "") {
            $dsn = DEFAULT_DSN;
        }

        // Debugモード指定 
        // 常時ONにするとメモリが解放されない。
        // 連続クエリ実行時に問題が生じる。
        if(DEBUG_MODE) {
            $options['debug'] = PEAR_DB_DEBUG;
        } else {
            $options['debug'] = 0;
        }

        // 持続的接続オプション
        $options['persistent'] = PEAR_DB_PERSISTENT;

        // バッファリング trueにするとメモリが解放されない。
        // 連続クエリ実行時に問題が生じる。
        $options['result_buffering'] = false;

        if ($new) {
            $this->conn = MDB2::connect($dsn, $options);
        } else {
            $this->conn = MDB2::singleton($dsn, $options);
        }
        if (!PEAR::isError($this->conn)) {
            $this->conn->setCharset(CHAR_CODE);
            $this->conn->setFetchMode(MDB2_FETCHMODE_ASSOC);
        }
        $this->dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $this->force_run = $force_run;
        $this->where = "";
        $this->order = "";
        $this->groupby = "";
        $this->option = "";
    }

    /**
     * シングルトンの SC_Query インスタンスを取得する.
     *
     * @param string $dsn データソース名
     * @param boolean $force_run エラーが発生しても処理を続行する場合 true
     * @param boolean $new 新規に接続を行うかどうか
     * @return SC_Query シングルトンの SC_Query インスタンス
     */
    function getSingletonInstance($dsn = "", $force_run = false, $new = false) {
        if (is_null($this->instance)) {
            $this->instance =& new SC_Query($dsn, $force_run, $new);
        }
        $this->instance->where = "";
        $this->instance->order = "";
        $this->instance->groupby = "";
        $this->instance->option = "";
        return $this->instance;
    }

    /**
     *  エラー判定を行う.
     *
     * @deprecated PEAR::isError() を使用して下さい
     * @return boolean
     */
    function isError() {
        if(PEAR::isError($this->conn)) {
            return true;
        }
        return false;
    }

    /**
     * COUNT文を実行する.
     *
     * @param string $table テーブル名
     * @param string $where where句
     * @param array $arrval プレースホルダ
     * @return integer 件数
     */
    function count($table, $where = "", $arrval = array()) {
        if(strlen($where) <= 0) {
            $sqlse = "SELECT COUNT(*) FROM $table";
        } else {
            $sqlse = "SELECT COUNT(*) FROM $table WHERE $where";
        }
        $sqlse = $this->dbFactory->sfChangeMySQL($sqlse);
        return $this->getOne($sqlse, $arrval);
    }

    /**
     * SELECT文を実行する.
     *
     * @param string $col カラム名. 複数カラムの場合はカンマ区切りで書く
     * @param string $table テーブル名
     * @param string $where WHERE句
     * @param array $arrval プレースホルダ
     * @param integer $fetchmode 使用するフェッチモード。デフォルトは MDB2_FETCHMODE_ASSOC。
     * @return array|null
     */
    function select($col, $table, $where = "", $arrval = array(), $fetchmode = MDB2_FETCHMODE_ASSOC) {
        $sqlse = $this->getSql($col, $table, $where);
        return $this->getAll($sqlse, $arrval, $fetchmode);
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

    /**
     * トランザクションをコミットする.
     *
     * @return MDB2_OK 成功した場合は MDB2_OK;
     *         失敗した場合は PEAR::Error オブジェクト
     */
    function commit() {
        return $this->conn->commit();
    }

    /**
     * トランザクションを開始する.
     *
     * @return MDB2_OK 成功した場合は MDB2_OK;
     *         失敗した場合は PEAR::Error オブジェクト
     */
    function begin() {
        return $this->conn->beginTransaction();
    }

    /**
     * トランザクションをロールバックする.
     *
     * @return MDB2_OK 成功した場合は MDB2_OK;
     *         失敗した場合は PEAR::Error オブジェクト
     */
    function rollback() {
        return $this->conn->rollback();
    }

    /**
     * トランザクションが開始されているかチェックする.
     *
     * @return boolean トランザクションが開始されている場合 true
     */
    function inTransaction() {
        return $this->conn->inTransaction();
    }

    /**
     * 更新系の SQL を実行する.
     *
     * この関数は SC_Query::query() のエイリアスです.
     *
     * FIXME MDB2::exec() の実装であるべき
     */
    function exec($str, $arrval = array()) {
        return $this->query($str, $arrval);
    }
    
    /**
     * クエリを実行し、結果行毎にコールバック関数を適用する
     *
     * @param callback $function コールバック先
     * @param string $sql SQL クエリ
     * @param array $arrVal プリペアドステートメントの実行時に使用される配列。配列の要素数は、クエリ内のプレースホルダの数と同じでなければなりません。 
     * @param integer $fetchmode 使用するフェッチモード。デフォルトは DB_FETCHMODE_ASSOC。
     * @return boolean 結果
     */
    function doCallbackAll($cbFunc, $sql, $arrval = array(), $fetchmode = MDB2_FETCHMODE_ASSOC) {

        $sql = $this->dbFactory->sfChangeMySQL($sql);

        $sth =& $this->prepare($sql);
        if (PEAR::isError($sth) && $this->force_run) {
            return;
        }

        $affected =& $this->execute($sth, $arrval);
        if (PEAR::isError($affected) && $this->force_run) {
            return;
        }
        
        while($data = $affected->fetchRow($fetchmode)) {
            $result = call_user_func($cbFunc, $data);
            if($result === false) {
                break;
            }
        }
        $sth->free();
        return $result;
    }

    /**
     * クエリを実行し、全ての行を返す
     *
     * @param string $sql SQL クエリ
     * @param array $arrVal プリペアドステートメントの実行時に使用される配列。配列の要素数は、クエリ内のプレースホルダの数と同じでなければなりません。 
     * @param integer $fetchmode 使用するフェッチモード。デフォルトは DB_FETCHMODE_ASSOC。
     * @return array データを含む2次元配列。失敗した場合に 0 または DB_Error オブジェクトを返します。
     */
    function getAll($sql, $arrval = array(), $fetchmode = MDB2_FETCHMODE_ASSOC) {

        $sql = $this->dbFactory->sfChangeMySQL($sql);

        $sth =& $this->prepare($sql);
        if (PEAR::isError($sth) && $this->force_run) {
            return;
        }

        $affected =& $this->execute($sth, $arrval);
        if (PEAR::isError($affected) && $this->force_run) {
            return;
        }

        return $affected->fetchAll($fetchmode);
    }

    /**
     * 構築した SELECT 文を取得する.
     *
     * @param string $col SELECT 文に含めるカラム名
     * @param string $table SELECT 文に含めるテーブル名
     * @param string $where SELECT 文に含める WHERE 句
     * @return string 構築済みの SELECT 文
     */
    function getSql($col, $table, $where = '') {
        $sqlse = "SELECT $col FROM $table";

        // 引数の$whereを優先する。
        if (strlen($where) >= 1) {
            $sqlse .= " WHERE $where";
        } elseif (strlen($this->where) >= 1) {
            $sqlse .= " WHERE " . $this->where;
        }

        $sqlse .= ' ' . $this->groupby . ' ' . $this->order . ' ' . $this->option;

        return $sqlse;
    }

    /**
     * SELECT 文の末尾に付与する SQL を設定する.
     *
     * この関数で設定した値は SC_Query::getSql() で使用されます.
     *
     * @param string $str 付与する SQL 文
     * @return SC_Query 自分自身のインスタンス
     */
    function setOption($str) {
        $this->option = $str;
        return $this;
    }

    /**
     * SELECT 文に付与する LIMIT, OFFSET 句を設定する.
     *
     * この関数で設定した値は SC_Query::getSql() で使用されます.
     * TODO MDB2::setLimit() を使用する
     *
     * @param integer $limit LIMIT 句に付与する値
     * @param integer $offset OFFSET 句に付与する値
     * @return SC_Query 自分自身のインスタンス
     */
    function setLimitOffset($limit, $offset = 0) {
        if (is_numeric($limit) && is_numeric($offset)){

            $option = " LIMIT " . $limit;
            $option.= " OFFSET " . $offset;
            $this->option .= $option;
        }
        return $this;
    }

    /**
     * SELECT 文に付与する GROUP BY 句を設定する.
     *
     * この関数で設定した値は SC_Query::getSql() で使用されます.
     *
     * @param string $str GROUP BY 句に付与する文字列
     * @return SC_Query 自分自身のインスタンス
     */
    function setGroupBy($str) {
        if (strlen($str) == 0) {
            $this->groupby = '';
        } else {
            $this->groupby = "GROUP BY " . $str;
        }
        return $this;
    }

    /**
     * SELECT 文の WHERE 句に付与する AND 条件を設定する.
     *
     * この関数で設定した値は SC_Query::getSql() で使用されます.
     *
     * @param string $str WHERE 句に付与する AND 条件の文字列
     * @return SC_Query 自分自身のインスタンス
     */
    function andWhere($str) {
        if($this->where != "") {
            $this->where .= " AND " . $str;
        } else {
            $this->where = $str;
        }
        return $this;
    }

    /**
     * SELECT 文の WHERE 句に付与する OR 条件を設定する.
     *
     * この関数で設定した値は SC_Query::getSql() で使用されます.
     *
     * @param string $str WHERE 句に付与する OR 条件の文字列
     * @return SC_Query 自分自身のインスタンス
     */
    function orWhere($str) {
        if($this->where != "") {
            $this->where .= " OR " . $str;
        } else {
            $this->where = $str;
        }
        return $this;
    }

    /**
     * SELECT 文に付与する WHERE 句を設定する.
     *
     * この関数で設定した値は SC_Query::getSql() で使用されます.
     *
     * @param string $str WHERE 句に付与する文字列
     * @return SC_Query 自分自身のインスタンス
     */
    function setWhere($str) {
        $this->where = $str;
        return $this;
    }

    /**
     * SELECT 文に付与する ORDER BY 句を設定する.
     *
     * この関数で設定した値は SC_Query::getSql() で使用されます.
     *
     * @param string $str ORDER BY 句に付与する文字列
     * @return SC_Query 自分自身のインスタンス
     */
    function setOrder($str) {
        if (strlen($str) == 0) {
            $this->order = '';
        } else {
            $this->order = "ORDER BY " . $str;
        }
        return $this;
    }

    /**
     * SELECT 文に付与する LIMIT 句を設定する.
     *
     * この関数で設定した値は SC_Query::getSql() で使用されます.
     *
     * @param integer $limit LIMIT 句に設定する値
     * @return SC_Query 自分自身のインスタンス
     */
    function setLimit($limit){
        if ( is_numeric($limit)){
            $this->option = " LIMIT " .$limit;
        }
        return $this;
    }

    /**
     * SELECT 文に付与する OFFSET 句を設定する.
     *
     * この関数で設定した値は SC_Query::getSql() で使用されます.
     *
     * @param integer $offset LIMIT 句に設定する値
     * @return SC_Query 自分自身のインスタンス
     */
    function setOffset($offset) {
        if ( is_numeric($offset)){
            $this->offset = " OFFSET " .$offset;
        }
        return $this;
    }

    /**
     * INSERT文を実行する.
     *
     * @param string $table テーブル名
     * @param array $sqlval array('カラム名' => '値',...)の連想配列
     * @return
     */
    function insert($table, $sqlval) {
        $strcol = '';
        $strval = '';
        $find = false;

        if(count($sqlval) <= 0 ) return false;
        foreach ($sqlval as $key => $val) {
            $strcol .= $key . ',';
            if(strcasecmp("Now()", $val) === 0) {
                $strval .= 'Now(),';
            } else if(strpos($val, '~') === 0) {
                $strval .= preg_replace("/^~/", "", $val);
            } else {
                $strval .= '?,';
                $arrval[] = $val;
            }
            $find = true;
        }
        if(!$find) {
            return false;
        }
        // 文末の","を削除
        $strcol = preg_replace("/,$/", "", $strcol);
        $strval = preg_replace("/,$/", "", $strval);
        $sqlin = "INSERT INTO $table(" . $strcol. ") VALUES (" . $strval . ")";
        // INSERT文の実行
        $ret = $this->query($sqlin, $arrval, false, null, MDB2_PREPARE_MANIP);

        return $ret;
    }

    /**
     * UPDATE文を実行する.
     *
     * @param string $table テーブル名
     * @param array $sqlval array('カラム名' => '値',...)の連想配列
     * @param string $where WHERE句
     * @param array $arrValIn WHERE句用のプレースホルダ配列 (従来は追加カラム用も兼ねていた)
     * @param array $arrRawSql 追加カラム
     * @param array $arrRawSqlVal 追加カラム用のプレースホルダ配列
     * @return
     */
    function update($table, $sqlval, $where = "", $arrValIn = array(), $arrRawSql = array(), $arrRawSqlVal = array()) {
        $arrCol = array();
        $arrVal = array();
        $find = false;

        foreach ($sqlval as $key => $val) {
            if (strcasecmp("Now()", $val) === 0) {
                $arrCol[] = $key . '= Now()';
            } else if(strpos('~', $val) === 0) {
                $arrCol[] = $key . '= ' . $val;
            } else {
                $arrCol[] = $key . '= ?';
                $arrVal[] = $val;
            }
            $find = true;
        }

        if ($arrRawSql != "") {
            foreach($arrRawSql as $key => $val) {
                $arrCol[] = "$key = $val";
            }
        }
        
        $arrVal = array_merge($arrVal, $arrRawSqlVal);
        
        if (empty($arrCol)) {
            return false;
        }

        // 文末の","を削除
        $strcol = implode(', ', $arrCol);

        if (is_array($arrValIn)) { // 旧版との互換用
            // プレースホルダー用に配列を追加
            $arrVal = array_merge($arrVal, $arrValIn);
        }

        $sqlup = "UPDATE $table SET $strcol";
        if (strlen($where) >= 1) {
            $sqlup .= " WHERE $where";
        }

        // UPDATE文の実行
        return $this->query($sqlup, $arrVal, false, null, MDB2_PREPARE_MANIP);
    }

    /**
     * MAX文を実行する.
     *
     * @param string $table テーブル名
     * @param string $col カラム名
     * @param string $where 付与する WHERE 句
     * @param array $arrval ブレースホルダに挿入する値
     * @return integer MAX文の実行結果
     */
    function max($col, $table, $where = "", $arrval = array()) {
        $ret = $this->get("MAX($col)", $table, $where, $arrval);
        return $ret;
    }

    /**
     * MIN文を実行する.
     *
     * @param string $table テーブル名
     * @param string $col カラム名
     * @param string $where 付与する WHERE 句
     * @param array $arrval ブレースホルダに挿入する値
     * @return integer MIN文の実行結果
     */
    function min($col, $table, $where = "", $arrval = array()) {
        $ret = $this->get("MIN($col)", $table, $where, $arrval);
        return $ret;
    }

    /**
     * SQL を構築して, 特定のカラムの値を取得する.
     *
     * @param string $table テーブル名
     * @param string $col カラム名
     * @param string $where 付与する WHERE 句
     * @param array $arrval ブレースホルダに挿入する値
     * @return mixed SQL の実行結果
     */
    function get($col, $table, $where = "", $arrval = array()) {
        $sqlse = $this->getSql($col, $table, $where);
        // SQL文の実行
        $ret = $this->getOne($sqlse, $arrval);
        return $ret;
    }

    /**
     * SQL を指定して, 特定のカラムの値を取得する.
     *
     * @param string $sql 実行する SQL
     * @param array $arrval ブレースホルダに挿入する値
     * @return mixed SQL の実行結果
     */
    function getOne($sql, $arrval = array()) {

        $sql = $this->dbFactory->sfChangeMySQL($sql);

        $sth =& $this->prepare($sql);
        if (PEAR::isError($sth) && $this->force_run) {
            return;
        }

        $affected =& $this->execute($sth, $arrval);
        if (PEAR::isError($affected) && $this->force_run) {
            return;
        }

        return $affected->fetchOne();
    }

    /**
     * 一行をカラム名をキーとした連想配列として取得
     *
     * @param string $table テーブル名
     * @param string $col カラム名
     * @param string $where WHERE句
     * @param array $arrVal プレースホルダ配列
     * @param integer $fetchmode 使用するフェッチモード。デフォルトは MDB2_FETCHMODE_ASSOC。
     * @return array array('カラム名' => '値', ...)の連想配列
     */
    function getRow($col, $table, $where = "", $arrVal = array(), $fetchmode = MDB2_FETCHMODE_ASSOC) {

        $sql = $this->getSql($col, $table, $where);
        $sql = $this->dbFactory->sfChangeMySQL($sql);

        $sth =& $this->prepare($sql);
        if (PEAR::isError($sth) && $this->force_run) {
            return;
        }

        $affected =& $this->execute($sth, $arrVal);
        if (PEAR::isError($affected) && $this->force_run) {
            return;
        }

        return $affected->fetchRow($fetchmode);
    }

    /**
     * SELECT 文の実行結果を 1行のみ取得する.
     *
     * @param string $table テーブル名
     * @param string $col カラム名
     * @param string $where 付与する WHERE 句
     * @param array $arrval ブレースホルダに挿入する値
     * @return array SQL の実行結果の配列
     */
    function getCol($col, $table, $where = "", $arrval = array()) {
        $sql = $this->getSql($col, $table, $where);
        $sql = $this->dbFactory->sfChangeMySQL($sql);

        $sth =& $this->prepare($sql);
        if (PEAR::isError($sth) && $this->force_run) {
            return;
        }

        $affected =& $this->execute($sth, $arrval);
        if (PEAR::isError($affected) && $this->force_run) {
            return;
        }

        return $affected->fetchCol($col);
    }

    /**
     * レコードの削除
     *
     * @param string $table テーブル名
     * @param string $where WHERE句
     * @param array $arrval プレースホルダ
     * @return
     */
    function delete($table, $where = "", $arrval = array()) {
        if(strlen($where) <= 0) {
            $sqlde = "DELETE FROM $table";
        } else {
            $sqlde = "DELETE FROM $table WHERE $where";
        }
        $ret = $this->query($sqlde, $arrval, false, null, MDB2_PREPARE_MANIP);
        return $ret;
    }

    /**
     * 次のシーケンス値を取得する.
     *
     * @param string $seq_name 取得するシーケンス名
     * @param integer 次のシーケンス値
     */
    function nextVal($seq_name) {
        return $this->conn->nextID($seq_name);
    }

    /**
     * 現在のシーケンス値を取得する.
     *
     * @param string $seq_name 取得するシーケンス名
     * @return integer 現在のシーケンス値
     */
    function currVal($seq_name) {
        return $this->conn->currID($seq_name);
    }

    /**
     * シーケンス値を設定する.
     *
     * @param string $seq_name シーケンス名
     * @param integer $start 設定するシーケンス値
     * @return MDB2_OK
     */
    function setVal($seq_name, $start) {
        $objManager =& $this->conn->loadModule('Manager');
        // XXX エラーハンドリングを行う
        $objManager->dropSequence($seq_name);
        return $objManager->createSequence($seq_name, $start);
    }

    /**
     * SQL を実行する.
     *
     * FIXME $ignore_errが無視されるようになっているが互換性として問題が無いか確認が必要
     *
     * @param string $n 実行する SQL 文
     * @param array $arr ブレースホルダに挿入する値
     * @param boolean $ignore_err MDB2切替で無効化されている (エラーが発生しても処理を続行する場合 true)
     * @param mixed $types プレースホルダの型指定 デフォルトnull = string
     * @param mixed $result_types 返値の型指定またはDML実行(MDB2_PREPARE_MANIP)
     * @return array SQL の実行結果の配列
     */
    function query($n ,$arr = array(), $ignore_err = false, $types = null, $result_types = MDB2_PREPARE_RESULT ){

        $n = $this->dbFactory->sfChangeMySQL($n);

        $sth =& $this->prepare($n, $types, $result_types);
        if (PEAR::isError($sth) && $this->force_run) {
            return;
        }

        $result = $this->execute($sth, $arr);
        if (PEAR::isError($result) && $this->force_run) {
            return;
        }
        
        //PREPAREの解放
        $sth->free();
        
        return $result;
    }

    /**
     * シーケンスの一覧を取得する.
     *
     * @return array シーケンス名の配列
     */
    function listSequences() {
        $objManager =& $this->conn->loadModule('Manager');
        return $objManager->listSequences();
    }

    /**
     * テーブル一覧を取得する.
     *
     * @return array テーブル名の配列
     */
    function listTables() {
        $objManager =& $this->conn->loadModule('Manager');
        return $objManager->listTables();
    }

    /**
     * テーブルのカラム一覧を取得する.
     *
     * @param string $table テーブル名
     * @return array 指定のテーブルのカラム名の配列
     */
    function listTableFields($table) {
        $objManager =& $this->conn->loadModule('Manager');
        return $objManager->listTableFields($table);
    }

    /**
     * テーブルのインデックス一覧を取得する.
     *
     * @param string $table テーブル名
     * @return array 指定のテーブルのインデックス一覧
     */
    function listTableIndexes($table) {
        $objManager =& $this->conn->loadModule('Manager');
        return $objManager->listTableIndexes($table);
    }
    
    /**
     * テーブルにインデックスを付与する
     *
     * @param string $table テーブル名
     * @param string $name インデックス名
     * @param array $definition フィールド名など　通常のフィールド指定時は、$definition=array('fields' => array('フィールド名' => array()));
     */
    function createIndex($table, $name, $definition) {
        $objManager =& $this->conn->loadModule('Manager');
        return $objManager->createIndex($table, $name, $definition);
    }

    /**
     * テーブルにインデックスを破棄する
     *
     * @param string $table テーブル名
     * @param string $name インデックス名
     */
    function dropIndex($table, $name) {
        $objManager =& $this->conn->loadModule('Manager');
        return $objManager->dropIndex($table, $name);
    }
    
    /**
     * 値を適切にクォートする.
     *
     * TODO MDB2 に対応するための暫定的な措置.
     *      ブレースホルダが使用できない実装があるため.
     *      本来であれば, MDB2::prepare() を適切に使用するべき
     *
     * @see MDB2::quote()
     * @param string $val クォートを行う文字列
     * @return string クォートされた文字列
     */
    function quote($val) {
        return $this->conn->quote($val);
    }

    /**
     * プリペアドステートメントを構築する.
     *
     * @access private
     * @param string $sql プリペアドステートメントを構築する SQL
     * @param mixed $types プレースホルダの型指定 デフォルト null
     * @param mixed $result_types 返値の型指定またはDML実行(MDB2_PREPARE_MANIP)、nullは指定無し
     * @return MDB2_Statement_Common プリペアドステートメントインスタンス
     */
    function prepare($sql, $types = null, $result_types = MDB2_PREPARE_RESULT) {
        $sth =& $this->conn->prepare($sql, $types, $result_types);
        if (PEAR::isError($sth)) {
            if (!$this->force_run) {
                trigger_error($this->traceError($sth, $sql), E_USER_ERROR);
            } else {
                error_log($this->traceError($sth, $sql), 3, LOG_REALFILE);
            }
        }
        return $sth;
    }

    /**
     * プリペアドクエリを実行する.
     *
     * @access private
     * @param MDB2_Statement_Common プリペアドステートメントインスタンス
     * @param array $arrVal ブレースホルダに挿入する配列
     * @return MDB2_Result 結果セットのインスタンス
     */
    function execute(&$sth, $arrVal = array()) {
        $timeStart = SC_Utils_Ex::sfMicrotimeFloat();
        $affected =& $sth->execute($arrVal);

        // 一定以上時間かかったSQLの場合、ログ出力する。
        if(defined('SQL_QUERY_LOG_MODE') && SQL_QUERY_LOG_MODE == true) {
            $timeEnd = SC_Utils_Ex::sfMicrotimeFloat();;
            $timeExecTime = $timeEnd - $timeStart;
            if(defined('SQL_QUERY_LOG_MIN_EXEC_TIME') && $timeExecTime >= (float)SQL_QUERY_LOG_MIN_EXEC_TIME) {
                //$logMsg = sprintf("SQL_LOG [%.2fsec]\n%s", $timeExecTime, $this->getLastQuery(false));
                $logMsg = sprintf("SQL_LOG [%.2fsec]\n%s", $timeExecTime, $sth->query);
                GC_Utils_Ex::gfPrintLog($logMsg);
            }
        }

        if (PEAR::isError($affected)) {
            $sql = isset($sth->query) ? $sth->query : '';
            if (!$this->force_run) {
                trigger_error($this->traceError($affected, $sql), E_USER_ERROR);
            } else {
                error_log($this->traceError($affected, $sql), 3, LOG_REALFILE);
            }
        }
        return $affected;
    }

    /**
     * エラーの内容をトレースする.
     *
     * @access private
     * @param PEAR::Error $error PEAR::Error インスタンス
     * @param string $sql エラーの発生した SQL 文
     * @return string トレースしたエラー文字列
     */
    function traceError($error, $sql = "") {
        $scheme = '';
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $scheme = "http://";
        } else {
            $scheme = "https://";
        }

        $err = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\n\n"
            . "SERVER_ADDR: " . $_SERVER['SERVER_ADDR'] . "\n"
            . "REMOTE_ADDR: " . $_SERVER['REMOTE_ADDR'] . "\n"
            . "USER_AGENT: " . $_SERVER['HTTP_USER_AGENT'] . "\n\n"
            . "SQL: " . $sql . "\n\n"
            . $error->getMessage() . "\n\n"
            . $error->getUserInfo() . "\n\n";

        $err .= SC_Utils_Ex::sfBacktraceToString($error->getBackTrace());

        return $err;
    }
}

?>
