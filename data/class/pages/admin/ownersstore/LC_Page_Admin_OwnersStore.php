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

/**
 * オーナーズストア：プラグイン管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_OwnersStore extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'ownersstore/plugin.tpl';
        $this->tpl_subno    = 'index';
        $this->tpl_mainno   = 'ownersstore';
        $this->tpl_maintitle = 'オーナーズストア';
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
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        $mode = $this->getMode();
        // パラメーター情報の初期化
        $this->initParam($objFormParam, $mode);
        $objFormParam->setParam($_POST);
 
        $mode = $this->getMode();

        switch ($mode) {
            // インストール
            case 'install':
                $file_key = 'plugin_file';
                $this->arrErr = $this->checkUploadFile($file_key);
                if ($this->isError($this->arrErr) === false) {
                    $upload_file = $_FILES[$file_key];
                    $upload_file_file_name = $upload_file['name'];
                    // インストール処理.
                    $this->arrErr = $this->installPlugin($upload_file_file_name, 'plugin_file');
                    if ($this->isError($this->arrErr) === false) {
                        // コンパイルファイルのクリア処理
                        SC_Utils_Ex::clearCompliedTemplate();
                        $this->tpl_onload = "alert('プラグインをインストールしました。');";
                    }
                }
                break;
            // 削除
            case 'uninstall':
                // エラーチェック
                $this->arrErr = $objFormParam->checkError();
                if ($this->isError($this->arrErr) === false) {
                    $plugin_id = $objFormParam->getValue('plugin_id');
                    $plugin = SC_Plugin_Util_Ex::getPluginByPluginId($plugin_id);
                    $this->arrErr = $this->uninstallPlugin($plugin);
                    if ($this->isError($this->arrErr) === false) {
                        // TODO 全プラグインのインスタンスを保持したまま後続処理が実行されるので、全てのインスタンスを解放する。
                        unset($GLOBALS['_SC_Helper_Plugin_instance']);
                        // コンパイルファイルのクリア処理
                        SC_Utils_Ex::clearCompliedTemplate();
                        $this->tpl_onload = "alert('" . $plugin['plugin_name'] ."を削除しました。');";
                    }
                }
                break;
            // 有効化
            case 'enable':

                // エラーチェック
                $this->arrErr = $objFormParam->checkError();
                if ($this->isError($this->arrErr) === false) {
                    $plugin_id = $objFormParam->getValue('plugin_id');
                    // プラグイン取得.
                    $plugin = SC_Plugin_Util_Ex::getPluginByPluginId($plugin_id);
                    $this->arrErr = $this->enablePlugin($plugin);
                    if ($this->isError($this->arrErr) === false) {
                        // TODO 全プラグインのインスタンスを保持したまま後続処理が実行されるので、全てのインスタンスを解放する。
                        unset($GLOBALS['_SC_Helper_Plugin_instance']);
                        // コンパイルファイルのクリア処理
                        SC_Utils_Ex::clearCompliedTemplate();
                        $this->tpl_onload = "alert('" . $plugin['plugin_name'] . "を有効にしました。');";
                    }
                }
                break;
            // 無効化
            case 'disable':
                // エラーチェック
                $this->arrErr = $objFormParam->checkError();
                if ($this->isError($this->arrErr) === false) {
                    $plugin_id = $objFormParam->getValue('plugin_id');
                    // プラグイン取得.
                    $plugin = SC_Plugin_Util_Ex::getPluginByPluginId($plugin_id);
                    $this->arrErr = $this->disablePlugin($plugin);
                    if ($this->isError($this->arrErr) === false) {
                        // TODO 全プラグインのインスタンスを保持したまま後続処理が実行されるので、全てのインスタンスを解放する。
                        unset($GLOBALS['_SC_Helper_Plugin_instance']);
                        // コンパイルファイルのクリア処理
                        SC_Utils_Ex::clearCompliedTemplate();
                        $this->tpl_onload = "alert('" . $plugin['plugin_name'] . "を無効にしました。');";
                    }
                }
                break;
            // アップデート.
            case 'update':
                // エラーチェック
                $this->arrErr = $objFormParam->checkError();
                if ($this->isError($this->arrErr) === false) {
                    $target_plugin_code = $objFormParam->getValue('plugin_code'); // アップデート対象のプラグインコード
                    $this->arrErr = $this->checkUploadFile($target_plugin_code);

                    if ($this->isError($this->arrErr) === false) {
                        $update_plugin_file = $_FILES[$target_plugin_code];
                        $update_plugin_file_name = $update_plugin_file['name']; // アップデートファイルのファイル名.
                        // インストール処理.
                        $target_plugin = SC_Plugin_Util_Ex::getPluginByPluginCode($target_plugin_code);
                        $this->arrErr = $this->updatePlugin($target_plugin, $update_plugin_file_name, $target_plugin_code);
                        if ($this->isError($this->arrErr) === false) {
                            // コンパイルファイルのクリア処理
                            SC_Utils_Ex::clearCompliedTemplate();
                            $this->tpl_onload = "alert('プラグインをアップデートしました。');";
                        }
                    }
                }
                break;
            // 優先度.
            case 'priority':
                // エラーチェック
                $arrErr = $objFormParam->checkError();
                $plugin_id = $objFormParam->getValue('plugin_id');
                if ($this->isError($arrErr) === false) {
                    // 優先度の更新
                    $priority = $objFormParam->getValue('priority');
                    $this->updatePriority($plugin_id, $priority);
                    // コンパイルファイルのクリア処理
                    SC_Utils_Ex::clearCompliedTemplate();
                } else {
                    // エラーメッセージを詰め直す.
                    $this->arrErr['priority'][$plugin_id] = $arrErr['priority'];
                }

                break;
            default:
                break;
        }
        // DBからプラグイン情報を取得
        $plugins = SC_Plugin_Util_Ex::getAllPlugin();

        foreach ($plugins as $key => $plugin) {
            // 設定ファイルがあるかを判定.
            $plugins[$key]['config_flg'] = $this->isContainsFile(PLUGIN_UPLOAD_REALDIR . $plugin['plugin_code'], 'config.php');
            if ($plugins[$key]['enable'] === PLUGIN_ENABLE_TRUE) {
                // 競合するプラグインがあるかを判定.
                $plugins[$key]['conflict_message']= $this->checkConflictPlugin($plugin['plugin_id']);
            }
        }
        $this->plugins = $plugins;
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
     * パラメーター初期化.
     *
     * @param SC_FormParam_Ex $objFormParam
     * @param string $mode モード
     * @return void
     */
    function initParam(&$objFormParam, $mode) {
        $objFormParam->addParam('mode', 'mode', INT_LEN, '', array('ALPHA_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('plugin_id', 'plugin_id', INT_LEN, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('plugin_code', 'plugin_code', MTEXT_LEN, '', array('ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
        if ($mode === 'priority') {
            $objFormParam->addParam('優先度', 'priority', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        }
    }

    /**
     * ファイルパラメーター初期化.
     *
     * @param SC_UploadFile_Ex $objUpFile SC_UploadFileのインスタンス.
     * @param string $key 登録するキー.
     * @return void
     */
    function initUploadFile(&$objUpFile, $key) {
        $objUpFile->addFile('プラグインファイル', $key, explode(',', PLUGIN_EXTENSION), FILE_SIZE, true, 0, 0, false);
    }

    /**
     * ファイルが指定されている事をチェックします.
     *
     * @param string $file ファイル
     * @param string $file_key ファイルキー
     * @return array エラー情報を格納した連想配列.
     */
    function checkUploadFile($file_key) {
        $objErr = new SC_CheckError_Ex();
        // 拡張子チェック
        $objErr->doFunc(array('プラグインファイル', $file_key, explode(',', PLUGIN_EXTENSION)), array('FILE_EXT_CHECK'));
        // ファイルサイズチェック
        $objErr->doFunc(array('プラグインファイル', $file_key, FILE_SIZE), array('FILE_SIZE_CHECK'));
        // ファイル名チェック
        $objErr->doFunc(array('プラグインファイル', $file_key), array('FILE_NAME_CHECK'));

        return $objErr->arrErr;
    }

    /**
     * 既にインストールされているプラグインかを判定します.
     *
     * @param string $plugin_code プラグインコード
     * @return boolean インストール済の場合true インストールされていない場合false
     */
    function isInstalledPlugin($plugin_code) {
        $plugin = SC_Plugin_Util_Ex::getPluginByPluginCode($plugin_code);
        if (!empty($plugin)) {
            return true;
        }
        return false;
    }

    /**
     * ファイル名からプラグインコードを取得する.
     *
     * ファイル名を「.」で配列に分解.
     * 配列内から拡張子として格納される可能性のある「tar」「gz」を除外すし、再度結合する.
     *
     * @param string $file_name ファイル名
     * @return string $plugin_code プラグインコード.
     */
    function getPluginCode($file_name) {
        // 分解
        $array_ext = explode('.', $file_name);
        $array_file_name = array_diff($array_ext, array('tar','gz'));
        // 結合
        $plugin_code = implode('.', $array_file_name);
        return $plugin_code;
    }

    /**
     * プラグイン保存ディレクトリのパスを取得する.
     *
     * @param string $plugin_code プラグインコード
     * @return string $plugin_dir_path プラグイン保存ディレクトリのパス.
     */
    function getPluginDir($plugin_code) {
        $plugin_dir_path = PLUGIN_UPLOAD_REALDIR . $plugin_code . '/';
        return $plugin_dir_path;
    }

    /**
     * プラグインHTMLディレクトリのパスを取得する.
     *
     * @param string $plugin_code プラグインコード
     * @return string $plugin_dir_path プラグイン保存ディレクトリのパス.
     */
    function getHtmlPluginDir($plugin_code) {
        $plugin_dir_path = PLUGIN_HTML_REALDIR . $plugin_code . '/';
        return $plugin_dir_path;
    }

    /**
     * プラグインファイルのパスを取得する.
     *
     * @param string $plugin_code プラグインコード
     * @return string $plugin_file_path クラスファイルのパス.
     */
    function getPluginFilePath($plugin_code) {
        $plugin_file_path = $this->getPluginDir($plugin_code) . $plugin_code . '.php';
        return $plugin_file_path;
    }

    /**
     * プラグインをインストールします.
     *
     * @param string $plugin_code プラグインコード.
     * @param string $key キー.
     * @return array エラー情報を格納した連想配列.
     */
    function installPlugin($upload_file_file_name, $key) {
        // インストール前に不要なファイルを消しておきます.
        SC_Helper_FileManager_Ex::deleteFile(DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR, false);
        
        $arrErr = array();
        // ファイルをチェックし一時展開用ディレクトリに展開します.
        $arrErr = $this->unpackPluginFile($upload_file_file_name, DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR, $key);
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }
        // plugin_infoを読み込み.
        $arrErr = $this->requirePluginFile(DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR . 'plugin_info.php', $key);
        if ($this->isError($arrErr) === true) {
            $this->rollBack(DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR);
            return $arrErr;
        }

        // リフレクションオブジェクトを生成.
        $objReflection = new ReflectionClass('plugin_info');
        $arrPluginInfo = $this->getPluginInfo($objReflection);
        // プラグインクラスに必須となるパラメータが正常に定義されているかチェックします.
        $arrErr = $this->checkPluginConstants($objReflection, DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR);
        if ($this->isError($arrErr) === true) {
            $this->rollBack(DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR);
            return $arrErr;
        }

        // プラグインコード
        $plugin_code = $arrPluginInfo['PLUGIN_CODE'];
        // プラグイン名
        $plugin_name = $arrPluginInfo['PLUGIN_NAME'];

        // 既にインストールされていないかを判定.
        if ($this->isInstalledPlugin($plugin_code) === true) {
            $this->rollBack(DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR);
            $arrErr['plugin_file'] = '※ ' . $plugin_name . 'は既にインストールされています。<br/>';
            return $arrErr;
        }

        // プラグイン情報をDB登録
        if ($this->registerData($arrPluginInfo) === false) {
            $this->rollBack(DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR);
            $arrErr['plugin_file'] = '※ DB登録に失敗しました。<br/>';
            return $arrErr;
        }

        // プラグイン保存ディレクトリを作成し、一時展開用ディレクトリから移動します.
        $plugin_dir_path = PLUGIN_UPLOAD_REALDIR . $plugin_code . '/';
        $this->makeDir($plugin_dir_path);
        SC_Utils_Ex::copyDirectory(DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR, $plugin_dir_path);

        // プラグイン情報を取得
        $plugin = SC_Plugin_Util_Ex::getPluginByPluginCode($plugin_code);

        // クラスファイルを読み込み.
        $plugin_class_file_path = $plugin_dir_path . $plugin['class_name'] . '.php';
        $arrErr = $this->requirePluginFile($plugin_class_file_path, $key);
        if ($this->isError($arrErr) === true) {
            $this->rollBack(DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR, $plugin['plugin_id']);
            return $arrErr;
        }
        // プラグインhtmlディレクトリ作成
        $plugin_html_dir = PLUGIN_HTML_REALDIR . $plugin_code;
        $this->makeDir($plugin_html_dir);

        $arrErr = $this->execPlugin($plugin, $plugin['class_name'], 'install');
        if ($this->isError($arrErr) === true) {
            $this->rollBack(DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR, $plugin['plugin_id'], $plugin_html_dir);
            return $arrErr;
        }

        // 不要なファイルの削除
        SC_Helper_FileManager_Ex::deleteFile(DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR, false);
        return $arrErr;
    }

    /**
     * ロールバック処理
     * インストール失敗時などに不要な一時ファイルを削除します.
     *
     * @param string $temp_dir インストール・アップデート時の一時展開用ディレクトリのパス.
     * @param string $plugin_id プラグインID.
     * @param string $plugin_html_dir プラグイン毎に生成されるhtmlディレクトリのパス.
     */
    function rollBack($temp_dir, $plugin_id = '', $plugin_html_dir ='') {
        // 一時ディレクトリを削除.
        SC_Helper_FileManager_Ex::deleteFile($temp_dir, false);
        // DBからプラグイン情報を削除
        if (empty($plugin_id) === false) {
            SC_Plugin_Util_Ex::deletePluginByPluginId($plugin_id);
        }
        // htmlディレクトリを削除
        if (empty($plugin_html_dir) === false) {
            SC_Helper_FileManager_Ex::deleteFile($plugin_html_dir, true);
        }
    }

    /**
     * プラグイン情報を取得します.
     * 
     * @param ReflectionClass $objReflection
     * @return array プラグイン情報の配列
     */
    function getPluginInfo(ReflectionClass $objReflection) {
        $arrStaticProps = $objReflection->getStaticProperties();
        $arrConstants   = $objReflection->getConstants();

        $arrPluginInfoKey = array(
            'PLUGIN_CODE',
            'PLUGIN_NAME',
            'CLASS_NAME',
            'PLUGIN_VERSION',
            'COMPLIANT_VERSION',
            'AUTHOR',
            'DESCRIPTION',
            'PLUGIN_SITE_URL',
            'AUTHOR_SITE_URL',
            'HOOK_POINTS',
        );
        $arrPluginInfo = array();
        foreach ($arrPluginInfoKey as $key) {
            // クラス変数での定義を優先
            if (isset($arrStaticProps[$key])) {
                $arrPluginInfo[$key] = $arrStaticProps[$key];
            // クラス変数定義がなければ, クラス定数での定義を読み込み.
            } elseif ($arrConstants[$key]) {
                $arrPluginInfo[$key] = $arrConstants[$key];
            } else {
                $arrPluginInfo[$key] = null;
            }
        }
        return $arrPluginInfo;
    }

    /**
     * プラグインクラス内の定数をチェックします.
     *
     * @param ReflectionClass $objReflection リフレクションオブジェクト
     * @return array エラー情報を格納した連想配列.
     */
    function checkPluginConstants(ReflectionClass $objReflection, $unpack_dir) {
        $arrErr = array();
        // プラグイン情報を取得
        $arrPluginInfo = $this->getPluginInfo($objReflection);

        if (!isset($arrPluginInfo['PLUGIN_CODE'])) {
            $arrErr['plugin_file'] = '※ PLUGIN_CODEが定義されていません。<br/>';
            return $arrErr;
        }
        if (!isset($arrPluginInfo['PLUGIN_NAME'])) {
            $arrErr['plugin_file'] = '※ PLUGIN_NAMEが定義されていません。<br/>';
            return $arrErr;
        }
        if (!isset($arrPluginInfo['CLASS_NAME'])) {
            $arrErr['plugin_file'] = '※ CLASS_NAMEが定義されていません。<br/>';
            return $arrErr;
        }
        $class_path = $unpack_dir . $arrPluginInfo['CLASS_NAME'] . '.php';
        if (file_exists($class_path) === false) {
            $arrErr['plugin_file'] = '※ CLASS_NAMEが正しく定義されていません。<br/>';
            return $arrErr;
        }
        if (!isset($arrPluginInfo['PLUGIN_VERSION'])) {
            $arrErr['plugin_file'] = '※ PLUGIN_VERSIONが定義されていません。<br/>';
            return $arrErr;
        }
        if (!isset($arrPluginInfo['COMPLIANT_VERSION'])) {
            $arrErr['plugin_file'] = '※ COMPLIANT_VERSIONが定義されていません。<br/>';
            return $arrErr;
        }
        if (!isset($arrPluginInfo['AUTHOR'])) {
            $arrErr['plugin_file'] = '※ AUTHORが定義されていません。<br/>';
            return $arrErr;
        }
        if (!isset($arrPluginInfo['DESCRIPTION'])) {
            $arrErr['plugin_file'] = '※ DESCRIPTIONが定義されていません。<br/>';
            return $arrErr;
        }
        $objErr = new SC_CheckError_Ex($arrPluginInfo);
        $objErr->doFunc(array('PLUGIN_CODE', 'PLUGIN_CODE', STEXT_LEN), array('MAX_LENGTH_CHECK','GRAPH_CHECK'));
        $objErr->doFunc(array('PLUGIN_NAME', 'PLUGIN_NAME', STEXT_LEN), array('MAX_LENGTH_CHECK'));
        $objErr->doFunc(array('CLASS_NAME', 'CLASS_NAME', STEXT_LEN), array('MAX_LENGTH_CHECK','GRAPH_CHECK'));
        $objErr->doFunc(array('PLUGIN_VERSION', 'PLUGIN_VERSION', STEXT_LEN), array('MAX_LENGTH_CHECK'));
        $objErr->doFunc(array('COMPLIANT_VERSION', 'COMPLIANT_VERSION', STEXT_LEN), array('MAX_LENGTH_CHECK'));
        $objErr->doFunc(array('AUTHOR', 'AUTHOR', STEXT_LEN), array('MAX_LENGTH_CHECK'));
        $objErr->doFunc(array('DESCRIPTION', 'DESCRIPTION', MTEXT_LEN), array('MAX_LENGTH_CHECK'));
        if (isset($arrPluginInfo['PLUGIN_SITE_URL'])) {
            $objErr->doFunc(array('PLUGIN_SITE_URL', 'PLUGIN_SITE_URL', URL_LEN), array('MAX_LENGTH_CHECK','GRAPH_CHECK'));
        }
        if (isset($arrPluginInfo['AUTHOR_SITE_URL'])) {
            $objErr->doFunc(array('AUTHOR_SITE_URL', 'AUTHOR_SITE_URL', URL_LEN), array('MAX_LENGTH_CHECK','GRAPH_CHECK'));
        }
        // エラー内容を出力用の配列にセットします.
        if ($this->isError($objErr->arrErr)) {
            $arrErr['plugin_file'] = '';
            foreach ($objErr->arrErr as $error) {
                    $arrErr['plugin_file'] .= $error;
            }
        }
        return $arrErr;
    }

    /**
     * プラグインをアップデートします.
     *
     * @param string $target_plugin アップデートするプラグインコード.
     * @param string $upload_file_name アップロードファイル名.
     * @return array エラー情報を格納した連想配列.
     */
    function updatePlugin($target_plugin, $upload_file_name) {
        // アップデート前に不要なファイルを消しておきます.
        SC_Helper_FileManager_Ex::deleteFile(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR, false);
        
        $arrErr = array();

        // ファイルをチェックし展開します.
        $arrErr = $this->unpackPluginFile($upload_file_name, DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR, $target_plugin['plugin_code']);
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }
        // plugin_infoを読み込み.
        $arrErr = $this->requirePluginFile(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'plugin_info.php', $target_plugin['plugin_code']);
        if ($this->isError($arrErr) === true) {
            $this->rollBack(DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR);
            return $arrErr;
        }
        // リフレクションオブジェクトを生成.
        $objReflection = new ReflectionClass('plugin_info');
        $arrPluginInfo = $this->getPluginInfo($objReflection);
        if ($arrPluginInfo['PLUGIN_CODE'] != $target_plugin['plugin_code']) {
            $arrErr[$target_plugin['plugin_code']] = '※ プラグインコードが一致しません。<br/>';
            return $arrErr;
        }
        
        // plugin_update.phpを読み込み.
        $arrErr = $this->requirePluginFile(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'plugin_update.php', $target_plugin['plugin_code']);
        if ($this->isError($arrErr) === true) {
            $this->rollBack(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR);
            return $arrErr;
        }
        // プラグインクラスファイルのUPDATE処理を実行.
        $arrErr = $this->execPlugin($target_plugin, 'plugin_update', 'update');

        // 保存ディレクトリの削除.
        SC_Helper_FileManager_Ex::deleteFile(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR, false);

        return $arrErr;
    }

    /**
     * ファイルをアップロードし、解凍先のディレクトリに解凍します.
     *
     * @param string $unpack_file_name 解凍ファイル名
     * @param string $unpack_dir 解凍先ディレクトリ
     * @param string $file_key ファイルキー
     * @return array エラー情報を格納した連想配列.
     */
    function unpackPluginFile($unpack_file_name, $unpack_dir, $file_key) {
        $arrErr = array();
        // 解凍ディレクトリディレクトリを作成し、一時ディレクトリからファイルを移動
        $objUpFile = new SC_UploadFile_Ex(PLUGIN_TEMP_REALDIR, $unpack_dir);
        $this->initUploadFile($objUpFile, $file_key);
        $arrErr = $objUpFile->makeTempFile($file_key, false);
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }

        // 正常にアップロードされているかをチェック.
        $arrErr = $objUpFile->checkEXISTS($file_key);
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }
        $objUpFile->moveTempFile();
        // 解凍
        $update_plugin_file_path = $unpack_dir . $unpack_file_name;
        if (!SC_Helper_FileManager_Ex::unpackFile($update_plugin_file_path)) {
            $arrErr['plugin_file'] = '※ 解凍に失敗しました。<br/>';
            return $arrErr;
        }
        return $arrErr;
    }

    /**
     * プラグインをアンインストールします.
     *
     * @param array $plugin プラグイン情報を確認した連想配列.
     * @return array エラー情報を格納した連想配列.
     */
    function uninstallPlugin($plugin) {
        $arrErr = array();
        // プラグインファイルを読み込みます.
        $plugin_class_file_path = PLUGIN_UPLOAD_REALDIR . $plugin['plugin_code'] . '/' . $plugin['class_name'] . '.php';
        $arrErr = $this->requirePluginFile($plugin_class_file_path, 'plugin_error');
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }
        
        // プラグインが有効な場合に無効化処理を実行
        if($plugin['enable'] == PLUGIN_ENABLE_TRUE){
            // 無効化処理を実行します.
            $arrErr = $this->execPlugin($plugin, $plugin['class_name'], 'disable');
            if ($this->isError($arrErr) === true) {
                return $arrErr;
            }
            // プラグインを無効にします.
            $this->updatePluginEnable($plugin['plugin_id'], PLUGIN_ENABLE_FALSE);
        }
        
        // アンインストール処理を実行します.
        $arrErr = $this->execPlugin($plugin, $plugin['class_name'], 'uninstall');
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }
        // プラグインの削除処理.
        $arrErr = $this->deletePlugin($plugin['plugin_id'], $plugin['plugin_code']);

        return $arrErr;
    }

    /**
     * プラグインを有効にします.
     *
     * @param array $plugin プラグイン情報を確認した連想配列.
     * @return array $arrErr エラー情報を格納した連想配列.
     */
    function enablePlugin($plugin) {
        $arrErr = array();
        // クラスファイルを読み込み.
        $plugin_class_file_path = PLUGIN_UPLOAD_REALDIR . $plugin['plugin_code'] . '/' . $plugin['class_name'] . '.php';
        $arrErr = $this->requirePluginFile($plugin_class_file_path, 'plugin_error');
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }
        // 有効化処理を実行します.
        $arrErr = $this->execPlugin($plugin, $plugin['class_name'], 'enable');
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }
        // プラグインを有効にします.
        $this->updatePluginEnable($plugin['plugin_id'], PLUGIN_ENABLE_TRUE);

        return $arrErr;
    }

    /**
     * プラグインを無効にします.
     *
     * @param array $plugin プラグイン情報を確認した連想配列.
     * @return array $arrErr エラー情報を格納した連想配列.
     */
    function disablePlugin($plugin) {
        $arrErr = array();
        // クラスファイルを読み込み.
        $plugin_class_file_path = PLUGIN_UPLOAD_REALDIR . $plugin['plugin_code'] . '/' . $plugin['class_name'] . '.php';
        $arrErr = $this->requirePluginFile($plugin_class_file_path, 'plugin_error');
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }

        // 無効化処理を実行します.
        $arrErr = $this->execPlugin($plugin, $plugin['class_name'], 'disable');
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }
        // プラグインを無効にします.
        $this->updatePluginEnable($plugin['plugin_id'], PLUGIN_ENABLE_FALSE);

        return $arrErr;
    }

    /**
     * 優先度を更新します.
     *
     * @param int $plugin_id プラグインID
     * @param int $priority 優先度
     * @return integer 更新件数
     */
    function updatePriority($plugin_id, $priority) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // UPDATEする値を作成する。
        $sqlval['priority'] = $priority;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = 'plugin_id = ?';
        // UPDATEの実行
        $ret = $objQuery->update('dtb_plugin', $sqlval, $where, array($plugin_id));
        return $ret;
    }

    /**
     * プラグイン情報をDB登録.
     *
     * @param array $arrPluginInfo プラグイン情報を格納した連想配列.
     * @return array エラー情報を格納した連想配列.
     */
    function registerData($arrPluginInfo) {

        // プラグイン情報をDB登録.
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        $arr_sqlval_plugin = array();
        $plugin_id = $objQuery->nextVal('dtb_plugin_plugin_id');
        $arr_sqlval_plugin['plugin_id'] = $plugin_id;
        $arr_sqlval_plugin['plugin_name'] = $arrPluginInfo['PLUGIN_NAME'];
        $arr_sqlval_plugin['plugin_code'] = $arrPluginInfo['PLUGIN_CODE'];
        $arr_sqlval_plugin['class_name'] = $arrPluginInfo['CLASS_NAME'];
        $arr_sqlval_plugin['author'] = $arrPluginInfo['AUTHOR'];
        // AUTHOR_SITE_URLが定義されているか判定.
        $author_site_url = $arrPluginInfo['AUTHOR_SITE_URL'];
        if ($author_site_url !== null) {
            $arr_sqlval_plugin['author_site_url'] = $arrPluginInfo['AUTHOR'];
        }
        // PLUGIN_SITE_URLが定義されているか判定.
        $plugin_site_url = $arrPluginInfo['PLUGIN_SITE_URL'];
        if ($plugin_site_url !== null) {
            $arr_sqlval_plugin['plugin_site_url'] = $plugin_site_url;
        }
        $arr_sqlval_plugin['plugin_version'] = $arrPluginInfo['PLUGIN_VERSION'];
        $arr_sqlval_plugin['compliant_version'] = $arrPluginInfo['COMPLIANT_VERSION'];
        $arr_sqlval_plugin['plugin_description'] = $arrPluginInfo['DESCRIPTION'];
        $arr_sqlval_plugin['priority'] = 0;
        $arr_sqlval_plugin['enable'] = PLUGIN_ENABLE_FALSE;
        $arr_sqlval_plugin['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery->insert('dtb_plugin', $arr_sqlval_plugin);

        // フックポイントをDB登録.
        $hook_point = $arrPluginInfo['HOOK_POINTS'];
        if ($hook_point !== null) {
            /**
             * FIXME コードが重複しているため、要修正
             */
            // フックポイントが配列で定義されている場合
            if (is_array($hook_point)) {
                foreach ($hook_point as $h) {
                    $arr_sqlval_plugin_hookpoint = array();
                    $id = $objQuery->nextVal('dtb_plugin_hookpoint_plugin_hookpoint_id');
                    $arr_sqlval_plugin_hookpoint['plugin_hookpoint_id'] = $id;
                    $arr_sqlval_plugin_hookpoint['plugin_id'] = $plugin_id;
                    $arr_sqlval_plugin_hookpoint['hook_point'] = $h[0];
                    $arr_sqlval_plugin_hookpoint['callback'] = $h[1];
                    $arr_sqlval_plugin_hookpoint['update_date'] = 'CURRENT_TIMESTAMP';
                    $objQuery->insert('dtb_plugin_hookpoint', $arr_sqlval_plugin_hookpoint);
                }
            // 文字列定義の場合
            } else {
                $array_hook_point = explode(',', $hook_point);
                foreach ($array_hook_point as $h) {
                    $arr_sqlval_plugin_hookpoint = array();
                    $id = $objQuery->nextVal('dtb_plugin_hookpoint_plugin_hookpoint_id');
                    $arr_sqlval_plugin_hookpoint['plugin_hookpoint_id'] = $id;
                    $arr_sqlval_plugin_hookpoint['plugin_id'] = $plugin_id;
                    $arr_sqlval_plugin_hookpoint['hook_point'] = $h;
                    $arr_sqlval_plugin_hookpoint['update_date'] = 'CURRENT_TIMESTAMP';
                    $objQuery->insert('dtb_plugin_hookpoint', $arr_sqlval_plugin_hookpoint);
                }
            }
        }
        return $objQuery->commit();
    }

    /**
     * ファイルを読み込む.
     *
     * @param string $file_path クラスのpath
     * @param string $key エラー情報のキー.
     * @return array $arrErr エラー情報を格納した連想配列.
     */
    function requirePluginFile($file_path, $key) {
        $arrErr = array();
        if (file_exists($file_path)) {
            require_once $file_path;
        } else {
            $arrErr[$key] = '※ ' . $file_path .'の読み込みに失敗しました。<br/>';
        }
        return $arrErr;
    }

    /**
     * インスタンスを生成し、指定のメソッドを実行する.
     *
     * @param object $obj インスタンス
     * @param string $class_name クラス名
     * @param string $exec_func 実行するメソッド名.
     * @return array $arrErr エラー情報を格納した連想配列.
     *
     */
    function execPlugin($obj, $class_name, $exec_func) {
        $arrErr = array();
        if (method_exists($class_name, $exec_func) === true) {
            call_user_func(array($class_name, $exec_func), $obj);
        } else {
            $arrErr['plugin_error'] = '※ ' . $class_name . '.php に' . $exec_func . 'が見つかりません。<br/>';
        }
        return $arrErr;
    }

    /**
     * plugin_idをキーにdtb_pluginのstatusを更新します.
     *
     * @param int $plugin_id プラグインID
     * @param int $enable_flg 有効フラグ
     * @return integer 更新件数
     */
    function updatePluginEnable($plugin_id, $enable_flg) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // UPDATEする値を作成する。
        $sqlval['enable'] = $enable_flg;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = 'plugin_id = ?';
        // UPDATEの実行
        $ret = $objQuery->update('dtb_plugin', $sqlval, $where, array($plugin_id));
        return $ret;
    }

    /**
     * plugin_idをキーにdtb_plugin, dtb_plugin_hookpointから物理削除します.
     *
     * @param int $plugin_id プラグインID.
     * @param string $plugin_code プラグインコード.
     * @return array $arrErr エラー情報を格納した連想配列.
     */
    function deletePlugin($plugin_id, $plugin_code) {
        $arrErr = array();
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        SC_Plugin_Util_Ex::deletePluginByPluginId($plugin_id);

        if (SC_Helper_FileManager_Ex::deleteFile($this->getPluginDir($plugin_code)) === false) {
            // TODO エラー処理
        }

        if (SC_Helper_FileManager_Ex::deleteFile($this->getHtmlPluginDir($plugin_code)) === false) {
            // TODO エラー処理
        }

        $objQuery->commit();

        return $arrErr;
    }

    /**
     * ファイルがあるかを判定します.
     *
     * @param string $plugin_dir 対象ディレクトリ.
     * @param string $file_name ファイル名.
     * @return boolean
     */
    function isContainsFile($plugin_dir, $file_name) {
        if (file_exists($plugin_dir) && is_dir($plugin_dir)) {
            if ($handle = opendir($plugin_dir)) {
                while (($item = readdir($handle)) !== false) {
                    if ($item === $file_name) return true;
                }
            }
            closedir($handle);
        }
        return false;
    }

    /**
     * アーカイブ内に指定のファイルが存在するかを判定します.
     *
     * @param Archive_Tar $tar_obj
     * @param string $file_path 判定するファイルパス
     * @return boolean
     */
    function checkContainsFile($tar_obj, $file_path) {
        // ファイル一覧を取得
        $arrayFile = $tar_obj->listContent();
        foreach ($arrayFile as  $value) {
            if ($value['filename'] === $file_path) return true;
        }
        return false;
    }

    /**
     * ディレクトリを作成します.
     *
     * @param string $dir_path 作成するディレクトリのパス
     * @return void
     */
    function makeDir($dir_path) {
        // ディレクトリ作成
        if (!file_exists($dir_path)) {
            mkdir($dir_path);
        }
    }

    /**
     * フックポイントで衝突する可能性のあるプラグインを判定.メッセージを返します.
     *
     * @param int $plugin_id プラグインID
     * @return string $conflict_alert_message メッセージ
     */
    function checkConflictPlugin($plugin_id) {
        // フックポイントを取得します.
        $hookPoints = $this->getHookPoint($plugin_id);

        $conflict_alert_message = '';
        $arrConflictPluginName = array();
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        foreach ($hookPoints as $hookPoint) {
            // 競合するプラグインを取得する,
            $table = 'dtb_plugin_hookpoint AS T1 LEFT JOIN dtb_plugin AS T2 ON T1.plugin_id = T2.plugin_id';
            $where = 'T1.hook_point = ? AND NOT T1.plugin_id = ? AND T2.enable = ' . PLUGIN_ENABLE_TRUE;
            $objQuery->setGroupBy('T1.plugin_id, T2.plugin_name');
            $conflictPlugins = $objQuery->select('T1.plugin_id, T2.plugin_name', $table, $where, array($hookPoint['hook_point'], $hookPoint['plugin_id']));

            // プラグイン名重複を削除する為、専用の配列に格納し直す.
            foreach ($conflictPlugins as $conflictPlugin) {
                // プラグイン名が見つからなければ配列に格納
                if (!in_array($conflictPlugin['plugin_name'], $arrConflictPluginName)) {
                    $arrConflictPluginName[] = $conflictPlugin['plugin_name'];
                }
            }
        }
        // メッセージをセットします.
        foreach ($arrConflictPluginName as $conflictPluginName) {
            $conflict_alert_message .= '* ' .  $conflictPluginName . 'と競合する可能性があります。<br/>';
        }
        return $conflict_alert_message;
    }

    /**
     * エラー情報が格納されているか判定します.
     *
     * @param array $arrErr エラー情報を格納した連想配列.
     * @return boolean.
     */
    function isError($error) {
        if (is_array($error) && count($error) > 0) {
            return true;
        }
        return false;
    }

    /**
     * プラグインIDからフックポイントを取得します,
     *
     * @param string $plugin_id プラグインID
     * @return array フックポイントの連想配列.
     */
    function getHookPoint($plugin_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $table = 'dtb_plugin_hookpoint';
        $where = 'plugin_id = ?';
        return $objQuery->select('*', $table, $where, array($plugin_id));
    }
}
