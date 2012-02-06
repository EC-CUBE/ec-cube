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

/**
 * EC-CUBEアプリケーション管理:アプリケーション設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_OwnersStore_Settings extends LC_Page_Admin_Ex {

    /** SC_FormParamのインスタンス */
    var $objForm;

    /** リクエストパラメーターを格納する連想配列 */
    var $arrForm;

    /** バリデーションエラー情報を格納する連想配列 */
    var $arrErr;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $this->tpl_mainpage = 'ownersstore/settings.tpl';
        $this->tpl_mainno   = 'ownersstore';
        $this->tpl_subno    = 'settings';
        $this->tpl_maintitle = 'オーナーズストア';
        $this->tpl_subtitle = '認証キー設定';
        $this->httpCacheControl('nocache');
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
        switch ($this->getMode()) {
        // 入力内容をDBへ登録する
        case 'register':
            $this->execRegisterMode();
            break;
        // 初回表示
        default:
            $this->execDefaultMode();
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

    /**
     * registerアクションの実行.
     * 入力内容をDBへ登録する.
     *
     * @param void
     * @return void
     */
    function execRegisterMode() {
        // パラメーターオブジェクトの初期化
        $this->initRegisterMode();
        // POSTされたパラメーターの検証
        $arrErr = $this->validateRegistermode();

        // エラー時の処理
        if (!empty($arrErr)) {
            $this->arrErr  = $arrErr;
            $this->arrForm = $this->objForm->getHashArray();
            return;
        }

        // エラーがなければDBへ登録
        $arrForm = $this->objForm->getHashArray();
        $this->registerOwnersStoreSettings($arrForm);

        $this->arrForm = $arrForm;

        $this->tpl_onload = "alert('登録しました。')";
    }

    /**
     * registerアクションの初期化.
     * SC_FormParamを初期化しメンバ変数にセットする.
     *
     * @param void
     * @return void
     */
    function initRegisterMode() {
        // 前後の空白を削除
        if (isset($_POST['public_key'])) {
            $_POST['public_key'] = trim($_POST['public_key']);
        }

        $objForm = new SC_FormParam_Ex();
        $objForm->addParam('認証キー', 'public_key', LTEXT_LEN, '', array('EXIST_CHECK', 'ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objForm->setParam($_POST);

        $this->objForm = $objForm;
    }

    /**
     * registerアクションのパラメーターを検証する.
     *
     * @param void
     * @return array エラー情報を格納した連想配列
     */
    function validateRegistermode() {
        return $this->objForm->checkError();
    }

    /**
     * defaultアクションの実行.
     * DBから登録内容を取得し表示する.
     *
     * @param void
     * @return void
     */
    function execDefaultMode() {
        $this->arrForm = $this->getOwnersStoreSettings();
    }

    /**
     * DBへ入力内容を登録する.
     *
     * @param array $arrSettingsData ｵｰﾅｰｽﾞｽﾄｱ設定の連想配列
     * @return void
     */
    function registerOwnersStoreSettings($arrSettingsData) {
        $table = 'dtb_ownersstore_settings';
        $objQuery = new SC_Query_Ex();
        $exists = $objQuery->exists($table);

        if ($exists) {
            $objQuery->update($table, $arrSettingsData);
        } else {
            $objQuery->insert($table, $arrSettingsData);
        }
    }

    /**
     * DBから登録内容を取得する.
     *
     * @param void
     * @return array
     */
    function getOwnersStoreSettings(){
        $table   = 'dtb_ownersstore_settings';
        $colmuns = '*';

        $objQuery = new SC_Query_Ex();
        $arrRet = $objQuery->select($colmuns, $table);

        if (isset($arrRet[0])) return $arrRet[0];

        return array();
    }
}
