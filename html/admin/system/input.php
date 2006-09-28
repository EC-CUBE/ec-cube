<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrErr;		// エラーメッセージ出力用
	var $tpl_recv;		// 入力情報POST先
	var $tpl_onload;	// ページ読み込み時のイベント
	var $arrForm;		// フォーム出力用
	var $tpl_mode;		// 新規作成:new or 編集:edit
	var $tpl_member_id; // 編集時に使用する。
	var $tpl_pageno;
	var $tpl_onfocus;	// パスワード項目選択時のイベント用
	var $tpl_old_login_id;
	function LC_Page() {
		$this->tpl_recv =  'input.php';
		$this->tpl_pageno = $_REQUEST['pageno'];
		$this->SHORTTEXT_MAX = STEXT_LEN;
		$this->MIDDLETEXT_MAX = MTEXT_LEN;
		$this->LONGTEXT_MAX = LTEXT_LEN;
		global $arrAUTHORITY;
		$this->arrAUTHORITY = $arrAUTHORITY;
	}
}

$conn = new SC_DbConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// member_idが指定されていた場合、編集モードとする。
if(sfIsInt($_GET['id'])) {
	$objPage->tpl_mode = 'edit';
	$objPage->tpl_member_id = $_GET['id'];
	$objPage->tpl_onfocus = "fnClearText(this.name);";
	// DBのメンバー情報を読み出す
	$data_list = fnGetMember($conn, $_GET['id']);
	// 該当ユーザを表示させる
	$objPage->arrForm = $data_list[0];
	// ダミーのパスワードをセットしておく。
	$objPage->arrForm['password'] = DUMMY_PASS;
	// ログインIDを保管しておく。
	$objPage->tpl_old_login_id = $data_list[0]['login_id'];
} else {
	// 新規作成モード
	$objPage->tpl_mode = "new";
	$objPage->arrForm['authority'] = -1;
}

// 新規作成モード or 編集モード
if( $_POST['mode'] == 'new' || $_POST['mode'] == 'edit') {
	// 入力エラーチェック
	$objPage->arrErr = fnErrorCheck($conn);
	
	// 入力が正常であった場合は、DBに書き込む
	if(count($objPage->arrErr) == 0) {
		if($_POST['mode'] == 'new') {
			// メンバーの追加
			fnInsertMember();
			// リロードによる二重登録対策のため、同じページに飛ばす。
			header("Location: ". $_SERVER['PHP_SELF'] . "?mode=reload");	
			exit;
		}
		if($_POST['mode'] == 'edit') {
			// メンバーの追加
			if(fnUpdateMember($_POST['member_id'])) {
				// 親ウィンドウを更新後、自ウィンドウを閉じる。
				$url = URL_SYSTEM_TOP . "?pageno=".$_POST['pageno'];
				$objPage->tpl_onload="fnUpdateParent('".$url."'); window.close();";
			}
		}
	// 入力エラーが発生した場合
	} else {
		// モードの設定
		$objPage->tpl_mode = $_POST['mode'];
		$objPage->tpl_member_id = $_POST['member_id'];
		$objPage->tpl_old_login_id = $_POST['old_login_id'];
		// すでに入力した値を表示する。
		$objPage->arrForm = $_POST;
		// 通常入力のパスワードは引き継がない。
		if($objPage->arrForm['password'] != DUMMY_PASS) {
			$objPage->arrForm['password'] = '';
		}
	}
}

// リロードの指定があった場合
if( $_GET['mode'] == 'reload') {
	// 親ウィンドウを更新するようにセットする。
	$url = URL_SYSTEM_TOP;
	$objPage->tpl_onload="fnUpdateParent('".$url."')";
}

// テンプレート用変数の割り当て
$objView->assignobj($objPage);
$objView->display('system/input.tpl');

