<?php
/**
 * 
 * @copyright    2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version CVS: $Id: 1.0 2006-06-04 06:38:01Z kakinaka $ 
 * @link        http://www.lockon.co.jp/
 *
 */
require_once(MODULE_PATH . "mdl_movilink/mdl_movilink.inc");

//ページ管理クラス
class LC_Page {
    //コンストラクタ
    function LC_Page() {
        //メインテンプレートの指定
        $this->tpl_mainpage = MODULE_PATH . 'mdl_movilink/mdl_movilink.tpl';
        $this->tpl_subtitle = MODULE_NAME;
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
        sfSetModuleDB();
        // カラムの生成
        sfMakeMoviLinkColumn();
        // CSVレコードの生成
        sfSetMoviLinkCSV();
        // javascript実行
        $objPage->tpl_onload = 'alert("登録完了しました。"); window.close();';
    }
    break;
case 'module_del':
    // 汎用項目の存在チェック
    if(sfColumnExists("dtb_payment", "memo01")){
        // データの削除フラグをたてる
        $objQuery->query("UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ?", array(MDL_MOVILINK_ID));
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
    $objFormParam->addParam("ECサイトID", "site_id", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));	
	$objFormParam->addParam("ステータス", "status", 1, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
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
    $arrRet = sfGetModuleDB();
    // 値をセット
    $objFormParam->setParam($arrRet);
}
?>