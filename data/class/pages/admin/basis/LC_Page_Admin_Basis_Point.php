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
require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * ポイント設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Point extends LC_Page_Admin {

    // {{{ properties

    /** フォームパラメータの配列 */
    var $objFormParam;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/point.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_subno = 'point';
        $this->tpl_mainno = 'basis';
        $this->tpl_subtitle = 'ポイント設定';
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
        $objSess = new SC_Session();
        $objQuery = new SC_Query();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        $cnt = $objQuery->count("dtb_baseinfo");

        if ($cnt > 0) {
            $this->tpl_mode = "update";
        } else {
            $this->tpl_mode = "insert";
        }
        //TODO 要リファクタリング(MODE if利用)
        if($this->getMode()!=null) {
            // 入力値の変換
            $this->objFormParam->convParam();
            $this->arrErr = $this->objFormParam->checkError();

            if(count($this->arrErr) == 0) {
                switch($this->getMode()) {
                case 'update':
                    $this->lfUpdateData(); // 既存編集
                    break;
                case 'insert':
                    $this->lfInsertData(); // 新規作成
                    break;
                default:
                    break;
                }
                // 再表示
                //sfReload();
                $this->tpl_onload = "window.alert('ポイント設定が完了しました。');";
            }
        } else {
            $arrCol = $this->objFormParam->getKeyList(); // キー名一覧を取得
            $col	= SC_Utils_Ex::sfGetCommaList($arrCol);
            $arrRet = $objQuery->select($col, "dtb_baseinfo");
            // POST値の取得
            $this->objFormParam->setParam($arrRet[0]);
        }

        $this->arrForm = $this->objFormParam->getFormParamList();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("ポイント付与率", "point_rate", PERCENTAGE_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("会員登録時付与ポイント", "welcome_point", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    }

    function lfUpdateData() {
        // 入力データを渡す。
        $sqlval = $this->objFormParam->getHashArray();
        $sqlval['update_date'] = 'Now()';
        $objQuery = new SC_Query();
        // UPDATEの実行
        $ret = $objQuery->update("dtb_baseinfo", $sqlval);
    }

    function lfInsertData() {
        // 入力データを渡す。
        $sqlval = $this->objFormParam->getHashArray();
        $sqlval['update_date'] = 'Now()';
        $objQuery = new SC_Query();
        // INSERTの実行
        $ret = $objQuery->insert("dtb_baseinfo", $sqlval);
    }
}
?>
