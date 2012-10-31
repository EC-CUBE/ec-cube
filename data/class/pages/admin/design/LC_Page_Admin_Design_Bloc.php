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
 * ブロック編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_Bloc extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'design/bloc.tpl';
        $this->tpl_subno_edit = 'bloc';
        $this->text_row = 13;
        $this->tpl_subno = 'bloc';
        $this->tpl_mainno = 'design';
        $this->tpl_maintitle = 'デザイン管理';
        $this->tpl_subtitle = 'ブロック設定';
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

        $this->bloc_id = $objFormParam->getValue('bloc_id');
        $this->device_type_id = $objFormParam->getValue('device_type_id', DEVICE_TYPE_PC);

        $objLayout = new SC_Helper_PageLayout_Ex();

        switch ($this->getMode()) {
            // 登録/更新
            case 'confirm':
                if (!$is_error) {
                    $this->arrErr = $this->lfCheckError($objFormParam, $this->arrErr, $objLayout);
                    if (SC_Utils_Ex::isBlank($this->arrErr)) {
                        $result = $this->doRegister($objFormParam, $objLayout);
                        if ($result !== false) {
                            $arrPram = array(
                                'bloc_id' => $result,
                                'device_type_id' => $this->device_type_id,
                                'msg' => 'on',
                            );

                            SC_Response_Ex::reload($arrPram, true);
                            SC_Response_Ex::actionExit();
                        }
                    }
                }
                break;

            // 削除
            case 'delete':
                if (!$is_error) {
                    if ($this->doDelete($objFormParam, $objLayout)) {
                        $arrPram = array(
                            'device_type_id' => $this->device_type_id,
                            'msg' => 'on',
                        );

                        SC_Response_Ex::reload($arrPram, true);
                        SC_Response_Ex::actionExit();
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

        if (!$is_error) {
            // ブロック一覧を取得
            $this->arrBlocList = $objLayout->getBlocs($this->device_type_id);
            // bloc_id が指定されている場合にはブロックデータの取得
            if (!SC_Utils_Ex::isBlank($this->bloc_id)) {
                $arrBloc = $this->getBlocTemplate($this->device_type_id, $this->bloc_id, $objLayout);
                $objFormParam->setParam($arrBloc);
            }
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
    function destroy() {
        parent::destroy();
    }

    /**
     * パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BLOC_ID'), 'bloc_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_DEVICE_TYPE_ID'), 'device_type_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BLOC_NAME'), 'bloc_name', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_FILE_NAME'), 'filename', STEXT_LEN, 'a', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BLOC_DATA'), 'bloc_html');
    }

    /**
     * ブロックのテンプレートを取得する.
     *
     * @param integer $device_type_id 端末種別ID
     * @param integer $bloc_id ブロックID
     * @param SC_Helper_PageLayout $objLayout SC_Helper_PageLayout インスタンス
     * @return array ブロック情報の配列
     */
    function getBlocTemplate($device_type_id, $bloc_id, &$objLayout) {
        $arrBloc = $objLayout->getBlocs($device_type_id, 'bloc_id = ?', array($bloc_id));
        if (SC_Utils_Ex::isAbsoluteRealPath($arrBloc[0]['tpl_path'])) {
            $tpl_path = $arrBloc[0]['tpl_path'];
        } else {
            $tpl_path = SC_Helper_PageLayout_Ex::getTemplatePath($device_type_id) . BLOC_DIR . $arrBloc[0]['tpl_path'];
        }
        if (file_exists($tpl_path)) {
            $arrBloc[0]['bloc_html'] = file_get_contents($tpl_path);
        }
        return $arrBloc[0];
    }

    /**
     * 登録を実行する.
     *
     * ファイルの作成に失敗した場合は, エラーメッセージを出力し,
     * データベースをロールバックする.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param SC_Helper_PageLayout $objLayout SC_Helper_PageLayout インスタンス
     * @return integer|boolean 登録が成功した場合, 登録したブロックID;
     *                         失敗した場合 false
     */
    function doRegister(&$objFormParam, &$objLayout) {
        $arrParams = $objFormParam->getHashArray();

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        // blod_id が空の場合は新規登録
        $is_new = SC_Utils_Ex::isBlank($arrParams['bloc_id']);
        $bloc_dir = $objLayout->getTemplatePath($arrParams['device_type_id']) . BLOC_DIR;
        // 既存データの重複チェック
        if (!$is_new) {
            $arrExists = $objLayout->getBlocs($arrParams['device_type_id'], 'bloc_id = ?', array($arrParams['bloc_id']));

            // 既存のファイルが存在する場合は削除しておく
            $exists_file = $bloc_dir . $arrExists[0]['filename'] . '.tpl';
            if (file_exists($exists_file)) {
                unlink($exists_file);
            }
        }

        $table = 'dtb_bloc';
        $arrValues = $objQuery->extractOnlyColsOf($table, $arrParams);
        $arrValues['tpl_path'] = $arrParams['filename'] . '.tpl';
        $arrValues['update_date'] = 'CURRENT_TIMESTAMP';

        // 新規登録
        if ($is_new || SC_Utils_Ex::isBlank($arrExists)) {
            $objQuery->setOrder('');
            $arrValues['bloc_id'] = 1 + $objQuery->max('bloc_id', $table, 'device_type_id = ?',
                                                       array($arrValues['device_type_id']));
            $arrValues['create_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->insert($table, $arrValues);
        }
        // 更新
        else {
            $objQuery->update($table, $arrValues, 'bloc_id = ? AND device_type_id = ?',
                              array($arrValues['bloc_id'], $arrValues['device_type_id']));
        }

        $bloc_path = $bloc_dir . $arrValues['tpl_path'];
        if (!SC_Helper_FileManager_Ex::sfWriteFile($bloc_path, $arrParams['bloc_html'])) {
            $this->arrErr['err'] = '※ ブロックの書き込みに失敗しました<br />';
            $objQuery->rollback();
            return false;
        }

        $objQuery->commit();
        return $arrValues['bloc_id'];
    }

    /**
     * 削除を実行する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param SC_Helper_PageLayout $objLayout SC_Helper_PageLayout インスタンス
     * @return boolean 登録が成功した場合 true; 失敗した場合 false
     */
    function doDelete(&$objFormParam, &$objLayout) {
        $arrParams = $objFormParam->getHashArray();
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        $arrExists = $objLayout->getBlocs($arrParams['device_type_id'], 'bloc_id = ? AND deletable_flg = 1',
                                          array($arrParams['bloc_id']));
        $is_error = false;
        if (!SC_Utils_Ex::isBlank($arrExists)) {
            $objQuery->delete('dtb_bloc', 'bloc_id = ? AND device_type_id = ?',
                              array($arrExists[0]['bloc_id'], $arrExists[0]['device_type_id']));
            $objQuery->delete('dtb_blocposition', 'bloc_id = ? AND device_type_id = ?',
                              array($arrExists[0]['bloc_id'], $arrExists[0]['device_type_id']));

            $bloc_dir = $objLayout->getTemplatePath($arrParams['device_type_id']) . BLOC_DIR;
            $exists_file = $bloc_dir . $arrExists[0]['filename'] . '.tpl';

            // ファイルの削除
            if (file_exists($exists_file)) {
                if (!unlink($exists_file)) {
                    $is_error = true;
                }
            }
        } else {
            $is_error = true;
        }

        if ($is_error) {
            $this->arrErr['err'] = '※ ブロックの削除に失敗しました<br />';
            $objQuery->rollback();
            return false;
        }
        $objQuery->commit();
        return true;
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
        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_BLOC_NAME'), 'bloc_name', STEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objErr->doFunc(array(SC_I18n_Ex::t('PARAM_LABEL_FILE_NAME'), 'filename', STEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK','FILE_NAME_CHECK_BY_NOUPLOAD'));

        $where = 'filename = ?';
        $arrValues = array($arrParams['filename']);

        // 変更の場合は自ブロックを除外
        if (!SC_Utils_Ex::isBlank($arrParams['bloc_id'])) {
            $where .= ' AND bloc_id <> ?';
            $arrValues[] = $arrParams['bloc_id'];
        }
        $arrBloc = $objLayout->getBlocs($arrParams['device_type_id'], $where, $arrValues);
        if (!SC_Utils_Ex::isBlank($arrBloc)) {
            $objErr->arrErr['filename'] = '※ 同じファイル名のデータが存在しています。別のファイル名を入力してください。<br />';
        }
        return $objErr->arrErr;
    }
}
