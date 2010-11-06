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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * システム情報 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_System_Editdb.php 18701 2010-06-14 08:30:18Z nanasess $
 */
class LC_Page_Admin_System_Editdb extends LC_Page {

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
        $objForm->addParam('mode', 'mode', INT_LEN, '', array('ALPHA_CHECK', 'MAX_LENGTH_CHECK'));
        $objForm->setParam($_GET);
        $this->objForm = $objForm;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {

        SC_Utils_Ex::sfIsSuccess(new SC_Session);
        $objView = new SC_AdminView();

        $this->initForm();
        switch($this->objForm->getValue('mode')) {

        default:
            break;
        }

        // インデックス一覧を取得する
        $arrIndexList = $this->lfGetIndexList();

        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
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
     * インデックス設定を行う一覧を返す関数
     *
     * @return void
     */
    function lfGetIndexList()
    {
        // データベースからインデックス設定一覧を取得する
        $objQuery = new SC_Query();
        $objQuery->setOrder("table_name, column_name");
        $arrIndexList = $objQuery->select("table_name, column_name, recommend_flg, recommend_comment", "dtb_index_list");

        $table = "";
        foreach($arrIndexList as $key => $arrIndex) {
            // テーブルに対するインデックス一覧を取得
            if($table !== $arrIndex["table"]) {
                $table = $arrIndex["table"];
                $arrIndexes = $objQuery->listTableIndexes($table);
            }
 
            // インデックスが設定されているかを取得
            if(array_search($table . "_" . $arrIndex["column"] . "_key", $arrIndexes) === false) {
                $arrIndexList[$key]["indexflag"] = false;
            } else {
                $arrIndexList[$key]["indexflag"] = true;
            }
        }
    
        return $arrIndexList;
    }

}
