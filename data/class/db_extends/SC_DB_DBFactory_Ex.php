<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "db/SC_DB_DBFactory.php");
require_once(CLASS_PATH . "db_extends/dbfactory/SC_DB_DBFactory_MYSQL_Ex.php");
require_once(CLASS_PATH . "db_extends/dbfactory/SC_DB_DBFactory_PGSQL_Ex.php");

/**
 * DBに依存した処理を抽象化するファクトリークラス(拡張).
 *
 * SC_DB_DBFactory をカスタマイズする場合はこのクラスを編集する.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_DB_DBFactory_Ex extends SC_DB_DBFactory {

    // }}}
    // {{{ functions

    /**
     * DB_TYPE に応じた DBFactory インスタンスを生成する.
     *
     * @return mixed DBFactory インスタンス
     */
    function getInstance() {
        switch (DB_TYPE) {
        case "mysql":
            return new SC_DB_DBFactory_MYSQL_Ex();
            break;

        case "pgsql":
            return new SC_DB_DBFactory_PGSQL_Ex();
            break;

        default:
        }
    }
}
?>
