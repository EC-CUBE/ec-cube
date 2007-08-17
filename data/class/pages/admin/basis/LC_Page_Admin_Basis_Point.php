<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * ポイント設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Point extends LC_Page {

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
        $conn = new SC_DBConn();
        $objView = new SC_AdminView();
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

        if(isset($_POST['mode']) && !empty($_POST['mode'])) {
            // 入力値の変換
            $this->objFormParam->convParam();
            $this->arrErr = $this->objFormParam->checkError();

            if(count($this->arrErr) == 0) {
                switch($_POST['mode']) {
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
