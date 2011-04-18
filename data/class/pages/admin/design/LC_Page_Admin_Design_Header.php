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
 * ヘッダ, フッタ編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_Header extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'design/header.tpl';
        $this->tpl_subnavi  = 'design/subnavi.tpl';
        $this->header_row = 13;
        $this->footer_row = 13;
        $this->tpl_subno = 'header';
        $this->tpl_mainno = 'design';
        $this->tpl_subtitle = 'ヘッダー/フッター設定';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrDeviceType = $masterData->getMasterData('mtb_device_type');
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
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();
        $this->arrErr = $objFormParam->checkError();
        $is_error = (!SC_Utils_Ex::isBlank($this->arrErr));

        $this->device_type_id = $objFormParam->getValue('device_type_id', DEVICE_TYPE_PC);

        switch ($this->getMode()) {
        // 登録
        case 'regist':
            if ($this->doRegister($objFormParam)) {
                $this->tpl_onload = "alert('登録が完了しました。');";
            }
            break;

        default:
            break;
        }

        if (!$is_error) {
            // テキストエリアに表示
            $header_path = $this->getTemplatePath($this->device_type_id, 'header');
            $footer_path = $this->getTemplatePath($this->device_type_id, 'footer');
            if ($header_path === false || $footer_path === false) {
                $this->arrErr['err'] = '※ ファイルの取得に失敗しました<br />';
            } else {
                $this->header_data = file_get_contents($header_path);
                $this->footer_data = file_get_contents($footer_path);
            }
        } else {
            // 画面にエラー表示しないため, ログ出力
            GC_Utils_Ex::gfPrintLog('Error: ' . print_r($this->arrErr, true));
        }

        //サブタイトルの追加
        $this->tpl_subtitle .= ' - ' . $this->arrDeviceType[$this->device_type_id];
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
     * パラメータ情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam("端末種別ID", "device_type_id", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("division", "division", STEXT_LEN, 'a', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("ヘッダデータ", "header");
        $objFormParam->addParam("フッタデータ", "footer");
    }

    /**
     * エラーチェックを行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラーメッセージの配列
     */
    function lfCheckError(&$objFormParam, &$arrErr, &$objLayout) {
        $arrParams = $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrParams);
        $objErr->arrErr =& $arrErr;
        $objErr->doFunc(array("division", "division", STEXT_LEN), array("EXIST_CHECK"));
        return $objErr->arrErr;
    }

    /**
     * 登録を実行する.
     *
     * ファイルの作成に失敗した場合は, エラーメッセージを出力する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return integer|boolean 登録が成功した場合 true; 失敗した場合 false
     */
    function doRegister(&$objFormParam) {
        $division = $objFormParam->getValue('division');
        $contents = $objFormParam->getValue($division);
        $tpl_path = $this->getTemplatePath($objFormParam->getValue('device_type_id'), $division);
        if ($tpl_path === false
            || !SC_Helper_FileManager_Ex::sfWriteFile($tpl_path, $contents)) {
            $this->arrErr['err'] = '※ ファイルの書き込みに失敗しました<br />';
            return false;
        }
        return true;
    }

    /**
     * テンプレートパスを取得する.
     *
     * @param integer $device_type_id 端末種別ID
     * @param string $division "header" or "footer"
     * @return string|boolean 成功した場合, テンプレートのパス; 失敗した場合 false
     */
    function getTemplatePath($device_type_id, $division) {
        $tpl_path = SC_Helper_PageLayout_Ex::getTemplatePath($device_type_id) . '/' . $division . '.tpl';
        if (file_exists($tpl_path)) {
            return $tpl_path;
        } else {
            return false;
        }
    }
}
?>
