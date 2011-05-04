<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_FileManager_Ex.php';

/**
 * システム情報 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System_Plugin extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'system/plugin.tpl';
        $this->tpl_subno    = 'plugin';
        $this->tpl_mainno   = 'system';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = 'プラグイン管理';
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
        // パラメータ管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメータ情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);

        $mode = $this->getMode();

        switch($mode) {
        case 'install':
        case 'uninstall':
        case 'enable':
        case 'disable':
            // エラーチェック
            $this->arrErr = $objFormParam->checkError();

            if(count($this->arrErr) == 0) {
                $plugin_id = $objFormParam->getValue('plugin_id');
                $plugin_code = $objFormParam->getValue('plugin_code');

                // プラグインファイルを読み込み、modeで指定されたメソッドを実行
                $this->arrErr = $this->lfExecPlugin($plugin_id, $plugin_code, $mode);
            }
            break;
        case 'upload':
            // プラグイン情報を設定
            $plugin_code = $this->lfGetPluginCode($_FILES['plugin_file']['name']);
            $plugin_dir = $this->lfGetPluginDir($plugin_code);

            // ファイルアップロード情報を設定
            $objUpFile = new SC_UploadFile_Ex(TEMPLATE_TEMP_REALDIR, $plugin_dir);
            $this->lfInitUploadFile($objUpFile);

            // エラーチェック
            $this->arrErr = $this->lfCheckErrorUploadFile($plugin_code, $plugin_dir);

            if(count($this->arrErr) == 0) {
                // 一時ディレクトリへアップロード
                $this->arrErr['plugin_file'] = $objUpFile->makeTempFile('plugin_file', false);

                if($this->arrErr['plugin_file'] == "") {
                    // プラグイン保存ディレクトリへ解凍
                    $this->arrErr = $this->lfUploadPlugin($objUpFile, $plugin_dir, $plugin_code, $_FILES['plugin_file']['name']);

                    // 完了メッセージアラート設定
                    if(count($this->arrErr) == 0) {
                        $this->tpl_onload = "alert('プラグインをアップロードしました。');";
                    }
                }
            }
            break;
        case 'up':
            $this->arrErr = $objFormParam->checkError();
            if(count($this->arrErr) == 0) {
                $plugin_id = $objFormParam->getValue('plugin_id');
                SC_Helper_DB_Ex::sfRankUp("dtb_plugin", "plugin_id", $plugin_id);
                SC_Response_Ex::reload();
            }
            break;
        case 'down':
            $this->arrErr = $objFormParam->checkError();
            if(count($this->arrErr) == 0) {
                $plugin_id = $objFormParam->getValue('plugin_id');
                SC_Helper_DB_Ex::sfRankDown("dtb_plugin", "plugin_id", $plugin_id);
                SC_Response_Ex::reload();
            }
            break;

        default:
            break;
        }

        // DBからプラグイン情報を取得
        $this->plugins = SC_Helper_Plugin_Ex::getAllPlugin();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * パラメータ初期化.
     *
     * @param object $objFormParam
     * @return void
     * 
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('mode', 'mode', INT_LEN, '', array('ALPHA_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('plugin_id', 'plugin_id', INT_LEN, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('plugin_code', 'plugin_code', MTEXT_LEN, '', array('ALPHA_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * アップロードファイルパラメータ初期化.
     *
     * @param object $objUpFile SC_UploadFileのインスタンス.
     * @return void
     */
    function lfInitUploadFile(&$objUpFile) {
        $objUpFile->addFile("プラグイン", 'plugin_file', array('tar', 'tar.gz'), TEMPLATE_SIZE, true, 0, 0, false);
    }

    /**
     * アップロードファイルのエラーチェック.
     * 
     * @param string $plugin_code
     * @param string $plugin_dir
     * @return array エラー情報を格納した連想配列.
     * 
     */
    function lfCheckErrorUploadFile($plugin_code, $plugin_dir) {
        $arrErr = array();

        // プラグイン重複チェック
        $plugins = SC_Helper_Plugin_Ex::getAllPlugin();
        foreach($plugins as $val) {
            if($val['plugin_code'] == $plugin_code) {
                $arrErr['plugin_file'] = "※ 同名のプラグインがすでに登録されています。<br/>";
            }
        }

        return $arrErr;
    }

    /**
     * プラグイン名(物理名)を取得する.
     * (アップロードされたファイル名をプラグイン名(物理名)とする).
     * 
     * @param string $upload_file_name
     * @return string プラグイン名(物理名).
     * 
     */
    function lfGetPluginCode($upload_file_name) {
        $array_ext = explode(".", $upload_file_name);
        return $array_ext[0];
    }

    /**
     * プラグイン保存ディレクトリのパスを取得する.
     *
     * @param string $plugin_code
     * @return string プラグイン保存ディレクトリ.
     * 
     */
    function lfGetPluginDir($plugin_code) {
        $plugin_dir = DATA_REALDIR . 'plugin/' . $plugin_code . '/';
        return $plugin_dir;
    }

    /**
     * プラグインファイルのパスを取得する.
     * 
     * @param string $plugin_code
     * @return string プラグインファイルパス.
     */
    function lfGetPluginFilePath($plugin_code) {
        $plugin_file_path = $this->lfGetPluginDir($plugin_code) . $plugin_code . '.php';
        return $plugin_file_path;
    }
    /**
     * プラグインをアップロードする.
     * 
     * @param object $objUpFile
     * @param string $plugin_dir
     * @param string $plugin_code
     * @param string $plugin_file_name
     * @return array エラー情報を格納した連想配列.
     * 
     */
    function lfUploadPlugin(&$objUpFile, $plugin_dir, $plugin_code, $plugin_file_name) {
        $arrErr = array();

        // 必須チェック
        $arrErr = $objUpFile->checkEXISTS('plugin_file');

        if(count($arrErr) == 0) {
            // プラグイン保存ディレクトリ作成
            if(file_exists($plugin_dir)) {
                $arrErr['plugin_file'] = "※ 同名のディレクトリがすでに存在します。<br/>";
            } else {
                mkdir($plugin_dir);
            }

            if(count($arrErr) == 0) {
                // 一時ディレクトリからプラグイン保存ディレクトリへ移動
                $objUpFile->moveTempFile();

                // プラグイン保存ディレクトリへ解凍
                SC_Helper_FileManager_Ex::unpackFile($plugin_dir . $plugin_file_name);

                // プラグイン情報をDB登録
                $this->lfRegistData($plugin_dir, $plugin_code);
            }
        }

        return $arrErr;
    }

    /**
     * プラグイン情報をDB登録.
     *
     * @param string $plugin_dir
     * @param string $plugin_code
     * @return void
     *
     */
    function lfRegistData($plugin_dir, $plugin_code) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sqlval = array();

        $sqlval['plugin_id'] = $objQuery->nextVal('dtb_plugin_plugin_id');
        $sqlval['plugin_code'] = $plugin_code;
        $sqlval['rank'] = 1 + $objQuery->max('rank', 'dtb_plugin');
        $sqlval['status'] = PLUGIN_STATUS_UPLOADED;
        $sqlval['enable'] = PLUGIN_ENABLE_FALSE;
        $sqlval['update_date'] = 'now()';
        $objQuery->insert('dtb_plugin', $sqlval);
    }

    /**
     * プラグインファイルを読み込む.
     * 
     * @param string $plugin_code
     * @return array エラー情報を格納した連想配列.
     */
    function lfRequirePluginFile($plugin_code) {
        $arrErr = array();
        $plugin_file_path = $this->lfGetPluginFilePath($plugin_code);

        if(file_exists($plugin_file_path)) {
            require_once $plugin_file_path;
        } else {
            $arrErr['plugin_error'] = "※ " . $plugin_code . ".phpが存在しないため実行できません。<br/>";
        }

        return $arrErr;
    }

    /**
     * プラグインファイルを読み込み、指定されたメソッドを実行する.
     *
     * @param integer $plugin_id
     * @param string $plugin_code
     * @param string $exec_mode プラグイン実行メソッド名.
     * @return array エラー情報を格納した連想配列.
     *
     */
    function lfExecPlugin($plugin_id, $plugin_code, $exec_mode) {
        $arrErr = array();

        // プラグインファイル読み込み
        $arrErr = $this->lfRequirePluginFile($plugin_code);

        if(count($arrErr) == 0) {
            $plugin = new $plugin_code();
            $arrErr = $plugin->$exec_mode($plugin_id);
        }

        return $arrErr;
    }
}
