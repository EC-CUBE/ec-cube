<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
class SC_Plugin_Installer {
    
    protected $exec_func;
    
    protected $plugin_code;
    
    protected $arrPlugin;
    
    protected $arrInstallData;
    
    public function __construct($exec_func, $arrPlugin) {
        define('PLUGIN_LOG_REALFILE', DATA_REALDIR . "logs/plugin.log");
        $this->exec_func   = $exec_func;
        $this->plugin_code = $arrPlugin['plugin_code'];
        $this->arrPlugin   = $arrPlugin;
        $this->arrInstallData = array();
        $this->arrInstallData['sql'] = array();
        $this->arrInstallData['copy_file'] = array();
        $this->arrInstallData['copy_direcrtory'] = array();
        $this->arrInstallData['remove_file'] = array();
        $this->arrInstallData['remove_directory'] = array();
    }
    
    public function execInstall() {
        GC_Utils_Ex::gfPrintLog("start install: " . $this->arrPlugin['plugin_code']);
        
        $plugin_code = $this->arrPlugin['plugin_code'];

        $objQuery =& SC_Query::getSingletonInstance();
        
        // テーブル作成SQLなどを実行
        $arrSql = $this->arrInstallData['sql'];
        
        foreach ($arrSql as $sql) {
            GC_Utils_Ex::gfPrintLog("exec sql:" . $sql['sql']);
            $objQuery->query($sql['sql'], $sql['params']);
        }
        
        $arrInsertQuery = $this->arrInstallData['insert'];
        foreach ($arrInsertQuery as $insertQuery) {
            $objQuery->insert(
                    $insertQuery['table'],
                    $insertQuery['arrVal'],
                    $insertQuery['arrSql'],
                    $insertQuery['arrSqlVal'],
                    $insertQuery['form'],
                    $insertQuery['arrFromVal']
                    );
        }
        
            
        $arrInsertQuery = $this->arrInstallData['update'];
        foreach ($arrInsertQuery as $insertQuery) {
            $objQuery->update(
                    $insertQuery['table'],
                    $insertQuery['arrVal'],
                    $insertQuery['where'],
                    $insertQuery['arrWhereVal'],
                    $insertQuery['arrRawSql'],
                    $insertQuery['arrRawSqlVal']
                    );
        }
                            
        // プラグインのディレクトリコピー
        $arrCopyDirectories = $this->arrInstallData['copy_directory'];

        foreach ($arrCopyDirectories as $directory) {
            GC_Utils_Ex::gfPrintLog("exec dir copy:" . $directory['src']);
            // ディレクトリコピー -> HTML配下とDATA配下を別関数にする
            SC_Utils::copyDirectory(
                    PLUGIN_UPLOAD_REALDIR . $plugin_code . DIRECTORY_SEPARATOR . $directory['src'],
                    PLUGIN_HTML_REALDIR   . $plugin_code . DIRECTORY_SEPARATOR . $directory['dist']);
        }

        // プラグインのファイルコピー
        $arrCopyFiles = $this->arrInstallData['copy_file'];

        foreach ($arrCopyFiles as $file) {
            GC_Utils_Ex::gfPrintLog("exec file copy:" . $file['src']);
            // ファイルコピー
            copy(PLUGIN_UPLOAD_REALDIR . $plugin_code . DIRECTORY_SEPARATOR . $file['src'],
                 PLUGIN_HTML_REALDIR   . $plugin_code . DIRECTORY_SEPARATOR . $file['dist']);
        }

        GC_Utils_Ex::gfPrintLog("end install: " . $this->arrPlugin['plugin_code']);
    }
    
