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
    
    protected $arrPlugin;
    
    protected $arrInstallData;
    
    function __construct($arrPlugin) {
        $this->arrPlugin = $arrPlugin;
    }
    
    function execInstall() {
        
        GC_Utils_Ex::gfPrintLog("start install");
        
        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->begin();
        
        // テーブル作成SQLなどを実行
        $arrSql = $this->arrInstallData["plugin_code"]['sql'];
        
        foreach ($arrSql as $sql) {
            GC_Utils_Ex::gfPrintLog("start install");
            $objQuery->query($sql['sql'], $sql['params']);
        }
        
        // プラグインのファイルコピー
        $arrCopyFiles = $this->arrInstallData["plugin_code"]['copy_file'];
        
        foreach ($arrCopyFiles as $file) {
            // ファイルコピー
        }
        
        $objQuery->commit();
        GC_Utils_Ex::gfPrintLog("end install");
        
    }
    
    function copyFile($sql, $dist) {
        
    }
    
    function removeFile($dist) {
        
    }
    
    function sql($sql, array $params) {
        $plugin_code = $this->arrPlugin['plugin_code'];
        $this->arrInstallData[$plugin_code]['sql'] = array(
            'sql'    => $sql,
            'params' => $params
        );
    }
}