<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 配送業者設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Delivery_Input extends LC_Page {

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
        $this->tpl_mainpage = 'basis/delivery_input.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_subno = 'delivery';
        $this->tpl_mainno = 'basis';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));
        $this->tpl_subtitle = '配送業者設定';
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

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'edit':
            // 入力値の変換
            $this->objFormParam->convParam();
            $this->arrErr = $this->lfCheckError();
            if(count($this->arrErr) == 0) {
                $this->tpl_deliv_id = $this->lfRegistData();
                $this->tpl_onload = "window.alert('配送業者設定が完了しました。');";
            }
            break;
        case 'pre_edit':
            if($_POST['deliv_id'] != "") {
                $this->lfGetDelivData($_POST['deliv_id']);
                $this->tpl_deliv_id = $_POST['deliv_id'];
            }
            break;
        default:
            break;
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
        $this->objFormParam->addParam("配送業者名", "name", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("名称", "service_name", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("伝票No.確認URL", "confirm_url", STEXT_LEN, "n", array("URL_CHECK", "MAX_LENGTH_CHECK"), "http://");

        for($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
            $this->objFormParam->addParam("配送時間$cnt", "deliv_time$cnt", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        }

        if(INPUT_DELIV_FEE) {
            for($cnt = 1; $cnt <= DELIVFEE_MAX; $cnt++) {
                $this->objFormParam->addParam("配送料金$cnt", "fee$cnt", PRICE_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
            }
        }
    }

    /* DBに登録する */
    function lfRegistData() {
        $arrRet = $this->objFormParam->getHashArray();
        $objQuery = new SC_Query();
        $objQuery->begin();

        // 入力データを渡す。
        $sqlval['name'] = $arrRet['name'];
        $sqlval['service_name'] = $arrRet['service_name'];
        $sqlval['confirm_url'] = $arrRet['confirm_url'];
        $sqlval['creator_id'] = $_SESSION['member_id'];
        $sqlval['update_date'] = 'Now()';

        if($_POST['deliv_id'] != "") {
            $deliv_id = $_POST['deliv_id'];
            $where = "deliv_id = ?";
            $objQuery->update("dtb_deliv", $sqlval, $where, array($deliv_id));
            $objQuery->delete("dtb_delivfee", $where, array($deliv_id));
            $objQuery->delete("dtb_delivtime", $where, array($deliv_id));
        } else {
            // 登録する配送業者IDの取得

            if (DB_TYPE == "pgsql") {
                $deliv_id = $objQuery->nextval('dtb_deliv', 'deliv_id');
                $sqlval['deliv_id'] = $deliv_id;
            }

            $sqlval['rank'] = $objQuery->max("dtb_deliv", "rank") + 1;
            $sqlval['create_date'] = 'Now()';
            // INSERTの実行
            $objQuery->insert("dtb_deliv", $sqlval);

            if (DB_TYPE == "mysql") {
                $deliv_id = $objQuery->nextval('dtb_deliv', 'deliv_id');
            }
        }

        $sqlval = array();
        // 配送時間の設定
        for($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
            $keyname = "deliv_time$cnt";
            if($arrRet[$keyname] != "") {
                $sqlval['deliv_id'] = $deliv_id;
                $sqlval['deliv_time'] = $arrRet[$keyname];
                // INSERTの実行
                $objQuery->insert("dtb_delivtime", $sqlval);
            }
        }

        if(INPUT_DELIV_FEE) {
            $sqlval = array();
            // 配送料金の設定
            for($cnt = 1; $cnt <= DELIVFEE_MAX; $cnt++) {
                $keyname = "fee$cnt";
                if($arrRet[$keyname] != "") {
                    $sqlval['deliv_id'] = $deliv_id;
                    $sqlval['fee'] = $arrRet[$keyname];
                    $sqlval['pref'] = $cnt;
                    // INSERTの実行
                    $objQuery->insert("dtb_delivfee", $sqlval);
                }
            }
        }
        $objQuery->commit();
        return $deliv_id;
    }

    /* 配送業者情報の取得 */
    function lfGetDelivData($deliv_id) {
        $objQuery = new SC_Query();
        // 配送業者一覧の取得
        $col = "deliv_id, name, service_name, confirm_url";
        $where = "deliv_id = ?";
        $table = "dtb_deliv";
        $arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
        $this->objFormParam->setParam($arrRet[0]);
        // 配送時間の取得
        $col = "deliv_time";
        $where = "deliv_id = ?  ORDER BY time_id";
        $table = "dtb_delivtime";
        $arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
        $this->objFormParam->setParamList($arrRet, 'deliv_time');
        // 配送料金の取得
        $col = "fee";
        $where = "deliv_id = ? ORDER BY pref";
        $table = "dtb_delivfee";
        $arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
        $this->objFormParam->setParamList($arrRet, 'fee');
    }

    /* 入力内容のチェック */
    function lfCheckError() {
        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        if(!isset($objErr->arrErr['name']) && $_POST['deliv_id'] == "") {
            // 既存チェック
            $objDb = new SC_Helper_DB_Ex();
            $ret = $objDb->sfIsRecord("dtb_deliv", "service_name", array($arrRet['service_name']));
            if ($ret) {
                $objErr->arrErr['name'] = "※ 同じ名称の組み合わせは登録できません。<br>";
            }
        }

        return $objErr->arrErr;
    }
}
?>
