<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * サイト管理設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Control extends LC_Page {

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
        $this->tpl_mainpage = 'basis/control.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_mainno = 'basis';
        $this->tpl_subno = 'control';
        $this->tpl_subtitle = 'サイト管理設定';
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

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
            case 'edit':
                // 入力値の変換
                $this->objFormParam->convParam();

                // エラーチェック
                $this->arrErr = $this->lfCheckError();
                if(count($this->arrErr) == 0) {
                    $this->lfSiteControlData($_POST['control_id']);
                    // javascript実行
                    $this->tpl_onload = "alert('更新が完了しました。');";
                }

                break;
            default:
                break;
        }

        // サイト管理情報の取得
        $arrSiteControlList = $this->lfGetControlList();
        $masterData = new SC_DB_MasterData_Ex();

        // プルダウンの作成
        for ($i = 0; $i < count($arrSiteControlList); $i++) {
            switch ($arrSiteControlList[$i]["control_id"]) {
                // トラックバック
                case SITE_CONTROL_TRACKBACK:
                    $arrSiteControlList[$i]["control_area"]
                            = $masterData->getMasterData("mtb_site_control_track_back");
                    break;
                // アフィリエイト
                case SITE_CONTROL_AFFILIATE:
                    $arrSiteControlList[$i]["control_area"]
                            = $masterData->getMasterData("mtb_site_control_affiliate");
                    break;
                default:
                    break;
            }
        }

        $this->arrControlList = $arrSiteControlList;
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

    // サイト管理情報の取得
    function lfGetControlList() {
        $objQuery = new SC_Query();
        // サイト管理情報の取得
        $sql = "SELECT * FROM dtb_site_control ";
        $sql .= "WHERE del_flg = 0";
        $arrRet = $objQuery->getall($sql);
        return $arrRet;
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("設定状況", "control_flg", INT_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
    }

    /* 入力内容のチェック */
    function lfCheckError() {
        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        return $objErr->arrErr;
    }

    /* DBへデータを登録する */
    function lfSiteControlData($control_id = "") {
        $objQuery = new SC_Query();
        $sqlval = $this->objFormParam->getHashArray();
        $sqlval['update_date'] = 'Now()';

        // 新規登録
        if($control_id == "") {
            // INSERTの実行
            //$sqlval['creator_id'] = $_SESSION['member_id'];
            $sqlval['create_date'] = 'Now()';
            $objQuery->insert("dtb_site_control", $sqlval);
        // 既存編集
        } else {
            $where = "control_id = ?";
            $objQuery->update("dtb_site_control", $sqlval, $where, array($control_id));
        }
    }
}
?>
