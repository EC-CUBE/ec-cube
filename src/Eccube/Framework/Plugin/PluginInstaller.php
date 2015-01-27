<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework\Plugin;

use Eccube\Application;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;
use Eccube\Framework\Query;

class PluginInstaller
{
    protected $exec_func;

    protected $plugin_code;

    protected $arrPlugin;

    protected $arrInstallData;

    /**
     * @param string $exec_func
     */
    public function __construct($exec_func, $arrPlugin)
    {
        $this->exec_func   = $exec_func;
        $this->plugin_code = $arrPlugin['plugin_code'];
        $this->arrPlugin   = $arrPlugin;
        $this->arrInstallData = array();
        $this->arrInstallData['sql'] = array();
        $this->arrInstallData['copy_file'] = array();
        $this->arrInstallData['copy_direcrtory'] = array();
        $this->arrInstallData['insert'] = array();
        $this->arrInstallData['update'] = array();
        $this->arrInstallData['delete'] = array();
        $this->arrInstallData['remove_file'] = array();
        $this->arrInstallData['remove_directory'] = array();
    }

    public function execPlugin()
    {
        $this->log("start");

        $plugin_code = $this->arrPlugin['plugin_code'];

        // テーブル作成SQLなどを実行
        $arrSql = $this->arrInstallData['sql'];
        $arrErr = array();

        // SQLの検証
        foreach ($arrSql as $sql) {
            $this->log("verify sql: " . $sql['sql']);
            $error_message = $this->verifySql($sql['sql'], $sql['params']);
            if (!is_null($error_message)) {
                $this->log("verify sql: invalid sql " . $sql['sql']);
                $this->log("verify sql: $error_message");
                $arrErr[] = $error_message;
            }
        }

        if (count($arrErr) > 0) {
            return $arrErr;
        }

        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');

        // SQLの実行
        if (!Utils::isBlank($arrSql)) {
            foreach ($arrSql as $sql) {
                $this->log("exec sql: " . $sql['sql']);
                $objQuery->query($sql['sql'], $sql['params']);
            }
        }

        $arrInsertQuery = $this->arrInstallData['insert'];
        if (!Utils::isBlank($arrInsertQuery)) {
            foreach ($arrInsertQuery as $query) {
                $objQuery->insert(
                        $query['table'],
                        $query['arrVal'],
                        $query['arrSql'],
                        $query['arrSqlVal'],
                        $query['form'],
                        $query['arrFromVal']
                );
            }
        }

        $arrUpdateQuery = $this->arrInstallData['update'];
        if (!Utils::isBlank($arrUpdateQuery)) {
            foreach ($arrUpdateQuery as $query) {
                $objQuery->update(
                        $query['table'],
                        $query['arrVal'],
                        $query['where'],
                        $query['arrWhereVal'],
                        $query['arrRawSql'],
                        $query['arrRawSqlVal']
                );
            }
        }

        // プラグインのディレクトリコピー
        $arrCopyDirectories = $this->arrInstallData['copy_directory'];

        if (!Utils::isBlank($arrCopyDirectories)) {
            foreach ($arrCopyDirectories as $directory) {
                $this->log("exec dir copy: " . $directory['src'] . ' -> ' . $directory['dist']);
                // ディレクトリコピー -> HTML配下とDATA配下を別関数にする
                Utils::copyDirectory(
                        PLUGIN_UPLOAD_REALDIR . $plugin_code . DIRECTORY_SEPARATOR . $directory['src'],
                        PLUGIN_HTML_REALDIR   . $plugin_code . DIRECTORY_SEPARATOR . $directory['dist']);
            }
        }

        // プラグインのファイルコピー
        $arrCopyFiles = $this->arrInstallData['copy_file'];

        if (!Utils::isBlank($arrCopyFiles)) {
            foreach ($arrCopyFiles as $file) {
                $this->log("exec file copy: " . $file['src'] . ' -> ' . $file['dist']);
                // ファイルコピー
                copy(PLUGIN_UPLOAD_REALDIR . $plugin_code . DIRECTORY_SEPARATOR . $file['src'],
                     PLUGIN_HTML_REALDIR   . $plugin_code . DIRECTORY_SEPARATOR . $file['dist']);
            }
        }

        $this->log("end");
    }

    public function copyFile($src, $dist)
    {
        $this->arrInstallData['copy_file'][] = array(
            'src'  => $src,
            'dist' => $dist
        );
    }

    public function copyDirectory($src, $dist)
    {
        $this->arrInstallData['copy_directory'][] = array(
            'src'  => $src,
            'dist' => $dist
        );
    }

    public function removeFile($dist)
    {
        $this->arrInstallData['remove_file'][] = array(
            'dist' => $dist
        );
    }

    public function removeDirectory($dist)
    {
       $this->arrInstallData['remove_directory'][] = array(
            'dist' => $dist
        );
    }

    public function sql($sql, array $params = array())
    {
        $this->arrInstallData['sql'][] = array(
            'sql'    => $sql,
            'params' => $params
        );
    }

    protected function log($msg)
    {
        $msg = sprintf("%s %s: %s", $this->plugin_code, $this->exec_func, $msg);
        GcUtils::gfPrintLog($msg, PLUGIN_LOG_REALFILE);
    }

    /**
     * カラム追加クエリの追加
     *
     */
    public function addColumn($table_name, $col_name, $col_type)
    {
        $sql = "ALTER TABLE $table_name ADD $col_name $col_type ";
        $this->sql($sql);
    }

    /**
     * カラム削除クエリの追加
     *
     */
    public function dropColumn($table_name, $col_name)
    {
        $sql = "ALTER TABLE $table_name DROP $col_name";
        $this->sql($sql);
    }

    public function insert($table, $arrVal, $arrSql = array(), $arrSqlVal = array(), $from = '', $arrFromVal = array())
    {
        $this->arrInstallData['insert'][] = array(
            'table' => $table,
            'arrVal' => $arrVal,
            'arrSql' => $arrSql,
            'arrSqlVal' => $arrSqlVal,
            'form' =>$from,
            'arrFromVal' => $arrFromVal
        );
    }

    public function update($table, $arrVal, $where = '', $arrWhereVal = array(), $arrRawSql = array(), $arrRawSqlVal = array())
    {
        $this->arrInstallData['update'][] = array(
            'table' => $table,
            'arrVal' => $arrVal,
            'where' => $where,
            'arrWhereVal' => $arrWhereVal,
            'arrRawSql' =>$arrRawSql,
            'arrRawSqlVal' => $arrRawSqlVal
        );
    }

    /**
     *
     * @param string $sql
     * @param type   $params
     */
    protected function verifySql($sql, $params)
    {
        // FIXME $paramsのチェックも行いたい.
        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');

        // force runを有効にし, システムエラーを回避する
        $objQuery->force_run = true;

        // prepareでSQLを検証
        $sth = $objQuery->prepare($sql);

        if (\PEAR::isError($sth)) {
            $error_message = $sth->message . ":" . $sth->userinfo;
            $objQuery->force_run = false;

            return $error_message;
        }

        $sth->free();
        // force_runをもとに戻す.
        $objQuery->force_run = false;

        return $error_message;
    }
}
