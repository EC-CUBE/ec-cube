<?php
/**
 * @copyright    2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @link        http://www.lockon.co.jp/
 *
 * モジュールバージョン表記
 * @version ### ### 1.2
 *
 */
require_once(MODULE_PATH . "mdl_gmo-pg/mdl_gmo-pg.inc");

//ページ管理クラス
class LC_Page {
    //コンストラクタ
    function LC_Page() {
        //メインテンプレートの指定
        $this->tpl_mainpage = MODULE_PATH . 'mdl_gmo-pg/mdl_gmo-pg.tpl';
        $this->tpl_subtitle = 'GMOペイメントゲートウェイ決済モジュール';
    }
}
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// 認証確認
$objSess = new SC_Session();
sfIsSuccess($objSess);

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
$objFormParam = lfInitParam($objFormParam);
// POST値の取得
$objFormParam->setParam($_POST);

// 汎用項目を追加(必須！！)
sfAlterMemo();

switch($_POST['mode']) {
case 'edit':
    // 入力エラー判定
    $objPage->arrErr = lfCheckError();

    // エラーなしの場合にはデータを更新
    if(count($objPage->arrErr) == 0) {
        // データ更新
        lfUpdPaymentDB($objFormParam->getHashArray());

        // javascript実行
        $objPage->tpl_onload = 'alert("登録完了しました。\n基本情報＞支払方法設定より詳細設定をしてください。"); window.close();';
    }
    break;
case 'module_del':
    // 汎用項目の存在チェック
    if(sfColumnExists("dtb_payment", "memo01")){
        // データの削除フラグをたてる
        $objQuery->query("UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ?", array(MDL_GMOPG_ID));
    }
    break;
default:
    // データのロード
    lfLoadData();
    break;
}

$objPage->arrForm = $objFormParam->getFormParamList();

$objView->assignobj($objPage);                    //変数をテンプレートにアサインする
$objView->display($objPage->tpl_mainpage);        //テンプレートの出力
//-------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam($objFormParam) {
	/* 半角英数字13 桁以内 */
    $objFormParam->addParam("ショップIP", "gmo_shopid", 13, "KVa", array("EXIST_CHECK", "ALNUM_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
	/* 半角英数字10 桁以内 */
    $objFormParam->addParam("ショップパスワード ", "gmo_shoppass", 10, "KVa", array("EXIST_CHECK", "ALNUM_CHECK",  "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
	/* 半角英数字10 桁以内 */
    $objFormParam->addParam("店舗管理番号", "gmo_tenantno", 10, "KVa", array("EXIST_CHECK", "ALNUM_CHECK",  "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
    $objFormParam->addParam("3Dセキュア認証", "gmo_3d", 1, "a", array("NUM_CHECK",  "MAX_LENGTH_CHECK"));
    return $objFormParam;
}

// エラーチェックを行う
function lfCheckError(){
    global $objFormParam;

    $arrErr = $objFormParam->checkError();

    return $arrErr;
}

// 登録データを読み込む
function lfLoadData(){
    global $objFormParam;

    //データを取得
    $arrRet = sfGetPaymentDB("AND del_flg = '0'");

    // 値をセット
    $objFormParam->setParam($arrRet[0]);

    // 画面表示用にデータを変換
    $arrDisp = array();
    $arrDisp = $arrRet[0];
    if (!empty($arrDisp["pc_send"])) $arrDisp["pc"] = 1;
    if (!empty($arrDisp["mobile_send"])) $arrDisp["mobile"] = 1;
    $objFormParam->setParam($arrDisp);
}

// データの更新処理
function lfUpdPaymentDB($arrPostData){
    $objQuery = new SC_Query;
    $objSess  = new SC_Session;

    // del_flgを削除にしておく
    // XXX
    $del_sql = "UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ? ";
    $objQuery->query($del_sql, array(MDL_GMOPG_ID));

    // データ登録
    $arrData = array();
    $arrData["payment_method"] = "ペイメントゲートウェイクレジット";
	$arrData["fix"] = 3;
	$arrData["module_id"] = MDL_GMOPG_ID;
	$arrData["module_path"] = MODULE_PATH . "mdl_gmo-pg/gmo-pg_credit.php";
	$arrData["memo01"] = $arrPostData["gmo_shopid"];
	$arrData["memo02"] = $arrPostData["gmo_shoppass"];
	$arrData["memo03"] = $arrPostData["gmo_tenantno"];
	$arrData["memo04"] = isset($arrPostData["gmo_3d"]) ? '1' : '0';
	$arrData["memo05"] = "";
	$arrData["del_flg"] = "0";
	$arrData["creator_id"] = $objSess->member_id;
	$arrData["update_date"] = "now()";

    // ランクの最大値を取得する
    $max_rank = $objQuery->getone("SELECT max(rank) FROM dtb_payment");
	$arrData['rank'] = $max_rank;

    // 支払方法データを取得
    // XXX
    $arrPaymentData = sfGetPaymentDB();
    // データが存在していればUPDATE、無ければINSERT
    if(!empty($arrPaymentData)){
        $objQuery->update("dtb_payment", $arrData, "module_id = ?", array(MDL_GMOPG_ID));
    }else{
	    $objQuery->insert("dtb_payment", $arrData);
    }
}
?>