    public function execPlugin($exec_func) {
        $this->log("start");
        
        $plugin_code = $this->arrPlugin['plugin_code'];

        $objQuery =& SC_Query::getSingletonInstance();
        
        // テーブル作成SQLなどを実行
        $arrSql = $this->arrInstallData['sql'];
        
        foreach ($arrSql as $sql) {
            $this->log("exec sql: " . $sql['sql']);
            $objQuery->query($sql['sql'], $sql['params']);
        }
        
        // プラグインのディレクトリコピー
        $arrCopyDirectories = $this->arrInstallData['copy_directory'];

        foreach ($arrCopyDirectories as $directory) {
            $this->log("exec dir copy: " . $directory['src'] . ' -> ' . $directory['dist']);
            // ディレクトリコピー -> HTML配下とDATA配下を別関数にする
            SC_Utils::copyDirectory(
                    PLUGIN_UPLOAD_REALDIR . $plugin_code . DIRECTORY_SEPARATOR . $directory['src'],
                    PLUGIN_HTML_REALDIR   . $plugin_code . DIRECTORY_SEPARATOR . $directory['dist']);
        }

        // プラグインのファイルコピー
        $arrCopyFiles = $this->arrInstallData['copy_file'];

        foreach ($arrCopyFiles as $file) {
            $this->log("exec file copy: " . $file['src'] . ' -> ' . $file['dist']);
            // ファイルコピー
            copy(PLUGIN_UPLOAD_REALDIR . $plugin_code . DIRECTORY_SEPARATOR . $file['src'],
                 PLUGIN_HTML_REALDIR   . $plugin_code . DIRECTORY_SEPARATOR . $file['dist']);
        }
        
        $this->log("end");         
    }

    public function copyFile($src, $dist) {
        $this->arrInstallData['copy_file'][] = array(
            'src'  => $src,
            'dist' => $dist
        );
    }
 
    public function copyDirectory($src, $dist) {
        $this->arrInstallData['copy_directory'][] = array(
            'src'  => $src,
            'dist' => $dist
        );        
    }
    
    public function removeFile($dist) {
        $this->arrInstallData['remove_file'][] = array(
            'dist' => $dist
        );
    }
    
    public function removeDirectory($dist) {
       $this->arrInstallData['remove_file'][] = array(
            'dist' => $dist
        );     
    }

    public function sql($sql, array $params = array()) {
        $this->arrInstallData['sql'][] = array(
            'sql'    => $sql,
            'params' => $params
        );
    }
    
    public function query($sql, array $params = array()) {
        $this->sql($sql, $params);
    }
    
    protected function log($msg) {
        $msg = sprintf("%s %s: %s", $this->exec_func, $this->plugin_code, $msg);
        GC_Utils::gfPrintLog($msg, PLUGIN_LOG_REALFILE);
    }
    
    /**
     * カラム追加クエリの追加
     * 
     * @param type $table
     * @param type $col
     * @param type $type 
     */
    function sqlAterTableAdd($table_name, $col_name, $col_type) {
        $sql = ("ALTER TABLE $table_name ADD $col_name $col_type ");
        $this->sql($sql);
    }
    
    /**
     * カラム削除クエリの追加
     * 
     * @param type $table
     * @param type $col
     * @param type $type 
     */
    function sqlAterTableDrop($table_name, $col_name) {
        $sql = ("ALTER TABLE $table_name DROP $col_name");
        $this->sql($sql);
    }
    
    
    function sqlInsert($table, $arrVal, $arrSql = array(), $arrSqlVal = array(), $from = '', $arrFromVal = array()) {
        $this->arrInstallData['insert'] = array(
            'params'   => array(
                'table' => $table,
                'arrVal' => $arrVal, 
                'arrSql' => $arrSql, 
                'arrSqlVal' => $arrSqlVal, 
                'form' =>$from,
                'arrFromVal' => $arrFromVal)
        );
    }
    
    function sqlUpdate($table, $arrVal, $where = '', $arrWhereVal = array(), $arrRawSql = array(), $arrRawSqlVal = array()) {
        $this->arrInstallData['update'] = array(
            'params'   => array(
                'table' => $table,
                'arrVal' => $arrVal, 
                'where' => $where, 
                'arrWhereVal' => $arrWhereVal, 
                'arrRawSql' =>$arrRawSql,
                'arrRawSqlVal' => $arrRawSqlVal)
        );
    }    
}
