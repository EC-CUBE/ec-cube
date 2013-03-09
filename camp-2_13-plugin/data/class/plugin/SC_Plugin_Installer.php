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
    
    protected $plugin_code;
    
    protected $arrPlugin;
    
    protected $arrInstallData;
    
    public function __construct($arrPlugin) {
        $this->arrPlugin   = $arrPlugin;
        $this->arrInstallData = array();
        $this->arrInstallData['sql'] = array();
        $this->arrInstallData['copy_file'] = array();
        $this->arrInstallData['copy_direcrtory'] = array();
        $this->arrInstallData['remove_file'] = array();
        $this->arrInstallData['remove_directory'] = array();
    }
    
    public function execInstall() {
        GC_Utils_Ex::gfPrintLog("start install");
        
        $plugin_code = $this->arrPlugin['plugin_code'];

        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->begin();
        
        // テーブル作成SQLなどを実行
        $arrSql = $this->arrInstallData['sql'];
        
        foreach ($arrSql as $sql) {
            GC_Utils_Ex::gfPrintLog("exec sql:" . $sql['sql']);
            $objQuery->query($sql['sql'], $sql['params']);
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

        $objQuery->commit();
        GC_Utils_Ex::gfPrintLog("end install");
        
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
}