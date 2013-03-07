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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * CSS設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_CSS extends LC_Page_Admin_Ex 
{

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
        parent::init();
        $this->tpl_mainpage = 'design/css.tpl';
        $this->area_row = 30;
        $this->tpl_subno = 'css';
        $this->tpl_mainno = 'design';
        $this->tpl_maintitle = 'デザイン管理';
        $this->tpl_subtitle = 'CSS設定';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrDeviceType = $masterData->getMasterData('mtb_device_type');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action()
    {

        $objLayout = new SC_Helper_PageLayout_Ex();

        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();
        $this->arrErr = $objFormParam->checkError();
        $is_error = (!SC_Utils_Ex::isBlank($this->arrErr));

        // CSSファイル名を取得
        $this->css_name = $objFormParam->getValue('css_name');
        $this->old_css_name = $objFormParam->getValue('old_css_name', $this->css_name);
        $this->device_type_id = $objFormParam->getValue('device_type_id', DEVICE_TYPE_PC);

        $css_dir = $objLayout->getTemplatePath($this->device_type_id, true) . 'css/';
        $css_path = $css_dir . $this->css_name . '.css';

        switch ($this->getMode()) {
            // データ更新処理
            case 'confirm':
                if (!$is_error) {
                    $this->arrErr = $this->lfCheckError($objFormParam, $this->arrErr);
                    if (SC_Utils_Ex::isBlank($this->arrErr)) {
                        if ($this->doRegister($css_dir, $this->css_name, $this->old_css_name, $css_path,
                                              $objFormParam->getValue('css_data'))) {
                            $this->tpl_onload = "alert('登録が完了しました。');";
                        }
                    }
                }
                break;
            case 'delete':
                if (!$is_error) {
                    if ($this->doDelete($css_path)) {
                        $arrPram = array(
                            'device_type_id' => $this->device_type_id,
                            'msg' => 'on',
                        );

                        SC_Response_Ex::reload($arrPram, true);
                    }
                }
                break;
            default:
                if (isset($_GET['msg']) && $_GET['msg'] == 'on') {
                    // 完了メッセージ
                    $this->tpl_onload = "alert('登録が完了しました。');";
                }
                break;
        }

        if (!$is_error && $this->checkPath($this->css_name)) {
            // CSSファイルの読み込み
            if (!SC_Utils_Ex::isBlank($this->css_name)) {
                $objFormParam->setValue('css_data', file_get_contents($css_path));
            }
            // ファイルリストを取得
            $this->arrCSSList = $this->getCSSList($css_dir);
        } else {
            // 画面にエラー表示しないため, ログ出力
            GC_Utils_Ex::gfPrintLog('Error: ' . print_r($this->arrErr, true));
        }
        $this->tpl_subtitle = $this->arrDeviceType[$this->device_type_id] . '＞' . $this->tpl_subtitle;
        $this->arrForm = $objFormParam->getFormParamList();

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy()
    {
        parent::destroy();
    }

    /**
     * パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('端末種別ID', 'device_type_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('CSSファイル名', 'css_name', STEXT_LEN, 'a', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('旧CSSファイル名', 'old_css_name', STEXT_LEN, 'a', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('CSSデータ', 'css_data');

    }

    /**
     * 登録を実行する.
     *
     * ファイルの作成に失敗した場合は, エラーメッセージを出力する.
     *
     * @param string $css_dir CSS ディレクトリ
     * @param string $css_name CSSファイル名
     * @param string $old_css_name 旧CSSファイル名
     * @param string $css_path CSSファイルの絶対パス
     * @param string $css_data 書き込みを行うデータ
     * @return boolean 登録が成功した場合 true; 失敗した場合 false
     */
    function doRegister($css_dir, $css_name, $old_css_name, $css_path, $css_data)
    {
        $objFileManager = new SC_Helper_FileManager_Ex();

        if (!SC_Utils_Ex::isBlank($old_css_name)
            && $old_css_name != $css_name) {
            if (!unlink($css_dir . $old_css_name . '.css')) {
                $this->arrErr['err'] = '※ 旧ファイルの削除に失敗しました<br />';
                return false;
            }
        }

        if (!SC_Helper_FileManager_Ex::sfWriteFile($css_path, $css_data)) {
            $this->arrErr['err'] = '※ CSSの書き込みに失敗しました<br />';
            return false;
        }
        return true;
    }

    /**
     * 削除を実行する.
     *
     * @param string $css_path CSSファイルの絶対パス
     * @return boolean 削除が成功した場合 true; 失敗した場合 false
     */
    function doDelete($css_path)
    {
        if (!unlink($css_path)) {
            $this->arrErr['err'] = '※ CSSの削除に失敗しました<br />';
            return false;
        }
        return true;
    }

    /**
     * CSSファイルのリストを取得.
     *
     * @param array $css_dir CSSディレクトリ
     * @return array ファイルリスト
     */
    function getCSSList($css_dir)
    {
        $objFileManager = new SC_Helper_FileManager_Ex();

        $arrFileList = $objFileManager->sfGetFileList($css_dir);
        foreach ($arrFileList as $val) {
            if (!$val['is_dir']) {
                $arrCSSList[] = array(
                    'file_name' => $val['file_name'],
                    'css_name'  => preg_replace('/(.+)\.(.+?)$/','$1',$val['file_name']),
                );
            }
        }
        return $arrCSSList;
    }

    /**
     * エラーチェックを行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラーメッセージの配列
     */
    function lfCheckError(&$objFormParam, &$arrErr)
    {
        $arrParams = $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrParams);
        $objErr->arrErr =& $arrErr;
        $objErr->doFunc(array('CSSファイル名', 'css_name', STEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK','FILE_NAME_CHECK_BY_NOUPLOAD'));

        $device_type_id = $objFormParam->getValue('device_type_id');
        $css_name = $objFormParam->getValue('css_name');
        $old_css_name = $objFormParam->getValue('old_css_name', $css_name);

        $is_error = false;
        // 重複チェック
        if (!SC_Utils_Ex::isBlank(($objErr->arrErr['css_name']))) {
            $arrCSSList = $this->getCSSList($this->getCSSDir());
            foreach ($arrCSSList as $val) {
                if ($val['css_name'] == $css_name) {
                    if (SC_Utils_Ex::isBlank($old_css_name)
                        || $old_css_name != $css_name) {
                        $is_error = true;
                    }
                }
            }
            if ($is_error) {
                $objErr->arrErr['css_name'] = '※ 同じファイル名のデータが存在しています。別の名称を付けてください。<br />';
            }
        }
        return $objErr->arrErr;
    }

    /**
     * CSSディレクトリを取得する.
     *
     * @param integer $device_type_id 端末種別ID
     * @return string CSSディレクトリ
     */
    function getCSSDir($device_type_id)
    {
        return SC_Helper_PageLayout_Ex::getTemplatePath($device_type_id, true) . 'css/';
    }

    /**
     * 文字列に[./]表記がないかをチェックします
     * @param string $str
     * @return boolean 
     */
    function checkPath($str)
    {
        // 含む場合はfalse
        if (preg_match('|\./|', $str)) {
            return false;
        }
        return true;
    }
}
