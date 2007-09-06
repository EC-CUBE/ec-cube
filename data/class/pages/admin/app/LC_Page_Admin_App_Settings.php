<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * EC-CUBEアプリケーション管理:アプリケーション設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_App_Settings extends LC_Page {

    /** SC_FormParamのインスタンス */
    var $objForm;

    /** リクエストパラメータを格納する連想配列 */
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

        $this->tpl_mainpage = 'app/settings.tpl';
        $this->tpl_subnavi  = 'app/subnavi.tpl';
        $this->tpl_mainno   = 'app';
        $this->tpl_subno    = 'settings';
        $this->tpl_subtitle = 'アプリケーション設定';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {

        // ログインチェック
        SC_Utils::sfIsSuccess(new SC_Session());

        // トランザクションIDの取得
        $this->transactionid = $this->getToken();

        // $_POST['mode']によってアクション振り分け
        switch($this->getMode()) {
        // 入力内容をDBへ登録する
        case 'register':
            $this->execRegisterMode();
            break;
        // 初回表示
        default:
            $this->execDefaultMode();
        }

        // ページ出力
        $objView = new SC_AdminView();
        $objView->assignObj($this);
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
     * switchアクション振り分け用パラメータを取得する.
     *
     * @param void
     * @return string モード名
     */
    function getMode() {
        $mode = '';
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (isset($_GET['mode'])) $mode = $_GET['mode'];
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['mode'])) $mode = $_POST['mode'];
        }
        return $mode;
    }

    /**
     * registerアクションの実行.
     * 入力内容をDBへ登録する.
     *
     * @param void
     * @return void
     */
    function execRegisterMode() {
        if ($this->isValidToken() !== true) {
            SC_Utils_Ex::sfDispError('');
        }
        // パラメータオブジェクトの初期化
        $this->initRegisterMode();
        // POSTされたパラメータの検証
        $arrErr = $this->validateRegistermode();

        // エラー時の処理
        if (!empty($arrErr)) {
            $this->arrErr  = $arrErr;
            $this->arrForm = $this->objForm->getHashArray();
            $this->transactionid = $this->getToken();
            return;
        }

        // エラーがなければDBへ登録
        $arrForm = $this->objForm->getHashArray();
        $this->registerAppSettings($arrForm);

        $this->arrForm = $arrForm;

        $this->tpl_onload = "alert('登録しました。')";
        $this->transactionid = $this->getToken();
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

        $objForm = new SC_FormParam();
        $objForm->addParam('認証キー', 'public_key', MTEXT_LEN, '', array('EXIST_CHECK', 'ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objForm->setParam($_POST);

        $this->objForm = $objForm;
    }

    /**
     * registerアクションのパラメータを検証する.
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
        // $this->arrForm = $this->getAppSettings();
    }

    /**
     * DBへ入力内容を登録する.
     *
     * @param array $arrSettingsData アプリケーション設定の連想配列
     * @return void
     */
    function registerAppSettings($arrSettingsData) {

    }

    /**
     * DBから登録内容を取得する.
     *
     * @param void
     * @return array
     */
    function getAppSettings(){
        $table   = 'dtb_application_settings';
        $colmuns = '*';
        $where   = 'app_id = 1';

        $objQuery = new SC_Query();
        $arrRet = $objQuery->select($colmuns, $table, $where);

        if (isset($arrRet[0])) return $arrRet[0];

        return array();
    }
}
?>
