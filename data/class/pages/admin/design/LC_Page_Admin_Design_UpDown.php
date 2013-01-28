<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
 * テンプレートアップロード のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_UpDown extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'design/up_down.tpl';
        $this->tpl_subno    = 'up_down';
        $this->tpl_mainno   = 'design';
        $this->tpl_maintitle = t('TPL_MAINTITLE_003');
        $this->tpl_subtitle = t('LC_Page_Admin_Design_UpDown_002');
        $this->arrErr  = array();
        $this->arrForm = array();
        ini_set('max_execution_time', 300);
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
     * FIXME ロジックを見直し
     *
     * @return void
     */
    function action() {

        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();

        $this->device_type_id = $objFormParam->getValue('device_type_id', DEVICE_TYPE_PC);

        switch ($this->getMode()) {
            // アップロードボタン押下時の処理
            case 'upload':
                $objUpFile = $this->lfInitUploadFile($objFormParam);
                $this->arrErr = $this->lfCheckError($objFormParam, $objUpFile);
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    if ($this->doUpload($objFormParam, $objUpFile)) {
                        $this->tpl_onload = "alert('" . t('ALERT_012') . "');";
                        $objFormParam->setValue('template_name', '');
                        $objFormParam->setValue('template_code', '');
                    }
                }
                break;

            default:
                break;
        }
        //サブタイトルの追加
        $this->tpl_subtitle = $this->arrDeviceType[$this->device_type_id] . ' > ' . $this->tpl_subtitle;
        $this->arrForm = $objFormParam->getFormParamList();

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
     * SC_UploadFileクラスの初期化.
     *
     * @param object $objForm SC_FormParamのインスタンス
     * @return object SC_UploadFileのインスタンス
     */
    function lfInitUploadFile($objForm) {
        $pkg_dir = SMARTY_TEMPLATES_REALDIR . $objForm->getValue('template_code');
        $objUpFile = new SC_UploadFile_Ex(TEMPLATE_TEMP_REALDIR, $pkg_dir);
        $objUpFile->addFile(t('PARAM_LABEL_TEMPLATE_FILE'), 'template_file', array(), TEMPLATE_SIZE, true, 0, 0, false);
        return $objUpFile;
    }

    /**
     * SC_FormParamクラスの初期化.
     *
     * @param SC_FormParam $objFormParam SC_FormParamのインスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam(t('PARAM_LABEL_TEMPLATE_CODE'), 'template_code', STEXT_LEN, 'a', array('EXIST_CHECK', 'SPTAB_CHECK','MAX_LENGTH_CHECK', 'ALNUM_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_TEMPLATE_NAME'), 'template_name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_DEVICE_TYPE_ID'), 'device_type_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * uploadモードのパラメーター検証を行う.
     *
     * @param object $objFormParam SC_FormParamのインスタンス
     * @param object $objUpFile SC_UploadFileのインスタンス
     * @return array エラー情報を格納した連想配列, エラーが無ければ(多分)nullを返す
     */
    function lfCheckError(&$objFormParam, &$objUpFile) {
        $arrErr = $objFormParam->checkError();
        $template_code = $objFormParam->getValue('template_code');

        // 同名のフォルダが存在する場合はエラー
        if (file_exists(USER_TEMPLATE_REALDIR . $template_code)) {
            $arrErr['template_code'] = t('LC_Page_Admin_Design_UpDown_003');
        }

        // 登録不可の文字列チェック
        $arrIgnoreCode = array('admin',
                               MOBILE_DEFAULT_TEMPLATE_NAME,
                               SMARTPHONE_DEFAULT_TEMPLATE_NAME,
                               DEFAULT_TEMPLATE_NAME);
        if (in_array($template_code, $arrIgnoreCode)) {
            $arrErr['template_code'] = t('LC_Page_Admin_Design_UpDown_004');
        }

        // DBにすでに登録されていないかチェック
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $exists = $objQuery->exists('dtb_templates', 'template_code = ?', array($template_code));
        if ($exists) {
            $arrErr['template_code'] = t('LC_Page_Admin_Design_UpDown_005');
        }

        /*
         * ファイル形式チェック
         * ファイルが壊れていることも考慮して, 展開可能かチェックする.
         */
        $tar = new Archive_Tar($_FILES['template_file']['tmp_name'], true);
        $arrArchive = $tar->listContent();
        if (!is_array($arrArchive)) {
            $arrErr['template_file'] = t('LC_Page_Admin_Design_UpDown_006');
        } else {
            $make_temp_error = $objUpFile->makeTempFile('template_file', false);
            if (!SC_Utils_Ex::isBlank($make_temp_error)) {
                $arrErr['template_file'] = $make_temp_error;
            }
        }
        return $arrErr;
    }

    /**
     * DBおよびファイルシステムにテンプレートパッケージを追加する.
     *
     * エラーが発生した場合は, エラーを出力し, false を返す.
     *
     * @param object $objFormParam SC_FormParamのインスタンス
     * @param object $objUpFile SC_UploadFileのインスタンス
     * @return boolean 成功した場合 true; 失敗した場合 false
     */
    function doUpload($objFormParam, $objUpFile) {
        $template_code = $objFormParam->getValue('template_code');
        $template_name = $objFormParam->getValue('template_name');
        $device_type_id = $objFormParam->getValue('device_type_id');

        $template_dir = SMARTY_TEMPLATES_REALDIR . $template_code;
        $compile_dir  = DATA_REALDIR . 'Smarty/templates_c/' . $template_code;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        $arrValues = array(
            'template_code' => $template_code,
            'device_type_id' => $device_type_id,
            'template_name' => $template_name,
            'create_date' => 'CURRENT_TIMESTAMP',
            'update_date' => 'CURRENT_TIMESTAMP',
        );
        $objQuery->insert('dtb_templates', $arrValues);

        $is_error = false;
        // フォルダ作成
        if (!file_exists($template_dir)) {
            if (!mkdir($template_dir)) {
                $this->arrErr['err'] = t('LC_Page_Admin_Design_UpDown_007');
                $objQuery->rollback();
                return false;
            }
        }
        if (!file_exists($compile_dir)) {
            if (!mkdir($compile_dir)) {
                $this->arrErr['err'] = t('LC_Page_Admin_Design_UpDown_008');
                $objQuery->rollback();
                return false;
            }
        }

        // 一時フォルダから保存ディレクトリへ移動
        $objUpFile->moveTempFile();

        // 解凍
        if (!SC_Helper_FileManager_Ex::unpackFile($template_dir . '/' . $_FILES['template_file']['name'])) {
            $this->arrErr['err'] = t('LC_Page_Admin_Design_UpDown_009');
            $objQuery->rollback();
            return false;
        }
        // ユーザデータの下のファイルをコピーする
        $from_dir = SMARTY_TEMPLATES_REALDIR . $template_code . '/_packages/';
        $to_dir = USER_REALDIR . 'packages/' . $template_code . '/';
        if (!SC_Utils_Ex::recursiveMkdir($to_dir)) {
            $this->arrErr['err'] = t('LC_Page_Admin_Design_UpDown_010', array('T_ARG1' => $to_dir));
            
            $objQuery->rollback();
            return false;
        }
        SC_Utils_Ex::sfCopyDir($from_dir, $to_dir);

        $objQuery->commit();
        return true;
    }
}
