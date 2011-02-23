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

// {{{ requires
require_once(CLASS_EX_REALDIR . "page_extends/admin/LC_Page_Admin_Ex.php");

/**
 * 店舗基本情報 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System_AdminArea extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'system/adminarea.tpl';
        $this->tpl_subnavi = 'system/subnavi.tpl';
        $this->tpl_subno = 'adminarea';
        $this->tpl_mainno = 'adminarea';
        $this->tpl_subtitle = '管理画面設定';
        $this->tpl_enable_ssl = FALSE;
        if(strpos(HTTPS_URL,"https://") !== FALSE){
            $this->tpl_enable_ssl = TRUE;
        }
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        if(count($_POST) >= 1 ) {
            // POSTデータの引き継ぎ
            $this->arrForm = $_POST;

            // 入力データの変換
            $this->arrForm = $this->lfConvertParam($this->arrForm);
            // 入力データのエラーチェック
            $this->arrErr = $this->lfErrorCheck($this->arrForm);
            //設定ファイルの権限チェック
            if(!is_writable(CONFIG_REALFILE)){
                $this->arrErr["all"] = CONFIG_REALFILE . ' を変更する権限がありません。';
            }
            //管理画面ディレクトリのチェック
            $this->lfCheckAdminArea($this->arrForm);

            if(count($this->arrErr) == 0) {
                $this->lfUpdateAdminData($this->arrForm);	// 既存編集
                $this->tpl_onload = "window.alert('管理機能の設定を変更しました。URLを変更した場合は、新しいURLにアクセスしてください。');";
            }else{
                $this->tpl_onload = "window.alert('設定内容に誤りがあります。設定内容を確認してください。');";                
            }
        } else {
            $admin_dir = str_replace("/","",ADMIN_DIR);
            $this->arrForm = array("admin_dir"=>$admin_dir,"admin_force_ssl"=>ADMIN_FORCE_SSL,"admin_allow_hosts"=>"");
            if(defined("ADMIN_ALLOW_HOSTS")){
                $allow_hosts = unserialize(ADMIN_ALLOW_HOSTS);
                $this->arrForm["admin_allow_hosts"] = implode("\n",$allow_hosts);
            }
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
    
    
    
    //管理機能ディレクトリのチェック
    function lfCheckAdminArea($array){
        $admin_dir = trim($array['admin_dir'])."/";

        $installData = file(CONFIG_REALFILE, FILE_IGNORE_NEW_LINES);
        foreach($installData as $key=>$line){
            if(strpos($line,"ADMIN_DIR") !== false and ADMIN_DIR != $admin_dir){
                //既存ディレクトリのチェック
                if(file_exists(HTML_REALDIR.$admin_dir) and $admin_dir != "admin/"){
                    $this->arrErr["admin_dir"] .= ROOT_URLPATH.$admin_dir."は既に存在しています。別のディレクトリ名を指定してください。";
                }
                //権限チェック
                if(!is_writable(HTML_REALDIR . ADMIN_DIR)){
                    $this->arrErr["admin_dir"] .= ROOT_URLPATH.ADMIN_DIR."のディレクトリ名を変更する権限がありません。";
                }
            }
        }
    }

    
    //管理機能ディレクトリのリネームと CONFIG_REALFILE の変更
    function lfUpdateAdminData($array){
        $admin_dir = trim($array['admin_dir'])."/";
        $admin_force_ssl = "FALSE";
        if($array['admin_force_ssl'] == 1){
            $admin_force_ssl = "TRUE";
        }
        $admin_allow_hosts = explode("\n",$array['admin_allow_hosts']);
        foreach($admin_allow_hosts as $key=>$host){
            $host = trim($host);
            if(strlen($host) >= 8){
                $admin_allow_hosts[$key] = $host;
            }else{
                unset($admin_allow_hosts[$key]);
            }
        }
        $admin_allow_hosts = serialize($admin_allow_hosts);

        // CONFIG_REALFILE の書き換え
        $installData = file(CONFIG_REALFILE, FILE_IGNORE_NEW_LINES);
        $diff = 0;
        foreach($installData as $key=>$line){
            if(strpos($line,"ADMIN_DIR") !== false and ADMIN_DIR != $admin_dir){
                $installData[$key] = 'define("ADMIN_DIR","'.$admin_dir.'");';
                //管理機能ディレクトリのリネーム
                rename(HTML_REALDIR.ADMIN_DIR,HTML_REALDIR.$admin_dir);
                $diff ++;
            }
            
            if(strpos($line,"ADMIN_FORCE_SSL") !== false){
                $installData[$key] = 'define("ADMIN_FORCE_SSL",'.$admin_force_ssl.');';
                $diff ++;
            }
            if(strpos($line,"ADMIN_ALLOW_HOSTS") !== false and ADMIN_ALLOW_HOSTS != $admin_allow_hosts) {
                $installData[$key] = "define('ADMIN_ALLOW_HOSTS','".$admin_allow_hosts."');";
                $diff ++;
            }
        }
        
        if($diff > 0) {
            $fp = fopen(CONFIG_REALFILE,"wb");
            $installData = implode("\n",$installData);
            echo $installData;
            fwrite($fp, $installData);
            fclose($fp);
        }
        return true;
    }

    /* 取得文字列の変換 */
    function lfConvertParam($array) {
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         *  a :  全角英数字を半角英数字に変換する
         */
        $arrConvList = array('admin_dir'=>"a",'admin_force_ssl' => "n",'admin_allow_hosts' => "a");
        return SC_Utils_Ex::mbConvertKanaWithArray($array, $arrConvList);
    }

    // 入力エラーチェック
    function lfErrorCheck($array) {
        $objErr = new SC_CheckError($array);

        //管理機能設定チェック
        $objErr->doFunc(array('ディレクトリ名', "admin_dir", ID_MAX_LEN) ,array("EXIST_CHECK","SPTAB_CHECK", "ALNUM_CHECK"));
        $objErr->doFunc(array('SSL制限', "admin_force_ssl", 1) ,array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('IP制限', "admin_allow_hosts", LTEXT_LEN) ,array("IP_CHECK", "MAX_LENGTH_CHECK"));
        return $objErr->arrErr;
    }
}
?>
