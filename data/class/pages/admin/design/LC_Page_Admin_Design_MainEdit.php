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
 * メイン編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_MainEdit extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'design/main_edit.tpl';
        $this->text_row     = 13;
        $this->tpl_subno = 'main_edit';
        $this->tpl_mainno = 'design';
        $this->tpl_maintitle = t('TPL_MAINTITLE_003');
        $this->tpl_subtitle = t('LC_Page_Admin_Design_MainEdit_002');
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

        $objLayout = new SC_Helper_PageLayout_Ex();
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();
        $this->arrErr = $objFormParam->checkError();
        $is_error = (!SC_Utils_Ex::isBlank($this->arrErr));

        $this->device_type_id = $objFormParam->getValue('device_type_id', DEVICE_TYPE_PC);
        $this->page_id = $objFormParam->getValue('page_id');

        switch ($this->getMode()) {
            // 削除
            case 'delete':
                if (!$is_error) {
                    if ($objLayout->isEditablePage($this->device_type_id, $this->page_id)) {
                        $objLayout->lfDelPageData($this->page_id, $this->device_type_id);

                        SC_Response_Ex::reload(array('device_type_id' => $this->device_type_id,
                                                     'msg' => 'on'), true);
                        SC_Response_Ex::actionExit();
                    }
                }
                break;

            // 登録/編集
            case 'confirm':
                if (!$is_error) {
                    $this->arrErr = $this->lfCheckError($objFormParam, $this->arrErr);
                    if (SC_Utils_Ex::isBlank($this->arrErr)) {
                        $result = $this->doRegister($objFormParam, $objLayout);
                        if ($result !== false) {

                            SC_Response_Ex::reload(array('device_type_id' => $this->device_type_id,
                                                         'page_id' => $result,
                                                         'msg' => 'on'), true);
                        SC_Response_Ex::actionExit();
                        }
                    }
                }
                break;

            default:
                if (isset($_GET['msg']) && $_GET['msg'] == 'on') {
                    $this->tpl_onload = "alert('" . t('ALERT_004') . "');";
                }
                break;
        }

        if (!$is_error) {
            $this->arrPageList = $objLayout->getPageProperties($this->device_type_id, null);
            // page_id が指定されている場合にはテンプレートデータの取得
            if (!SC_Utils_Ex::isBlank($this->page_id)) {
                $arrPageData = $this->getTplMainpage($this->device_type_id, $this->page_id, $objLayout);
                $objFormParam->setParam($arrPageData);
            }
        } else {
            // 画面にエラー表示しないため, ログ出力
            GC_Utils_Ex::gfPrintLog('Error: ' . print_r($this->arrErr, true));
        }
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
     * パラメーター情報の初期化
     *
     * XXX URL のフィールドは, 実際は filename なので注意
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam(t('c_Page ID_01'), 'page_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Terminal type ID_01'), 'device_type_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_THE_NAME'), 'page_name', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_URL_01'), 'filename', STEXT_LEN, 'a', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_HEADER_CHK'), 'header_chk', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_FOOTER_CHK'), 'footer_chk', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_EDIT_FLG'), 'edit_flg', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_TPL_DATA'), 'tpl_data');
    }

    /**
     * ページデータを取得する.
     *
     * @param integer $device_type_id 端末種別ID
     * @param integer $page_id ページID
     * @param SC_Helper_PageLayout $objLayout SC_Helper_PageLayout インスタンス
     * @return array ページデータの配列
     */
    function getTplMainpage($device_type_id, $page_id, &$objLayout) {
        $arrPageData = $objLayout->getPageProperties($device_type_id, $page_id);

        $templatePath = $objLayout->getTemplatePath($device_type_id);
        $filename = $templatePath . $arrPageData[0]['filename'] . '.tpl';
        if (file_exists($filename)) {
            $arrPageData[0]['tpl_data'] = file_get_contents($filename);
        }
        // ファイル名を画面表示用に加工しておく
        $arrPageData[0]['filename'] = preg_replace('|^' . preg_quote(USER_DIR) . '|', '', $arrPageData[0]['filename']);
        return $arrPageData[0];
    }

    /**
     * 登録を実行する.
     *
     * ファイルの作成に失敗した場合は, エラーメッセージを出力し,
     * データベースをロールバックする.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param SC_Helper_PageLayout $objLayout SC_Helper_PageLayout インスタンス
     * @return integer|boolean 登録が成功した場合, 登録したページID;
     *                         失敗した場合 false
     */
    function doRegister(&$objFormParam, &$objLayout) {
        $filename = $objFormParam->getValue('filename');
        $arrParams['device_type_id'] = $objFormParam->getValue('device_type_id');
        $arrParams['page_id'] = $objFormParam->getValue('page_id');
        $arrParams['header_chk'] = intval($objFormParam->getValue('header_chk')) === 1 ? 1 : 2;
        $arrParams['footer_chk'] = intval($objFormParam->getValue('footer_chk')) === 1 ? 1 : 2;
        $arrParams['tpl_data'] = $objFormParam->getValue('tpl_data');
        $arrParams['page_name'] = $objFormParam->getValue('page_name');
        $arrParams['url'] = USER_DIR . $filename . '.php';
        $arrParams['filename'] = USER_DIR . $filename;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        $page_id = $this->registerPage($arrParams, $objLayout);

        /*
         * 新規登録時
         * or 編集可能な既存ページ編集時かつ, PHP ファイルが存在しない場合に,
         * PHP ファイルを作成する.
         */
        if (SC_Utils_Ex::isBlank($arrParams['page_id'])
            || $objLayout->isEditablePage($arrParams['device_type_id'], $arrParams['page_id'])) {
            if (!$this->createPHPFile($filename)) {
                $this->arrErr['err'] = t('LC_Page_Admin_Design_MainEdit_003');
                $objQuery->rollback();
                return false;
            }
            // 新規登録時のみ $page_id を代入
            $arrParams['page_id'] = $page_id;
        }

        if ($objLayout->isEditablePage($arrParams['device_type_id'], $page_id)) {
            $tpl_path = $objLayout->getTemplatePath($arrParams['device_type_id']) . $arrParams['filename'] . '.tpl';
        } else {
            $tpl_path = $objLayout->getTemplatePath($arrParams['device_type_id']) . $filename . '.tpl';
        }

        if (!SC_Helper_FileManager_Ex::sfWriteFile($tpl_path, $arrParams['tpl_data'])) {
            $this->arrErr['err'] = t('LC_Page_Admin_Design_MainEdit_004');
            $objQuery->rollback();
            return false;
        }

        $objQuery->commit();
        return $arrParams['page_id'];
    }

    /**
     * 入力内容をデータベースに登録する.
     *
     * @param array $arrParams フォームパラメーターの配列
     * @param SC_Helper_PageLayout $objLayout SC_Helper_PageLayout インスタンス
     * @return integer ページID
     */
    function registerPage($arrParams, &$objLayout) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // ページIDが空の場合は新規登録
        $is_new = SC_Utils_Ex::isBlank($arrParams['page_id']);
        // 既存ページの存在チェック
        if (!$is_new) {
            $arrExists = $objLayout->getPageProperties($arrParams['device_type_id'], $arrParams['page_id']);
        }

        $table = 'dtb_pagelayout';
        $arrValues = $objQuery->extractOnlyColsOf($table, $arrParams);
        $arrValues['update_url'] = $_SERVER['HTTP_REFERER'];
        $arrValues['update_date'] = 'CURRENT_TIMESTAMP';

        // 新規登録
        if ($is_new || SC_Utils_Ex::isBlank($arrExists)) {
            $objQuery->setOrder('');
            $arrValues['page_id'] = 1 + $objQuery->max('page_id', $table, 'device_type_id = ?',
                                                       array($arrValues['device_type_id']));
            $arrValues['create_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->insert($table, $arrValues);
        }
        // 更新
        else {
            // 編集不可ページは更新しない
            if (!$objLayout->isEditablePage($arrValues['device_type_id'], $arrValues['page_id'])) {
                unset($arrValues['page_name']);
                unset($arrValues['filename']);
                unset($arrValues['url']);
            }

            $objQuery->update($table, $arrValues, 'page_id = ? AND device_type_id = ?',
                              array($arrValues['page_id'], $arrValues['device_type_id']));
        }
        return $arrValues['page_id'];
    }

    /**
     * エラーチェックを行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラーメッセージの配列
     */
    function lfCheckError(&$objFormParam, &$arrErr) {
        $arrParams = $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrParams);
        $objErr->arrErr =& $arrErr;
        $objErr->doFunc(array(t('PARAM_LABEL_THE_NAME'), 'page_name', STEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objErr->doFunc(array(t('c_URL_01'), 'filename', STEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));

        /*
         * URL チェック
         * ここでチェックするのは, パスのみなので SC_CheckError::URL_CHECK()
         * は使用しない
         */
        $valid_url = true;
        foreach (explode('/', $arrParams['filename']) as $val) {
            if (!preg_match('/^[a-zA-Z0-9:_~\.\-]+$/', $val)) {
                $valid_url = false;
            }
            if ($val == '.' || $val == '..') {
                $valid_url = false;
            }
        }
        if (!$valid_url) {
            $objErr->arrErr['filename'] = t('LC_Page_Admin_Design_MainEdit_005');
        }
        // 同一URLの存在チェック
        $where = 'page_id <> 0 AND device_type_id = ? AND filename = ?';
        $arrValues = array($arrParams['device_type_id'], USER_DIR . $arrParams['filename']);
        // 変更の場合は自 URL を除外
        if (!SC_Utils_Ex::isBlank($arrParams['page_id'])) {
            $where .= ' AND page_id <> ?';
            $arrValues[] = $arrParams['page_id'];
        }

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $exists = $objQuery->exists('dtb_pagelayout', $where, $arrValues);
        if ($exists) {
            $objErr->arrErr['filename'] = t('LC_Page_Admin_Design_MainEdit_006');
        }
        return $objErr->arrErr;
    }

    /**
     * PHP ファイルを生成する.
     *
     * 既に同名の PHP ファイルが存在する場合は何もせず true を返す.(#831)
     *
     * @param string $filename フォームパラメーターの filename
     * @return boolean 作成に成功した場合 true
     */
    function createPHPFile($filename) {
        $path = USER_REALDIR . $filename . '.php';

        if (file_exists($path)) {
            return true;
        }

        if (file_exists(USER_DEF_PHP_REALFILE)) {
            $php_contents = file_get_contents(USER_DEF_PHP_REALFILE);
        } else {
            return false;
        }

        // require.php の PATH を書き換える
        $defaultStrings = "exit; // Don't rewrite. This line is rewritten by EC-CUBE.";
        $replaceStrings = "require_once '" . str_repeat('../', substr_count($filename, '/')) . "../require.php';";
        $php_contents = str_replace($defaultStrings, $replaceStrings, $php_contents);

        return SC_Helper_FileManager_Ex::sfWriteFile($path, $php_contents);
    }
}
