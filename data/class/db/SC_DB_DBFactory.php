<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "db/dbfactory/SC_DB_DBFactory_MYSQL.php");
require_once(CLASS_PATH . "db/dbfactory/SC_DB_DBFactory_PGSQL.php");

/**
 * DBに依存した処理を抽象化するファクトリークラス.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Db_DBFactory {

    /**
     * DB_TYPE に応じた DBFactory インスタンスを生成する.
     *
     * @return mixed DBFactory インスタンス
     */
    function getInstance() {
        switch (DB_TYPE) {
        case "mysql":
            return new SC_DB_DBFactory_MYSQL();
            break;

        case "pgsql":
            return new SC_DB_DBFactory_PGSQL();
            break;

        default:
        }
    }
}
?>
