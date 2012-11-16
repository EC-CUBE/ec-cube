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
 * 会員規約設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Kiyaku extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/kiyaku.tpl';
        $this->tpl_subno = 'kiyaku';
        $this->tpl_maintitle = t('TPL_MAINTITLE_006');
        $this->tpl_subtitle = t('LC_Page_Admin_Basis_Kiyaku_002');
        $this->tpl_mainno = 'basis';
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

        $objDb = new SC_Helper_DB_Ex();

        $mode = $this->getMode();
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($mode, $objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();
        $this->arrErr = $this->lfCheckError($mode, $objFormParam);
        $is_error = (!SC_Utils_Ex::isBlank($this->arrErr));

        $this->kiyaku_id = $objFormParam->getValue('kiyaku_id');
        if ($is_error) {
            trigger_error('', E_USER_ERROR);
            return;
        }

        // 要求判定
        switch ($mode) {
            // 編集処理
            case 'confirm':
                // POST値の引き継ぎ
                $this->arrForm = $_POST;

                if (!$is_error) {
                    if ($this->kiyaku_id == '') {
                        $result = $this->lfInsertClass($this->arrForm, $_SESSION['member_id']);    // 新規作成
                    } else {
                        $result = $this->lfUpdateClass($this->arrForm, $this->kiyaku_id);    // 既存編集
                    }

                    if ($result !== FALSE) {
                        $arrPram = array(
                            'kiyaku_id' => $result,
                            'msg' => 'on',
                        );

                        SC_Response_Ex::reload($arrPram, true);
                        SC_Response_Ex::actionExit();
                    }
                }
                break;
            // 削除
            case 'delete':
                $objDb->sfDeleteRankRecord('dtb_kiyaku', 'kiyaku_id', $this->kiyaku_id, '', true);

                // 再表示
                $this->objDisplay->reload();
                break;
            case 'down':
                $objDb->sfRankDown('dtb_kiyaku', 'kiyaku_id', $this->kiyaku_id);

                // 再表示
                $this->objDisplay->reload();
                break;
            case 'up':
                $objDb->sfRankUp('dtb_kiyaku', 'kiyaku_id', $this->kiyaku_id);

                // 再表示
                $this->objDisplay->reload();
                break;
            default:
                if (isset($_GET['msg']) && $_GET['msg'] == 'on') {
                    // 完了メッセージ
                    $this->tpl_onload = "alert('" . t('ALERT_004') . "');";
                }
                break;
        }

        $this->arrForm = $objFormParam->getFormParamList();

        if (!$is_error) {
            // 規約一覧を取得
            $this->arrKiyaku = $this->lfGetKiyakuList();
            // kiyaku_id が指定されている場合には規約データの取得
            if (!SC_Utils_Ex::isBlank($this->kiyaku_id)) {
                // 編集項目を取得する。
                $arrKiyakuData = $this->lfGetKiyakuDataByKiyakuID($this->kiyaku_id);

                // 入力項目にカテゴリ名を入力する。
                $this->arrForm['kiyaku_title'] = $arrKiyakuData[0]['kiyaku_title'];
                $this->arrForm['kiyaku_text'] = $arrKiyakuData[0]['kiyaku_text'];
                // POSTデータを引き継ぐ
                $this->tpl_kiyaku_id = $this->kiyaku_id;
            }
        } else {
            // 画面にエラー表示しないため, ログ出力
            GC_Utils_Ex::gfPrintLog('Error: ' . print_r($this->arrErr, true));
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

    /* DBへの挿入 */
    function lfInsertClass($arrData, $member_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // INSERTする値を作成する。
        $sqlval = array();
        $sqlval['kiyaku_title'] = $arrData['kiyaku_title'];
        $sqlval['kiyaku_text'] = $arrData['kiyaku_text'];
        $sqlval['creator_id'] = $member_id;
        $sqlval['rank'] = $objQuery->max('rank', 'dtb_kiyaku') + 1;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
        // INSERTの実行
        $sqlval['kiyaku_id'] = $objQuery->nextVal('dtb_kiyaku_kiyaku_id');
        $ret = $objQuery->insert('dtb_kiyaku', $sqlval);
        return ($ret) ? $sqlval['kiyaku_id'] : FALSE;
    }

    function lfGetKiyakuDataByKiyakuID($kiyaku_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $where = 'kiyaku_id = ?';
        return $objQuery->select('kiyaku_text, kiyaku_title', 'dtb_kiyaku', $where, array($kiyaku_id));
    }

    function lfGetKiyakuList() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $where = 'del_flg <> 1';
        $objQuery->setOrder('rank DESC');
        return $objQuery->select('kiyaku_title, kiyaku_text, kiyaku_id', 'dtb_kiyaku', $where);
    }

    /* DBへの更新 */
    function lfUpdateClass($arrData, $kiyaku_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // UPDATEする値を作成する。
        $sqlval['kiyaku_title'] = $arrData['kiyaku_title'];
        $sqlval['kiyaku_text'] = $arrData['kiyaku_text'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = 'kiyaku_id = ?';
        // UPDATEの実行
        $ret = $objQuery->update('dtb_kiyaku', $sqlval, $where, array($kiyaku_id));
        return ($ret) ? $kiyaku_id : FALSE;
    }

    function lfInitParam($mode, &$objFormParam) {
        switch ($mode) {
            case 'confirm':
                $objFormParam->addParam(t('PARAM_LABEL_KIYAKU_TITLE'), 'kiyaku_title', SMTEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('PARAM_LABEL_KIYAKU_TEXT'), 'kiyaku_text', MLTEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
            case 'delete':
            case 'pre_edit':
            case 'down':
            case 'up':
            default:
                $objFormParam->addParam(t('PARAM_LABEL_KIYAKU_ID'), 'kiyaku_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                break;
        }
    }

    /**
     * 入力エラーチェック
     *
     * @param string $mode
     * @return array
     */
    function lfCheckError($mode, $objFormParam) {
        $arrErr = $objFormParam->checkError();
        if (!isset($arrErr['name']) && $mode == 'confirm') {
            $post = $objFormParam->getHashArray();
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $arrRet = $objQuery->select('kiyaku_id, kiyaku_title', 'dtb_kiyaku', 'del_flg = 0 AND kiyaku_title = ?', array($post['kiyaku_title']));
            // 編集中のレコード以外に同じ名称が存在する場合
            if ($arrRet[0]['kiyaku_id'] != $post['kiyaku_id'] && $arrRet[0]['kiyaku_title'] == $post['kiyaku_title']) {
                $arrErr['name'] = t('LC_Page_Admin_Basis_Kiyaku_004');
            }
        }
        return $arrErr;
    }
}
