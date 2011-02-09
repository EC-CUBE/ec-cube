<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_REALDIR . "pages/admin/LC_Page_Admin.php";

/**
 * 高度なデータベース管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System_Editdb extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $this->tpl_mainpage = 'system/editdb.tpl';
        $this->tpl_subnavi  = 'system/subnavi.tpl';
        $this->tpl_subno    = 'editdb';
        $this->tpl_mainno   = 'system';
        $this->tpl_subtitle = '高度なデータベース管理';
    }

    /**
     * フォームパラメータ初期化
     *
     * @return void
     */
    function initForm() {
        $objForm = new SC_FormParam();
        $objForm->addParam('モード', 'mode', INT_LEN, 'n', array('ALPHA_CHECK', 'MAX_LENGTH_CHECK'));
        $objForm->addParam('テーブル名', 'table_name');
        $objForm->addParam('カラム名', 'column_name');
        $objForm->addParam('インデックス', 'indexflag');
        $objForm->addParam('インデックス（変更後）', 'indexflag_new');
        $objForm->setParam($_POST);
        $this->objForm = $objForm;
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
        SC_Utils_Ex::sfIsSuccess(new SC_Session);

        //フォームの値を取得
        $this->initForm();

        switch($this->getMode()) {
        case 'confirm' :
            $this->lfDoChange();
            break;
        default:
            break;
	    }

        //インデックスの現在値を取得
        $this->arrForm = $this->lfGetIndexList();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    function lfGetTargetData() {
        $objQuery = new SC_Query();
        $arrIndexFlag = $this->objForm->getValue('indexflag');
        $arrIndexFlagNew = $this->objForm->getValue('indexflag_new');
        $arrTableName = $this->objForm->getValue('table_name');
        $arrColumnName =$this->objForm->getValue('column_name');
        $arrTarget = array();
        // 変更されている対象を走査
        for($i = 1; $i <= count($arrIndexFlag); $i++) {
            //入力値チェック
            $param = array('indexflag' => $arrIndexFlag[$i],
                            'indexflag_new' => $arrIndexFlagNew[$i],
                            'table_name' => $arrTableName[$i],
                            'column_name' => $arrColumnName[$i]);
            $objErr = new SC_CheckError($param);
            $objErr->doFunc(array("インデックス(" . $i . ")", 'indexflag', INT_LEN), array("NUM_CHECK"));
            $objErr->doFunc(array("インデックス変更後(" . $i . ")", 'indexflag_new', INT_LEN), array("NUM_CHECK"));
            $objErr->doFunc(array("インデックス変更後(" . $i . ")", 'indexflag_new', INT_LEN), array("NUM_CHECK"));
            $objErr->doFunc(array("テーブル名(" . $i . ")", 'table_name', STEXT_LEN), array("GRAPH_CHECK", "EXIST_CHECK", "MAX_LENGTH_CHECK"));
            $objErr->doFunc(array("カラム名(" . $i . ")", 'column_name', STEXT_LEN), array("GRAPH_CHECK", "EXIST_CHECK", "MAX_LENGTH_CHECK"));
            $arrErr = $objErr->arrErr;
            if(count($arrErr) != 0) {
                // 通常の送信ではエラーにならないはずです。
                $this->tpl_onload = "window.alert('不正なデータがあったため処理を中断しました。');";
                return;
            }
            if($param['indexflag'] != $param['indexflag_new']) {
                // 入力値がデータにある対象テーブルかのチェック
                if($objQuery->count('dtb_index_list', 'table_name = ? and column_name = ?', array($param['table_name'], $param['column_name']))) {
                    $arrTarget[] = $param;
                }
            }
        }
        return $arrTarget;
    }

    function lfDoChange() {
        $objQuery = new SC_Query();
        $arrTarget = $this->lfGetTargetData();
        if(count($arrTarget) == 0) {
            $this->tpl_onload = "window.alert('変更対象となるデータはありませんでした。');";
            return;
        }

        // 変更対象の設定変更
        foreach($arrTarget as $item) {
            $index_name = $item['table_name'] . '_' . $item['column_name'] . "_key";
            $arrField = array( 'fields' => array($item['column_name'] => array()));
            if($item['indexflag_new'] == '1') {
                $objQuery->createIndex($item['table_name'], $index_name, $arrField);
            }else{
                $objQuery->dropIndex($item['table_name'], $index_name);
            }
        }
        $this->tpl_onload = "window.alert('インデックスの変更が完了しました。');";
    }

    /**
     * インデックス設定を行う一覧を返す関数
     *
     * @return void
     */
    function lfGetIndexList()
    {
        // データベースからインデックス設定一覧を取得する
        $objQuery = new SC_Query();
        $objQuery->setOrder("table_name, column_name");
        $arrIndexList = $objQuery->select("table_name , column_name , recommend_flg, recommend_comment", "dtb_index_list");

        $table = "";
        foreach($arrIndexList as $key => $arrIndex) {
            // テーブルに対するインデックス一覧を取得
            if($table !== $arrIndex["table_name"]) {
                $table = $arrIndex["table_name"];
                $arrIndexes = $objQuery->listTableIndexes($table);
            }
            // インデックスが設定されているかを取得
            $idx_name = $table . "_" . $arrIndex["column_name"] . "_key";
            if(array_search($idx_name, $arrIndexes) === false) {
                $arrIndexList[$key]["indexflag"] = '';
            } else {
                $arrIndexList[$key]["indexflag"] = '1';
            }
        }
        return $arrIndexList;
    }

}
