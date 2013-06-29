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

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * ブロック編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_Bloc extends LC_Page_Admin_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
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
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();
        $this->arrErr = $objFormParam->checkError();
        $is_error = (!SC_Utils_Ex::isBlank($this->arrErr));

        $this->bloc_id = $objFormParam->getValue('bloc_id');
        $this->device_type_id = $objFormParam->getValue('device_type_id', DEVICE_TYPE_PC);

        $objBloc = new SC_Helper_Bloc_Ex($this->device_type_id);
        $objLayout = new SC_Helper_PageLayout_Ex();

        switch ($this->getMode()) {
            // 登録/更新
            case 'confirm':
                if (!$is_error) {
                    $this->arrErr = $this->lfCheckError($objFormParam, $this->arrErr, $objBloc);
                    if (SC_Utils_Ex::isBlank($this->arrErr)) {
                        $result = $this->doRegister($objFormParam, $objBloc);
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
                    if ($this->doDelete($objFormParam, $objBloc)) {
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
            $this->arrBlocList = $objBloc->getList();
            // bloc_id が指定されている場合にはブロックデータの取得
            if (!SC_Utils_Ex::isBlank($this->bloc_id)) {
                $arrBloc = $this->getBlocTemplate($this->bloc_id, $objBloc);
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
     * パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('ブロックID', 'bloc_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('端末種別ID', 'device_type_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ブロック名', 'bloc_name', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ファイル名', 'filename', STEXT_LEN, 'a', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ブロックデータ', 'bloc_html');
    }

    /**
     * ブロックのテンプレートを取得する.
     *
     * @param integer $bloc_id ブロックID
     * @param SC_Helper_Bloc_Ex $objBloc SC_Helper_Bloc_Ex インスタンス
     * @return array ブロック情報の配列
     */
    function getBlocTemplate($bloc_id, SC_Helper_Bloc_Ex &$objBloc)
    {
        $arrBloc = $objBloc->get($bloc_id);

        return $arrBloc;
    }

    /**
     * 登録を実行する.
     *
     * ファイルの作成に失敗した場合は, エラーメッセージを出力し,
     * データベースをロールバックする.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param SC_Helper_Bloc $objBloc SC_Helper_Bloc インスタンス
     * @return integer|boolean 登録が成功した場合, 登録したブロックID;
     *                         失敗した場合 false
     */
    function doRegister(&$objFormParam, SC_Helper_Bloc_Ex &$objBloc)
    {
        $arrParams = $objFormParam->getHashArray();
        $result = $objBloc->save($arrParams);

        if (!$result) {
            $this->arrErr['err'] = '※ ブロックの書き込みに失敗しました<br />';
        }

        return $result;
    }

    /**
     * 削除を実行する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param SC_Helper_Bloc $objBloc SC_Helper_Bloc インスタンス
     * @return boolean 登録が成功した場合 true; 失敗した場合 false
     */
    function doDelete(&$objFormParam, SC_Helper_Bloc_Ex &$objBloc)
    {
        $arrParams = $objFormParam->getHashArray();
        $result = $objBloc->delete($arrParams['bloc_id']);

        if (!$result) {
            $this->arrErr['err'] = '※ ブロックの削除に失敗しました<br />';
        }

        return $result;
    }

    /**
     * エラーチェックを行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラーメッセージの配列
     */
    function lfCheckError(&$objFormParam, &$arrErr, SC_Helper_Bloc_Ex &$objBloc)
    {
        $arrParams = $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrParams);
        $objErr->arrErr =& $arrErr;
        $objErr->doFunc(array('ブロック名', 'bloc_name', STEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objErr->doFunc(array('ファイル名', 'filename', STEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK','FILE_NAME_CHECK_BY_NOUPLOAD'));

        $where = 'filename = ?';
        $arrValues = array($arrParams['filename']);

        // 変更の場合は自ブロックを除外
        if (!SC_Utils_Ex::isBlank($arrParams['bloc_id'])) {
            $where .= ' AND bloc_id <> ?';
            $arrValues[] = $arrParams['bloc_id'];
        }
        $arrBloc = $objBloc->getWhere($where, $arrValues);
        if (!SC_Utils_Ex::isBlank($arrBloc)) {
            $objErr->arrErr['filename'] = '※ 同じファイル名のデータが存在しています。別のファイル名を入力してください。<br />';
        }

        return $objErr->arrErr;
    }
}
