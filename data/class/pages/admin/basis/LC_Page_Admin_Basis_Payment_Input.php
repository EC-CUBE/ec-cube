<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 支払方法設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Payment_Input extends LC_Page {

    // {{{ properties

    /** フォームパラメータの配列 */
    var $objFormParam;

    /** SC_UploadFile インスタンス */
    var $objUpFile;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/payment_input.tpl';
        $this->tpl_subtitle = '支払方法設定';
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
        $objDb = new SC_Helper_DB_Ex();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // ファイル管理クラス
        $this->objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
        // ファイル情報の初期化
        $this->objUpFile = $this->lfInitFile();
        // Hiddenからのデータを引き継ぐ
        $this->objUpFile->setHiddenFileList($_POST);

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        switch($_POST['mode']) {
        case 'edit':
            // 入力値の変換
            $this->objFormParam->convParam();

            // エラーチェック
            $this->arrErr = $this->lfCheckError();
            $this->charge_flg = $_POST["charge_flg"];
            if(count($this->arrErr) == 0) {
                $this->lfRegistData($_POST['payment_id']);
                // 一時ファイルを本番ディレクトリに移動する
                $this->objUpFile->moveTempFile();
                // 親ウィンドウを更新するようにセットする。
                $this->tpl_onload="fnUpdateParent('".URL_PAYMENT_TOP."'); window.close();";
            }

            break;
        // 画像のアップロード
        case 'upload_image':
            // ファイル存在チェック
            $this->arrErr = array_merge($this->arrErr, $this->objUpFile->checkEXISTS($_POST['image_key']));
            // 画像保存処理
            $this->arrErr[$_POST['image_key']] = $this->objUpFile->makeTempFile($_POST['image_key']);
            break;
        // 画像の削除
        case 'delete_image':
            $this->objUpFile->deleteFile($_POST['image_key']);
            break;
        default:
            break;
        }

        if($_POST['mode'] == "") {
            switch($_GET['mode']) {
            case 'pre_edit':
                if(SC_Utils_Ex::sfIsInt($_GET['payment_id'])) {
                    $arrRet = $this->lfGetData($_GET['payment_id']);
                    $this->objFormParam->setParam($arrRet);
                    $this->charge_flg = $arrRet["charge_flg"];
                    // DBデータから画像ファイル名の読込
                    $this->objUpFile->setDBFileList($arrRet);
                    $this->tpl_payment_id = $_GET['payment_id'];
                }
                break;
            default:
                break;
            }
        } else {
            $this->tpl_payment_id = $_POST['payment_id'];
        }

        $this->arrDelivList = $objDb->sfGetIDValueList("dtb_deliv", "deliv_id", "service_name");
        $this->arrForm = $this->objFormParam->getFormParamList();

        // FORM表示用配列を渡す。
        $this->arrFile = $this->objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
        // HIDDEN用に配列を渡す。
        $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objUpFile->getHiddenFileList());

        $objView->assignobj($this);
        $objView->display($this->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* ファイル情報の初期化 */
    function lfInitFile() {
        $this->objUpFile->addFile("ロゴ画像", 'payment_image', array('gif'), IMAGE_SIZE, false, CLASS_IMAGE_WIDTH, CLASS_IMAGE_HEIGHT);
        return $this->objUpFile;
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("支払方法", "payment_method", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("手数料", "charge", PRICE_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("利用条件(〜円以上)", "rule", PRICE_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("利用条件(〜円以下)", "upper_rule", PRICE_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("配送サービス", "deliv_id", INT_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("固定", "fix");
    }

    /* DBからデータを読み込む */
    function lfGetData($payment_id) {
        $objQuery = new SC_Query();
        $where = "payment_id = ?";
        $arrRet = $objQuery->select("*", "dtb_payment", $where, array($payment_id));
        return $arrRet[0];
    }

    /* DBへデータを登録する */
    function lfRegistData($payment_id = "") {

        $objQuery = new SC_Query();
        $sqlval = $this->objFormParam->getHashArray();
        $arrRet = $this->objUpFile->getDBFileList();	// ファイル名の取得
        $sqlval = array_merge($sqlval, $arrRet);
        $sqlval['update_date'] = 'Now()';

        if($sqlval['fix'] != '1') {
            $sqlval['fix'] = 2;	// 自由設定
        }

        // 新規登録
        if($payment_id == "") {
            // INSERTの実行
            $sqlval['creator_id'] = $_SESSION['member_id'];
            $sqlval['rank'] = $objQuery->max("dtb_payment", "rank") + 1;
            $sqlval['create_date'] = 'Now()';
            $objQuery->insert("dtb_payment", $sqlval);
        // 既存編集
        } else {
            $where = "payment_id = ?";
            $objQuery->update("dtb_payment", $sqlval, $where, array($payment_id));
        }
    }

    /*　利用条件の数値チェック */

    /* 入力内容のチェック */
    function lfCheckError() {

        // DBのデータを取得
        $arrPaymentData = $this->lfGetData($_POST['payment_id']);

        // 手数料を設定できない場合には、手数料を0にする
        if($arrPaymentData["charge_flg"] == 2) $this->objFormParam->setValue("charge", "0");

        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        // 利用条件(下限)チェック
        if($arrRet["rule"] < $arrPaymentData["rule_min"] and $arrPaymentData["rule_min"] != ""){
            $objErr->arrErr["rule"] = "利用条件(下限)は" . $arrPaymentData["rule_min"] ."円以上にしてください。<br>";
        }

        // 利用条件(上限)チェック
        if($arrRet["upper_rule"] > $arrPaymentData["upper_rule_max"] and $arrPaymentData["upper_rule_max"] != ""){
            $objErr->arrErr["upper_rule"] = "利用条件(上限)は" . $arrPaymentData["upper_rule_max"] ."円以下にしてください。<br>";
        }

        // 利用条件チェック
        $objErr->doFunc(array("利用条件(〜円以上)", "利用条件(〜円以下)", "rule", "upper_rule"), array("GREATER_CHECK"));

        return $objErr->arrErr;
    }
}
?>
