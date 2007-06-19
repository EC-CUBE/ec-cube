<?php
/**
 * 
 * @copyright    2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version CVS: $Id: 1.0 2006-06-04 06:38:01Z kakinaka $ 
 * @link        http://www.lockon.co.jp/
 *
 */
require_once(MODULE_PATH . "mdl_zero/mdl_zero.inc");

//ページ管理クラス
class LC_Page {
    //コンストラクタ
    function LC_Page() {
        //メインテンプレートの指定
        $this->tpl_mainpage = MODULE_PATH . 'mdl_zero/mdl_zero.tpl';
        $this->tpl_subtitle = 'ゼロ決済モジュール';
    }
}
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// クレジットチェック
lfZeroCheck();

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
        lfUpdPaymentDB();
        
        // javascript実行
        $objPage->tpl_onload = 'alert("登録完了しました。\n基本情報＞支払方法設定より詳細設定をしてください。"); window.close();';
    }
    break;
case 'module_del':
    // 汎用項目の存在チェック
    if(sfColumnExists("dtb_payment", "memo01")){
        // データの削除フラグをたてる
        $objQuery->query("UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ?", array(MDL_ZERO_ID));
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
    $objFormParam->addParam("PC版", "pc", INT_LEN, "n");
    $objFormParam->addParam("加盟店コード", "pc_send", SEND_LEN, "KVa", array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
    $objFormParam->addParam("番組コード ", "pc_clientip", CLIENTIP_LEN, "KVa", array("MAX_LENGTH_CHECK", "NUM_CHECK"));

    $objFormParam->addParam("携帯版", "mobile", INT_LEN, "n");
    $objFormParam->addParam("加盟店コード", "mobile_send", SEND_LEN, "KVa", array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
    $objFormParam->addParam("番組コード ", "mobile_clientip", CLIENTIP_LEN, "KVa", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    
    return $objFormParam;
}

// エラーチェックを行う
function lfCheckError(){
    global $objFormParam;
    
    $arrErr = $objFormParam->checkError();
    
    if($_POST["pc"]){
        if(empty($_POST["pc_send"])) $arrErr["pc_send"] = "※ 加盟店コード が入力されていません。<br>";
        if(empty($_POST["pc_clientip"])) $arrErr["pc_clientip"] = "※ 番組コード が入力されていません。<br>";
    }

    if($_POST["mobile"]){
        if(empty($_POST["mobile_send"])) $arrErr["mobile_send"] = "※ 加盟店コード が入力されていません。<br>";
        if(empty($_POST["mobile_clientip"])) $arrErr["mobile_clientip"] = "※ 番組コード が入力されていません。<br>";
    }
    
    // 接続チェックを行う
    if(count($arrErr) == 0) $arrErr = lfChkConnect();
    
    return $arrErr;
}

// 接続チェックを行う
function lfChkConnect(){
    $arrRet = array();
    
    // PC版の接続確認
    if($_POST["pc"]){
        // 送信データ生成
        $arrSendData = array(
            'clientip' => $_POST["pc_clientip"],    // 番組コード
            'custom' => SEND_PARAM_CUSTOM,            // yes固定
            'send' => $_POST["pc_send"],            // 加盟店コード
            'money' => 0                            // 金額
        );
        
        // データ送信
        $arrResponse = sfPostPaymentData(SEND_PARAM_PC_URL, $arrSendData, false);
        
        // エラーがあるかチェックする
        if(!ereg("^<HTML>",$arrResponse )){
            $arrRet["pc_clientip"] = "入力データが正しくありません<br>";
        }
    }

    // 携帯版の接続確認
    if($_POST["mobile"]){
        // 送信データ生成
        $arrSendData = array(
            'clientip' => $_POST["mobile_clientip"],    // 番組コード
            'act' => SEND_PARAM_ACT,                    // imode固定
            'money' => 0                                // 金額
        );
         
        // データ送信
        $arrResponse = sfPostPaymentData(SEND_PARAM_MOBILE_URL, $arrSendData, false);
        
        // エラーがあるかチェックする
        if(!ereg("^<HTML>",$arrResponse )){
            $arrRet["mobile_clientip"] = "入力データが正しくありません<br>";
        }
    }
    
    return $arrRet;    
}

// 登録データを読み込む
function lfLoadData(){
    global $objFormParam;
    
    //データを取得
    $arrRet = lfGetPaymentDB(" AND del_flg = '0'");
    
    // 値をセット
    $objFormParam->setParam($arrRet[0]);

    // 画面表示用にデータを変換
    $arrDisp = array();
    $arrDisp = $arrRet[0];
    if (!empty($arrDisp["pc_send"])) $arrDisp["pc"] = 1;
    if (!empty($arrDisp["mobile_send"])) $arrDisp["mobile"] = 1;
    $objFormParam->setParam($arrDisp);
}

// DBからデータを取得する
function lfGetPaymentDB($where = "", $arrWhereVal = array()){
    global $objQuery;
    
    $arrVal = array(MDL_ZERO_ID);
    $arrVal = array_merge($arrVal, $arrWhereVal);
    
    $arrRet = array();
    $sql = "SELECT 
                module_id, 
                memo01 as pc_send, 
                memo02 as pc_clientip,
                memo04 as mobile_send, 
                memo05 as mobile_clientip
            FROM dtb_payment WHERE module_id = ? " . $where;
    $arrRet = $objQuery->getall($sql, $arrVal);

    return $arrRet;
}


// データの更新処理
function lfUpdPaymentDB(){
    global $objQuery;
    global $objSess;
    $arrData = array();

    // del_flgを削除にしておく
    $del_sql = "UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ? ";
    $arrDel = array(MDL_ZERO_ID);
    $objQuery->query($del_sql, $arrDel);

    // PC用データ登録
    if($_POST["pc"]){
		$arrData["payment_method"] = "Zeroクレジット";
		$arrData["fix"] = 3;
		$arrData["creator_id"] = $objSess->member_id;
		$arrData["update_date"] = "now()";
		$arrData["module_id"] = MDL_ZERO_ID;
		$arrData["module_path"] = MODULE_PATH . "mdl_zero/card.php";
		$arrData["memo01"] = $_POST["pc_send"];
		$arrData["memo02"] = $_POST["pc_clientip"];
		$arrData["memo03"] = ZERO_CREDIT_ID;
		$arrData["del_flg"] = "0";
    }
    
    // 携帯用データ登録
    if($_POST["mobile"]){
		$arrData["payment_method"] = "Zeroクレジット";
		$arrData["fix"] = 3;
		$arrData["creator_id"] = $objSess->member_id;
		$arrData["update_date"] = "now()";
		$arrData["module_id"] = MDL_ZERO_ID;
		$arrData["module_path"] = MODULE_PATH . "mdl_zero/card.php";
		$arrData["memo03"] = ZERO_CREDIT_ID;
		$arrData["memo04"] = $_POST["mobile_send"];
		$arrData["memo05"] = $_POST["mobile_clientip"];
		$arrData["del_flg"] = "0";
    }
    
    // 更新データがあれば更新する。
    if(count($arrData) > 0){
	    // ランクの最大値を取得する
	    $max_rank = $objQuery->getone("SELECT max(rank) FROM dtb_payment");
	    
	    // 支払方法データを取得
	    $arrPaymentData = lfGetPaymentDB();
	    
	    // データが存在していればUPDATE、無ければINSERT
	    if(count($arrPaymentData) > 0){
            $objQuery->update("dtb_payment", array("memo01"=>"","memo02"=>"","memo03"=>"","memo04"=>"","memo05"=>""), " module_id = '" . MDL_ZERO_ID . "'");
	        $objQuery->update("dtb_payment", $arrData, " module_id = '" . MDL_ZERO_ID . "'");
	    }else{
	        $arrData["rank"] = $max_rank + 1;
	        $objQuery->insert("dtb_payment", $arrData);
	    }
    }
}


function lfZeroCheck(){
    if(!empty($_GET["clientip"])){
        global $objPage;
        global $objView;
        global $objQuery;
        require_once(MODULE_PATH . "mdl_zero/recv.php");
        exit();
    }
}


?>