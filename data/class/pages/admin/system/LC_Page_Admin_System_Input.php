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
 * システム管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System_Input extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $this->tpl_mainpage = 'system/input.tpl';

        // ページ送り用ナンバーの取得
        $this->tpl_pageno = isset($_REQUEST['pageno']) ? $_REQUEST['pageno'] : 1;

        // マスタ-データから権限配列を取得
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrAUTHORITY = $masterData->getMasterData('mtb_authority');
        
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
        // ログインチェック
        SC_Utils::sfIsSuccess(new SC_Session());

        // トランザクショントークンの取得
        $this->transactionid = SC_Helper_Session_Ex::getToken();

        switch($this->getMode()) {
        case 'new':
            $this->execNewMode();
            break;

        case 'edit':
            $this->execEditMode();
            break;

        case 'parent_reload':
            $this->execParentReloadMode();
            // defaultアクションも実行させるためbreakしない

        default:
            $this->execDefaultMode();
            break;
        }
        $this->setTemplate($this->tpl_mainpage);
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
     * switchアクション振り分け用パラメータを取得する.
     *
     * @param void
     * @return string モード名
     */
    function getMode() {
        $mode = '';
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if(isset($_GET['mode'])) $mode = $_GET['mode'];
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(isset($_POST['mode'])) $mode = $_POST['mode'];
        }
        return $mode;
    }

    /**
     * newアクションの実行
     * メンバーデータの新規登録を行う.
     *
     * @param void
     * @return void
     */
    function execNewMode() {
        if (SC_Helper_Session_Ex::isValidToken() !== true) {
            SC_Utils::sfDispError('');
        }

        $this->initNewMode();

        $arrErr = $this->validateNewMode();

        if (count($arrErr) > 0) {
            // 入力された値を保持する
            $this->tpl_mode      = $_POST['mode'];
            $this->tpl_member_id = $_POST['member_id'];
            $this->tpl_old_login_id = $_POST['old_login_id'];
            $this->arrForm = $this->objForm->getHashArray();
            // パスワードは保持しない
            $this->arrForm['password'] = '';
            // エラー情報をセットする
            $this->arrErr = $arrErr;
            // トランザクショントークンの取得
            $this->transactionid = SC_Helper_Session_Ex::getToken();
            return;
        }

        $this->insertMemberData($this->objForm->getHashArray());
        $this->objDisplay->reload(array('mode' => 'parent_reload'));
    }

    /**
     * newアクションの初期化.
     * SC_FormParamのインスタンスをメンバ変数にセットする.
     *
     * @param void
     * @return void
     */
    function initNewMode($mode = "") {
        $objForm = new SC_FormParam();

        $objForm->addParam('名前', 'name', STEXT_LEN, 'KV', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objForm->addParam('所属', 'department', STEXT_LEN, 'KV', array('MAX_LENGTH_CHECK'));
        $objForm->addParam('ログインID', 'login_id', '' , '', array('EXIST_CHECK', 'ALNUM_CHECK'));
        if ($mode == "edit" && $_POST['password'] == DUMMY_PASS) {
            $objForm->addParam('パスワード', 'password', '' , '', array('EXIST_CHECK'));
        } else {
        	$objForm->addParam('パスワード', 'password', '' , '', array('EXIST_CHECK', 'ALNUM_CHECK'));
        }
        $objForm->addParam('権限', 'authority', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));

        $objForm->setParam($_POST);
        $objForm->convParam();

        $this->objForm = $objForm;
    }

    /**
     * newアクションのパラメータ検証を行う.
     *
     * @param void
     * @return array エラー情報の連想配列
     */
    function validateNewMode() {
        $arrErr = $this->objForm->checkError();
        if (isset($arrErr) && count($arrErr) > 0) return $arrErr;

        // ログインID・パスワードの文字数チェック
        $objErr = new SC_CheckError();
        $objErr->doFunc(array("パスワード", 'password', ID_MIN_LEN, ID_MAX_LEN), array("NUM_RANGE_CHECK"));
        $objErr->doFunc(array("ログインID", 'login_id', ID_MIN_LEN, ID_MAX_LEN), array("NUM_RANGE_CHECK"));

        $arrErr = $objErr->arrErr;

        // 管理者名が登録済みでないか
        if ($this->memberDataExists('name = ?', $_POST['name'])) {
            $arrErr['name'] = "既に登録されている名前なので利用できません。<br>";
        }
        // ログインIDが登録済みでないか
        if ($this->memberDataExists('login_id = ?', $_POST['login_id'])) {
            $arrErr['login_id'] = "既に登録されているIDなので利用できません。<br>";
        }

        return $arrErr;
    }

    /**
     * editアクションの実行
     * メンバーデータの更新を行う.
     *
     * @param void
     * @return void
     */
    function execEditMode() {
        if (SC_Helper_Session_Ex::isValidToken() !== true) {
            SC_Utils::sfDispError('');
        }

        $this->initNewMode("edit");

        $arrErr = $this->validateEditMode();

        if (count($arrErr) > 0) {
            // 入力された値を保持する
            $this->tpl_mode      = $_POST['mode'];
            $this->tpl_member_id = $_POST['member_id'];
            $this->tpl_old_login_id = $_POST['old_login_id'];
            $this->arrForm = $this->objForm->getHashArray();
            // パスワードは保持しない
            $this->arrForm['password'] = '';
            // エラー情報をセットする
            $this->arrErr = $arrErr;
            // トランザクショントークンの取得
            $this->transactionid = SC_Helper_Session_Ex::getToken();
            return;
        }

        $this->updateMemberData($_POST['member_id'], $this->objForm->getHashArray());
        // 親ウィンドウを更新後、自ウィンドウを閉じる。
        $url = ADMIN_SYSTEM_URLPATH . "?pageno=" . $_POST['pageno'];
        $this->tpl_onload = "fnUpdateParent('".$url."'); window.close();";
    }

    /**
     * editアクションのパラメータ検証を行う.
     *
     * @param void
     * @return array エラー情報の連想配列
     */
    function validateEditMode() {
        $arrErr = $this->objForm->checkError();
        if (isset($arrErr) && count($arrErr) > 0) return $arrErr;

        // ログインID・パスワードの文字数チェック
        $objErr = new SC_CheckError();
        $objErr->doFunc(array("パスワード", 'password', ID_MIN_LEN, ID_MAX_LEN), array("SPTAB_CHECK" ,"NUM_RANGE_CHECK"));
        $objErr->doFunc(array("ログインID", 'login_id', ID_MIN_LEN, ID_MAX_LEN), array("SPTAB_CHECK" ,"NUM_RANGE_CHECK"));

        $arrErr = $objErr->arrErr;

        // ログインIDが変更されている場合はチェックする。
        if ($_POST['login_id'] != $_POST['old_login_id']) {
            // ログインIDが登録済みでないか
            if ($this->memberDataExists('login_id = ?', $_POST['login_id'])) {
                $arrErr['login_id'] = "既に登録されているIDなので利用できません。<br>";
            }
        }

        return $arrErr;
    }

    /**
     * parent_reloadアクションを実行する.
     * テンプレートに親windowをリロードするjavascriptをセットする.
     *
     * @param void
     * @return void
     */
    function execParentReloadMode() {
        $url = ADMIN_SYSTEM_URLPATH;
        $this->tpl_onload = "fnUpdateParent('$url')";
    }

    /**
     * defaultアクションを実行する.
     * 初回表示時に実行される.
     * $GET['id']が渡された場合、編集モードとして表示,
     * 無い場合は新規登録モードとして表示する.
     *
     * @param void
     * @return void
     */
    function execDefaultMode() {
        // $_GET['id']があれば編集モードで表示する
        if (isset($_GET['id']) && SC_Utils::sfIsInt($_GET['id'])) {
            $this->tpl_mode      = 'edit';
            $this->tpl_member_id = $_GET['id'];
            $this->tpl_onfocus   = "fnClearText(this.name);";
            $this->arrForm       = $this->getMemberData($_GET['id']);
            $this->arrForm['password'] = DUMMY_PASS;
            $this->tpl_old_login_id    = $this->arrForm['login_id'];
        // 新規作成モードで表示
        } else {
            $this->tpl_mode = "new";
            $this->arrForm['authority'] = -1;
        }
    }

    /**
     * DBからmember_idに対応する管理者データを取得する
     *
     * @param integer $id メンバーID
     * @return array 管理者データの連想配列, 無い場合は空の配列を返す
     */
    function getMemberData($id) {
        $table   = 'dtb_member';
        $columns = 'name,department,login_id,authority';
        $where   = 'member_id = ?';

        $objQuery = new SC_Query();
        $arrRet = $objQuery->select($columns, $table, $where, array($id));

        if (is_null($arrRet)) return array();

        return $arrRet[0];
    }

    /**
     *  値が登録済みかどうかを調べる
     *
     * @param string $where WHERE句
     * @param string $val 検索したい値
     * @return boolean 登録済みならtrue, 未登録ならfalse
     */
    function memberDataExists($where, $val) {
        $table = 'dtb_member';

        $objQuery = new SC_Query();
        $count = $objQuery->count($table, $where, array($val));

        if ($count > 0) return true;
        return false;
    }

    /**
     * 入力された管理者データをInsertする.
     *
     * @param array 管理者データの連想配列
     * @return void
     */
    function insertMemberData($arrMemberData) {
        $objQuery = new SC_Query();

        // INSERTする値を作成する.
        $sqlVal = array();
        $sqlVal['name']        = $arrMemberData['name'];
        $sqlVal['department']  = $arrMemberData['department'];
        $sqlVal['login_id']    = $arrMemberData['login_id'];
        $sqlVal['password']    = sha1($arrMemberData['password'] . ':' . AUTH_MAGIC);
        $sqlVal['authority']   = $arrMemberData['authority'];
        $sqlVal['rank']        = $objQuery->max('rank', 'dtb_member') + 1;
        $sqlVal['work']        = '1'; // 稼働に設定
        $sqlVal['del_flg']     = '0'; // 削除フラグをOFFに設定
        $sqlVal['creator_id']  = $_SESSION['member_id'];
        $sqlVal['create_date'] = 'NOW()';
        $sqlVal['update_date'] = 'NOW()';

        // INSERTの実行
        $sqlVal['member_id'] = $objQuery->nextVal('dtb_member_member_id');
        $objQuery->insert('dtb_member', $sqlVal);
    }

    /**
     * 管理者データをUpdateする.
     *
     * @param array 管理者データの連想配列
     * @return void
     */
    function updateMemberData($member_id, $arrMemberData) {
        $objQuery = new SC_Query();

        // Updateする値を作成する.
        $sqlVal = array();
        $sqlVal['name']        = $arrMemberData['name'];
        $sqlVal['department']  = $arrMemberData['department'];
        $sqlVal['login_id']    = $arrMemberData['login_id'];
        $sqlVal['authority']   = $arrMemberData['authority'];
        $sqlVal['update_date'] = 'NOW()';
        if($arrMemberData['password'] != DUMMY_PASS) {
            $sqlVal['password'] = sha1($arrMemberData['password'] . ":" . AUTH_MAGIC);
        }

        $where = "member_id = ?";

        // UPDATEの実行
        $objQuery->update("dtb_member", $sqlVal, $where, array($member_id));
    }
}
?>
