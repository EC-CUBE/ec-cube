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
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);

        $mode = $this->getMode();        

        switch ($mode) {
            // インストール
            case 'install':
                $file_key = "plugin_file";
                $this->arrErr = $this->checkUploadFile($file_key);
                if ($this->isError($this->arrErr) === false) {
                    $plugin_file = $_FILES[$file_key];
                    $plugin_file_name = $plugin_file['name'];
                    $plugin_code = $this->getPluginCode($plugin_file_name);

                    // 既に登録されていないか判定.
                    if ($this->isInstalledPlugin($plugin_code) === false) {
                        // インストール処理.
                        $this->arrErr = $this->installPlugin($plugin_code, $plugin_file_name);
                        if ($this->isError($this->arrErr) === false) {
                            // テンプレート再生成.
                            $this->remakeTemplate();
                            $this->tpl_onload = "alert('プラグインをインストールしました。');";
                        }
                    } else {
                        $this->arrErr[$file_key] = "※ 既にインストールされているプラグインです。<br/>";
                    }
                }
                break;
            // 削除
            case 'uninstall':
                // エラーチェック
                $this->arrErr = $objFormParam->checkError();
                if ($this->isError($this->arrErr) === false) {
                    $plugin_code = $objFormParam->getValue('plugin_code');
                    $plugin_id = $objFormParam->getValue('plugin_id');

                    $this->arrErr = $this->uninstallPlugin($plugin_id, $plugin_code);
                    // 完了メッセージアラート設定.
                    if ($this->isError($this->arrErr) === false) {
                        $plugin = SC_Helper_Plugin_Ex::getPluginByPluginId($plugin_id);
                        // テンプレート再生成.
                        $this->remakeTemplate();
                        $this->tpl_onload = "alert('" . $plugin['plugin_name'] ."を削除しました。');";
                    }
                }
                break;
            // 有効化
            case 'enable':
                // エラーチェック
                $arrErr = $objFormParam->checkError();
                if ($this->isError($arrErr) === false) {
                    $plugin_id = $objFormParam->getValue('plugin_id');
                    // プラグイン取得.
                    $plugin = SC_Helper_Plugin_Ex::getPluginByPluginId($plugin_id);
                    // ステータス更新
                    $arrErr = $this->enablePlugin($plugin_id, $plugin['plugin_code']);                    
                    if ($this->isError($arrErr) === false) {
                        // テンプレート再生成.
                        $this->remakeTemplate();
                        echo SC_Utils_Ex::jsonEncode(array('message' => $plugin['plugin_name'] . "を有効にしました。"));
                    }
                }
                exit;
                break;
            // 無効化
            case 'disable':
                // エラーチェック
                $arrErr = $objFormParam->checkError();
                if ($this->isError($arrErr) === false) {
                    $plugin_id = $objFormParam->getValue('plugin_id');
                    // プラグイン取得.
                    $plugin = SC_Helper_Plugin_Ex::getPluginByPluginId($plugin_id);
                    // プラグインを無効にします
                    $arrErr = $this->disablePlugin($plugin_id, $plugin['plugin_code']);                    
                    if ($this->isError($arrErr) === false) {
                        // テンプレート再生成.
                        $this->remakeTemplate();
                        echo SC_Utils_Ex::jsonEncode(array('message' => $plugin['plugin_name'] . "を無効にしました。"));
                    }
                }
                exit;
                break;
            // アップデート.
            case 'update':
                // エラーチェック
                $this->arrErr = $objFormParam->checkError();
                if ($this->isError($this->arrErr) === false) {
                    $plugin_code = $objFormParam->getValue('plugin_code'); // アップデート対象のプラグインコード
                    $this->arrErr = $this->checkUploadFile($plugin_code);

                    if ($this->isError($this->arrErr) === false) {
                        $update_plugin_file = $_FILES[$plugin_code];
                        $update_plugin_file_name = $update_plugin_file['name']; // アップデートファイルのファイル名.
                        $update_plugin_code = $this->getPluginCode($update_plugin_file_name); // アップデートファイルのプラグインコード.
                        // インストールされているプラグインかを判定.
                        if ($this->isInstalledPlugin($update_plugin_code) === true && $update_plugin_code === $plugin_code) {
                            // インストール処理.
                            $this->arrErr = $this->updatePlugin($plugin_code, $update_plugin_file_name, $plugin_code, $objFormParam->getValue('plugin_id'));
                            if ($this->isError($this->arrErr) === false) {
                                // テンプレート再生成.
                                $this->remakeTemplate();
                                $this->tpl_onload = "alert('プラグインをアップデートしました。');";
                            }
                        } else {
                            $this->arrErr[$plugin_code] = "※ プラグインファイルが不正です。<br/>";
                       }
                    }
                }
                break;
            // 優先度.
            case 'priority':
                // TODO 優先度の変更処理.
//                // 優先度を取得
//                $priority_array = $objFormParam->getValue('priority');
//                
//                // 優先度の更新
//                $objQuery =& SC_Query_Ex::getSingletonInstance();
//                foreach ($priority_array as $key => $value) {
//                    $sqlval['rank'] = $value;
//                    $objQuery->update("dtb_plugin", $sqlval, "plugin_id = ?", array($key));
//                }
//                break;
            default:
                break;
        }

        // DBからプラグイン情報を取得
        $plugins = SC_Helper_Plugin_Ex::getAllPlugin();

        foreach ($plugins as $key => $plugin) {
            // 設定ファイルがあるかを判定.
            $plugins[$key]['config_flg'] = $this->isContainsFile(PLUGIN_UPLOAD_REALDIR . $plugin['plugin_code'], "config.php");
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
     * @param object $objFormParam
     * @return void
     * 
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('mode', 'mode', INT_LEN, '', array('ALPHA_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('plugin_id', 'plugin_id', INT_LEN, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('plugin_code', 'plugin_code', MTEXT_LEN, '', array('ALPHA_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam("優先順位", "priority", INT_LEN, 'n', array("NUM_CHECK", 'MAX_LENGTH_CHECK'));
    }

    /**
     * ファイルパラメーター初期化.
     *
     * @param object $objUpFile SC_UploadFileのインスタンス.
     * @param string $key 登録するキー.
     * @return void
     */
    function lfInitUploadFile(&$objUpFile, $key) {
        $objUpFile->addFile("プラグインファイル", $key, explode(",", PLUGIN_EXTENSION), FILE_SIZE, true, 0, 0, false);
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
        $objErr->doFunc(array('プラグインファイル', $file_key, explode(",", PLUGIN_EXTENSION)), array("FILE_EXT_CHECK"));
        // ファイルサイズチェック
        $objErr->doFunc(array('プラグインファイル', $file_key, FILE_SIZE), array("FILE_SIZE_CHECK"));
        // ファイル名チェック
        $objErr->doFunc(array('プラグインファイル', $file_key), array("FILE_NAME_CHECK"));

        return $objErr->arrErr;
    }

    /**
     * 既にインストールされているプラグインかを判定します.
     *
     * @param string $plugin_code プラグインコード
     * @return boolean インストール済の場合true インストールされていない場合false 
     */
    function isInstalledPlugin($plugin_code) {
        $plugin = SC_Helper_Plugin_Ex::getPluginByPluginCode($plugin_code);
        if (!empty($plugin)) {
            return true;
        }
        return false;
    }

    /**
     * アップロードされた圧縮ファイルが正常であるかを検証します.
     *
     * @param string $file_path チェックするファイルのパス
     * @param string $plugin_code プラグインコード
     * @return array エラー情報を格納した連想配列.
     */
    function checkPluginFile($file_path, $plugin_code, $key_file) {
        $arrErr = array();

        // Archive_Tarを生成します.
        $tar_obj = new Archive_Tar($file_path);

        // 圧縮ファイル名とディレクトリ名が同一であるかを判定します.
        if ($this->checkUploadFileName($tar_obj, $plugin_code) === false) {
            $arrErr[$key_file] = "※ 圧縮ファイル名 or フォルダ名が不正です。圧縮ファイル名とフォルダ名が同一である事を確認して下さい。<br/>";
            return $arrErr;
        }

        // 必須となるクラスファイルが含まれているかを判定します.
        $plugin_main_file = $plugin_code . "/" . $plugin_code . ".php";
        if ($this->checkContainsFile($tar_obj, $plugin_main_file) === false) {
            $arrErr[$key_file] = "※ ファイルに" .  $plugin_code . ".phpが含まれていません。<br/>";
            return $arrErr;
        }
        return $arrErr;
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
        $array_ext = explode(".", $file_name);
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
     * @param string $plugin_file_name プラグインファイル名.
     * @return array エラー情報を格納した連想配列.
     */
    function installPlugin($plugin_code, $plugin_file_name) {

        $arrErr = array();
        // 保存ディレクトリ.
        $plugin_dir = $this->getPluginDir($plugin_code);

        // ファイルをチェックし展開します.
        $arrErr = $this->unpackPluginFile($plugin_file_name, $plugin_dir, $plugin_code, "plugin_file");
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }

        // プラグインファイルを読み込み.
        $plugin_class_file_path = $this->getPluginFilePath($plugin_code);
        $arrErr = $this->requirePluginFile($plugin_class_file_path, "plugin_file");
        if ($this->isError($arrErr) === true) {
            SC_Utils_Ex::deleteFile($plugin_dir);
            return $arrErr;
        }

        // リフレクションオブジェクトを生成.
        $objReflection = new ReflectionClass($plugin_code);

        // プラグインクラスに必須となるパラメータが定義されているかチェック.
        $arrErr = $this->checkPluginConstants($objReflection);
        if ($this->isError($arrErr) === true) {
            SC_Utils_Ex::deleteFile($plugin_dir);
            return $arrErr;
        }

        // プラグイン情報をDB登録
        if ($this->registerData($objReflection) === false) {
            SC_Utils_Ex::deleteFile($plugin_dir);
            $arrErr['plugin_file'] = "※ DB登録に失敗しました。<br/>";
            return $arrErr;
        }

        // プラグインhtmlディレクトリ作成
        $plugin_html_dir = PLUGIN_HTML_REALDIR . $plugin_code;
        $this->makeDir($plugin_html_dir);

        $plugin = SC_Helper_Plugin_Ex::getPluginByPluginCode($plugin_code);
        $arrErr = $this->execPlugin($plugin['plugin_id'], $plugin_code, "install");

        return $arrErr;
    }

    /**
     * プラグインクラス内の定数をチェックします.
     * 
     * @param ReflectionClass $objReflection リフレクションオブジェクト 
     * @return array エラー情報を格納した連想配列.
     */
    function checkPluginConstants(ReflectionClass $objReflection) {
        $arrErr = array();

        if ($objReflection->getConstant("PLUGIN_NAME") === false) {
            $arrErr['plugin_file'] = "※ PLUGIN_NAMEが定義されていません。<br/>";
            return $arrErr;
        }
        if ($objReflection->getConstant("PLUGIN_VERSION") === false) {
            $arrErr['plugin_file'] = "※ PLUGIN_VERSIONが定義されていません。<br/>";
            return $arrErr;
        }
        if ($objReflection->getConstant("COMPLIANT_VERSION") === false) {
            $arrErr['plugin_file'] = "※ COMPLIANT_VERSIONが定義されていません。<br/>";
            return $arrErr;
        }
        if ($objReflection->getConstant("AUTHOR") === false) {
            $arrErr['plugin_file'] = "※ AUTHORが定義されていません。<br/>";
            return $arrErr;
        }
        if ($objReflection->getConstant("DESCRIPTION") === false) {
            $arrErr['plugin_file'] = "※ DESCRIPTIONが定義されていません。<br/>";
            return $arrErr;
        }

        $objErr = new SC_CheckError_Ex($objReflection->getConstants());
        $objErr->doFunc(array('PLUGIN_NAME', 'PLUGIN_NAME', STEXT_LEN), array("MAX_LENGTH_CHECK",));
        $objErr->doFunc(array('PLUGIN_VERSION', 'PLUGIN_VERSION', STEXT_LEN), array("MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('COMPLIANT_VERSION', 'COMPLIANT_VERSION', STEXT_LEN), array("MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('AUTHOR', 'AUTHOR', STEXT_LEN), array("MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('DESCRIPTION', 'DESCRIPTION', SLTEXT_LEN), array("MAX_LENGTH_CHECK"));
        if ($objReflection->getConstant("PLUGIN_SITE_URL") !== false) {
            $objErr->doFunc(array('PLUGIN_SITE_URL', 'PLUGIN_SITE_URL', URL_LEN), array("MAX_LENGTH_CHECK","GRAPH_CHECK"));
        }
        if ($objReflection->getConstant("AUTHOR_SITE_URL") !== false) {
            $objErr->doFunc(array('AUTHOR_SITE_URL', 'AUTHOR_SITE_URL', URL_LEN), array("MAX_LENGTH_CHECK","GRAPH_CHECK"));
        }
        // エラー内容を出力用の配列にセットします.
        if ($this->isError($objErr->arrErr)) {
            $arrErr['plugin_file'] = "";
            foreach ($objErr->arrErr as $error) {
                    $arrErr['plugin_file'] .= $error;
            }
        }
        return $arrErr;
    }

    /**
     * プラグインをアップデートします.
     * 
     * @param string $plugin_code プラグインコード.
     * @param string $plugin_file_name プラグインファイル名.
     * @param string $file_key ファイルキー.
     * @param string $plugin_id プラグインID.
     * @return array エラー情報を格納した連想配列.
     */
    function updatePlugin($plugin_code, $plugin_file_name, $file_key, $plugin_id) {
        // アップロードしたファイルのエラーチェック.
        $arrErr = array();

        // 展開先ディレクトリ.
        $temp_plugin_dir = DOWNLOADS_TEMP_DIR . $plugin_code;

        // ファイルをチェックし展開します.
        $arrErr = $this->unpackPluginFile($plugin_file_name, $temp_plugin_dir, $plugin_code, $plugin_code);
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }

        // 展開されたディレクトリからプラグインクラスファイルを読み込みます.
        $update_plugin_class_path = $temp_plugin_dir . "/" . $plugin_code . ".php";
        $arrErr = $this->requirePluginFile($update_plugin_class_path, $file_key);
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }
        // プラグインクラスファイルのUPDATE処理を実行.
        $arrErr = $this->execPlugin($plugin_id, $plugin_code, "update");

        // 保存ディレクトリの削除.
        SC_Utils_Ex::deleteFile($temp_plugin_dir);

        return $arrErr;
    }

    /**
     * ファイルをアップロードし、解凍先のディレクトリに解凍します.
     * 
     * @param string $unpack_file_name ファイル名
     * @param string $unpack_dir 解凍ディレクトリ
     * @param string $plugin_code プラグインコード.
     * @param string $file_key ファイルキー
     * @return array エラー情報を格納した連想配列.
     */
    function unpackPluginFile($unpack_file_name, $unpack_dir, $plugin_code, $file_key) {
        $arrErr = array();
        // 解凍ディレクトリディレクトリを作成し、一時ディレクトリからファイルを移動
        $objUpFile = new SC_UploadFile_Ex(PLUGIN_TEMP_REALDIR, $unpack_dir);
        $this->lfInitUploadFile($objUpFile, $file_key);
        $arrErr = $objUpFile->makeTempFile($file_key, false);
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }

        // 正常にアップロードされているかをチェック.
        $arrErr = $objUpFile->checkEXISTS($file_key);
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }

        // 圧縮ファイルの中をチェック.
        $plugin_file_path = PLUGIN_TEMP_REALDIR . $unpack_file_name;
        $arrErr = $this->checkPluginFile($plugin_file_path, $plugin_code, $file_key);
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }

        // 展開用ディレクトリを作成し、一時ディレクトリから移動
        $this->makeDir($unpack_dir);
        $objUpFile->moveTempFile();

        // 解凍
        $update_plugin_file_path = $unpack_dir . "/" . $unpack_file_name;
        if (!SC_Helper_FileManager_Ex::unpackFile($update_plugin_file_path)) {
            $arrErr['plugin_file'] = "※ 解凍に失敗しました。<br/>";
            return $arrErr;
        }
        return $arrErr;
    }

    /**
     * プラグインをアンインストールします.
     * 
     * @param int $plugin_id プラグインID.
     * @param string $plugin_code プラグインコード.
     * @return array エラー情報を格納した連想配列.
     */
    function uninstallPlugin($plugin_id, $plugin_code) {
        $arrErr = array();
        // プラグインファイルを読み込みます.
        $plugin_class_path = $this->getPluginFilePath($plugin_code);
        $arrErr = $this->requirePluginFile($plugin_class_path, 'plugin_error');
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }

        // modeで指定されたメソッドを実行.
        $arrErr = $this->execPlugin($plugin_id, $plugin_code, "uninstall");
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }
        // プラグインの削除処理.
        $arrErr = $this->deletePlugin($plugin_id, $plugin_code);

        return $arrErr;
    }

    /**
     * プラグインを有効にします.
     * 
     * @param int $plugin_id プラグインID.
     * @param string $plugin_code プラグインコード.
     * @return array $arrErr エラー情報を格納した連想配列.
     */
    function enablePlugin($plugin_id, $plugin_code) {
        $arrErr = array();
        // クラスファイルを読み込み.
        $plugin_class_path = $this->getPluginFilePath($plugin_code);
        $arrErr = $this->requirePluginFile($plugin_class_path, 'plugin_error');
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }
        // 無効化処理を実行します.
        $arrErr = $this->execPlugin($plugin_id, $plugin_code, "enable");
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }
        // プラグインを有効にします.
        $this->updatePluginEnable($plugin_id, PLUGIN_ENABLE_TRUE);

        return $arrErr;
    }

    /**
     * プラグインを無効にします.
     * 
     * @param int $plugin_id プラグインID.
     * @param string $plugin_code プラグインコード.
     * @return array エラー情報を格納した連想配列.
     */
    function disablePlugin($plugin_id, $plugin_code) {
        $arrErr = array();
        // クラスファイルを読み込み.
        $plugin_class_path = $this->getPluginFilePath($plugin_code);
        $arrErr = $this->requirePluginFile($plugin_class_path, 'plugin_error');
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }

        // 無効化処理を実行します.
        $arrErr = $this->execPlugin($plugin_id, $plugin_code, "disable");
        if ($this->isError($arrErr) === true) {
            return $arrErr;
        }
        // プラグインを無効にします.
        $this->updatePluginEnable($plugin_id, PLUGIN_ENABLE_FALSE);

        return $arrErr;
    }

    /**
     * プラグイン情報をDB登録.
     *
     * @param ReflectionClass $objReflection リフレクションオブジェクト 
     * @return array エラー情報を格納した連想配列.
     */
    function registerData(ReflectionClass $objReflection) {

        // プラグイン情報をDB登録.
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        $arr_sqlval_plugin = array();
        $plugin_id = $objQuery->nextVal('dtb_plugin_plugin_id');
        $arr_sqlval_plugin['plugin_id'] = $plugin_id;
        $arr_sqlval_plugin['plugin_name'] = $objReflection->getConstant("PLUGIN_NAME");
        $arr_sqlval_plugin['plugin_code'] = $objReflection->getName();
        $arr_sqlval_plugin['author'] = $objReflection->getConstant("AUTHOR");
        // AUTHOR_SITE_URLが定義されているか判定.
        $author_site_url = $objReflection->getConstant("AUTHOR_SITE_URL");
        if($author_site_url !== false) $arr_sqlval_plugin['author_site_url'] = $author_site_url;
        // PLUGIN_SITE_URLが定義されているか判定.
        $plugin_site_url = $objReflection->getConstant("PLUGIN_SITE_URL");
        if($plugin_site_url !== false) $arr_sqlval_plugin['plugin_site_url'] = $plugin_site_url;
        $arr_sqlval_plugin['plugin_version'] = $objReflection->getConstant("PLUGIN_VERSION");
        $arr_sqlval_plugin['compliant_version'] = $objReflection->getConstant("COMPLIANT_VERSION");
        $arr_sqlval_plugin['plugin_description'] = $objReflection->getConstant("DESCRIPTION");
        $arr_sqlval_plugin['rank'] = 1 + $objQuery->max('rank', 'dtb_plugin');
        $arr_sqlval_plugin['enable'] = PLUGIN_ENABLE_FALSE;
        $arr_sqlval_plugin['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery->insert('dtb_plugin', $arr_sqlval_plugin);

        // フックポイントをDB登録.
        $hook_point = $objReflection->getConstant("HOOK_POINTS");
        if ($hook_point !== false) {
            $array_hook_point = explode(",", $hook_point);
            if (is_array($array_hook_point)) {
                foreach ($array_hook_point as $hook_point) {
                    $arr_sqlval_plugin_hookpoint = array();
                    $id = $objQuery->nextVal('dtb_plugin_hookpoint_id');
                    $arr_sqlval_plugin_hookpoint['id'] = $id;
                    $arr_sqlval_plugin_hookpoint['plugin_id'] = $plugin_id;
                    $arr_sqlval_plugin_hookpoint['hook_point'] = $hook_point;
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
     * @return array エラー情報を格納した連想配列.
     */
    function requirePluginFile($file_path, $key) {
        $arrErr = array();
        if (file_exists($file_path)) {
            require_once $file_path;
        } else {
            $arrErr[$key] = "※ " . $file_path ."の読み込みに失敗しました。<br/>";
        }
        return $arrErr;
    }

    /**
     * インスタンスを生成し、指定のメソッドを実行する.
     *
     * @param integer $plugin_id プラグインID
     * @param string $plugin_code プラグインコード
     * @param string $exec_func 実行するメソッド名.
     * @return array $arrErr エラー情報を格納した連想配列.
     *
     */
    function execPlugin($plugin_id, $plugin_code, $exec_func) {
        $arrErr = array();
            // インスタンスの生成.
            $objPlugin = new $plugin_code();
            if (method_exists($objPlugin, $exec_func) === true) {
                $arrErr = $objPlugin->$exec_func($plugin_id);
            } else {
                $arrErr['plugin_error'] = "※ " . $plugin_code . ".php に" . $exec_func . "が見つかりません。<br/>";
            }

        return $arrErr;
    }

    /**
     * 管理者側 テンプレート再生成
     *
     * @return void
     */
    function remakeTemplate() {
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance();
        $objPlugin->remakeAllTemplates();
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
        $where = "plugin_id = ?";
        // UPDATEの実行
        $ret = $objQuery->update("dtb_plugin", $sqlval, $where, array($plugin_id));
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
        $where = "plugin_id = ?";
        $objQuery->delete("dtb_plugin", $where, array($plugin_id));
        $objQuery->delete("dtb_plugin_hookpoint", $where, array($plugin_id));

        if ($objQuery->commit()) {
            if (SC_Utils_Ex::deleteFile($this->getPluginDir($plugin_code)) === false) {
                // TODO エラー処理
            } 

            if (SC_Utils_Ex::deleteFile($this->getHtmlPluginDir($plugin_code)) === false) {
                // TODO エラー処理
            }       
        }
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
            if($value["filename"] === $file_path) return true;
        }
        return false;
    }

    /**
     * 圧縮ファイル名と中のディレクトリ名が同じであるかをチェックします.
     *
     * @param Archive_Tar $tar_obj Archive_Tarクラスのオブジェクト
     * @param string $dir_name ディレクトリ名.
     * @return boolean
     */
    function checkUploadFileName($tar_obj, $dir_name) {
        // ファイル一覧を取得
        $arrayFile = $tar_obj->listContent();
        // ディレクトリ名と圧縮ファイル名が同じかをチェック.
        $pattern = ("|^". preg_quote($dir_name) ."\/(.*?)|");
        foreach ($arrayFile as $value) {
            if(preg_match($pattern, $value["filename"])) return true;
        }
        return false;;
    }

    /**
     * ディレクトリを作成します.
     *
     * @param string $dir_path 作成するディレクトリのパス
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
        $objQuery =& SC_Query_Ex::getSingletonInstance(); 
        $table = "dtb_plugin_hookpoint";
        $where = "plugin_id = ?";
        $conflictHookPoints = $objQuery->select("*", $table, $where, array($plugin_id));

        $conflict_alert_message = "";
        foreach ($conflictHookPoints as $conflictHookPoint) {
            // 登録商品のチェック
            $table = "dtb_plugin_hookpoint AS T1 LEFT JOIN dtb_plugin AS T2 ON T1.plugin_id = T2.plugin_id";
            $where = "T1.hook_point = ? AND NOT T1.plugin_id = ? AND T2.enable = " . PLUGIN_ENABLE_TRUE . " GROUP BY T1.plugin_id";
            $conflictPlugins = $objQuery->select("T1.plugin_id, T2.plugin_name", $table, $where, array($conflictHookPoint['hook_point'], $conflictHookPoint['plugin_id']));

            foreach ($conflictPlugins as $conflictPlugin) {
                $conflict_alert_message =+ "* " .  $conflictPlugin['plugin_name'] . "と競合する可能性があります。<br/>";
            }
        }
        return $conflict_alert_message;
    }

    /**
     * エラー情報が格納されているか判定します.
     *
     * @param array $arrErr エラー情報を格納した連想配列.
     * @return boolean.
     */
    function isError($arrErr) {
        if (is_array($arrErr) && count($arrErr) > 0) {
            return true;
        }
        return false;
    }
}