/* 入力エラーのチェック */
function fnErrorCheck($conn) {
	
	$objErr = new SC_CheckError();
	
	$_POST["name"] = mb_convert_kana($_POST["name"] ,"KV");
	$_POST["department"] = mb_convert_kana($_POST["department"] ,"KV");
	
	// 名前チェック
	$objErr->doFunc(array("名前",'name'), array("EXIST_CHECK"));
	$objErr->doFunc(array("名前",'name',STEXT_LEN,"BIG"), array("MAX_LENGTH_CHECK"));
	
	// 編集モードでない場合は、重複チェック
	if (!isset($objErr->arrErr['name']) && $_POST['mode'] != 'edit') {
		$sql = "SELECT name FROM dtb_member WHERE del_flg <> 1 AND name = ?";
		$result = $conn->getOne($sql, array($_POST['name'])); 
		if ( $result ) {
			$objErr->arrErr['name'] = "既に登録されている名前なので利用できません。<br>";
		}
	}
		
	// ログインIDチェック
	$objErr->doFunc(array("ログインID",'login_id'), array("EXIST_CHECK", "ALNUM_CHECK"));
	$objErr->doFunc(array("ログインID",'login_id',ID_MIN_LEN , ID_MAX_LEN) ,array("NUM_RANGE_CHECK"));
	
	// 新規モードもしくは、編集モードでログインIDが変更されている場合はチェックする。
	if (!isset($objErr->arrErr['login_id']) && $_POST['mode'] != 'edit' || ($_POST['mode'] == 'edit' && $_POST['login_id'] != $_POST['old_login_id'])) {
		$sql = "SELECT login_id FROM dtb_member WHERE del_flg <> 1 AND login_id = ?";
		$result = $conn->getOne($sql, array($_POST['login_id'])); 
		if ( $result != "" ) {
			$objErr->arrErr['login_id'] = "既に登録されているIDなので利用できません。<br>";
		}
	}
	
	// パスワードチェック(編集モードでDUMMY_PASSが入力されている場合は、スルーする)
	if(!($_POST['mode'] == 'edit' && $_POST['password'] == DUMMY_PASS)) { 
		$objErr->doFunc(array("パスワード",'password'), array("EXIST_CHECK", "ALNUM_CHECK"));
		if (!$arrErr['password']) {
			// パスワードのチェック
			$objErr->doFunc( array("パスワード",'password',4 ,15 ) ,array( "NUM_RANGE_CHECK" ) );	
		}
	}
	
	// 権限チェック
	$objErr->doFunc(array("権限",'authority'),array("EXIST_CHECK"));
	return $objErr->arrErr;
}

/* DBへのデータ挿入 */
function fnInsertMember() {
	// クエリークラスの宣言
	$oquery = new SC_Query();
	// INSERTする値を作成する。
	$sqlval['name'] = $_POST['name'];
	$sqlval['department'] = $_POST['department'];
	$sqlval['login_id'] = $_POST['login_id'];
	$sqlval['password'] = sha1($_POST['password'] . ":" . AUTH_MAGIC);
	$sqlval['authority'] = $_POST['authority'];
	$sqlval['rank']=  $oquery->max("dtb_member", "rank") + 1;
	$sqlval['work'] = "1"; // 稼働に設定
	$sqlval['del_flg'] = "0";	// 削除フラグをOFFに設定
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['create_date'] = "now()";
	$sqlval['update_date'] = "now()";
	// INSERTの実行
	$ret = $oquery->insert("dtb_member", $sqlval);
	return $ret;
}

/* DBへのデータ更新 */
function fnUpdateMember($id) {
	// クエリークラスの宣言
	$oquery = new SC_Query();
	// INSERTする値を作成する。
	$sqlval['name'] = $_POST['name'];
	$sqlval['department'] = $_POST['department'];
	$sqlval['login_id'] = $_POST['login_id'];
	if($_POST['password'] != DUMMY_PASS) {
		$sqlval['password'] = sha1($_POST['password'] . ":" . AUTH_MAGIC);
	}
	$sqlval['authority'] = $_POST['authority'];
	$sqlval['update_date'] = "now()";
	// UPDATEの実行
	$where = "member_id = " . $id;
	$ret = $oquery->update("dtb_member", $sqlval, $where);
	return $ret;
}

/* DBからデータの読み込み */
function fnGetMember($conn, $id) {
	$sqlse = "SELECT name,department,login_id,authority FROM dtb_member WHERE member_id = ?";
	$ret = $conn->getAll($sqlse, Array($id));
	return $ret;
}
?>