<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/**
 * サイト情報を取得する.
 *
 * FIXME このクラスを使用している場合は,
 * SC_Helper_DB::sf_getBasisData() に置き変えて下さい
 *
 * @deprecated SC_Helper_DB::sf_getBasisData() を使用して下さい.
 */
class SC_SiteInfo {

    var $conn;
    var $data;

    /**
     * @deprecated SC_Helper_DB::sf_getBasisData() を使用して下さい.
     *
     * FIXME この関数を使用している場合は,
     * SC_Helper_DB::sf_getBasisData() に置き変えて下さい
     *
     */
    function SC_SiteInfo($conn = ''){
        /*
        $DB_class_name = "SC_DbConn";
        if ( is_object($conn)){
            if ( is_a($conn, $DB_class_name)){
                // $connが$DB_class_nameのインスタンスである
                $this->conn = $conn;
            }
        } else {
            if (class_exists($DB_class_name)){
                //$DB_class_nameのインスタンスを作成する
                $this->conn = new SC_DbConn();
            }
        }

        if ( is_object($this->conn)){
            $conn = $this->conn;
            $sql = "SELECT * FROM dtb_baseinfo";
            $data = $conn->getAll($sql);
            $this->data = $data[0];
            }
        */
        $objDb = new SC_Helper_DB_Ex();
        $this->data = $objDb->sf_getBasisData();
    }
}
?>
