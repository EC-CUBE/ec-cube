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
 * ポイント設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Point extends LC_Page_Admin_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/point.tpl';
        $this->tpl_subno = 'point';
        $this->tpl_mainno = 'basis';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = 'ポイント設定';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        $objDb = new SC_Helper_DB_Ex();
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        // POST値の取得
        $objFormParam->setParam($_POST);

        if ($objDb->sfGetBasisExists()) {
            $this->tpl_mode = 'update';
        } else {
            $this->tpl_mode = 'insert';
        }

        if (!empty($_POST)) {
            // 入力値の変換
            $objFormParam->convParam();
            $this->arrErr = $objFormParam->checkError();

            if (count($this->arrErr) == 0) {
                switch ($this->getMode()) {
                    case 'update':
                        $this->lfUpdateData($objFormParam->getHashArray()); // 既存編集
                        break;
                    case 'insert':
                        $this->lfInsertData($objFormParam->getHashArray()); // 新規作成
                        break;
                    default:
                        break;
                }
                // 再表示
                $this->tpl_onload = "window.alert('ポイント設定が完了しました。');";
            }
        } else {
            $arrRet = $objDb->sfGetBasisData();
            $objFormParam->setParam($arrRet);
        }
        $this->arrForm = $objFormParam->getFormParamList();
    }

    /* パラメーター情報の初期化 */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('ポイント付与率', 'point_rate', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('会員登録時付与ポイント', 'welcome_point', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
    }

    public function lfUpdateData($post)
    {
        // 入力データを渡す。
        $sqlval = $post;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // UPDATEの実行
        $objQuery->update('dtb_baseinfo', $sqlval);
    }

    public function lfInsertData($post)
    {
        // 入力データを渡す。
        $sqlval = $post;
        $sqlval['id'] = 1;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // INSERTの実行
        $objQuery->insert('dtb_baseinfo', $sqlval);
    }
}
