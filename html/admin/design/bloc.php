<?php

require_once("../../require.php");

class LC_Page {
	var $arrForm;
	var $arrHidden;

	function LC_Page() {
		$this->tpl_mainpage = 'design/bloc.tpl';
		$this->tpl_subnavi = 'design/subnavi.tpl';
		$this->tpl_subno_edit = 'bloc';
		$this->text_row = 13;
		$this->tpl_subno = "bloc";	
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = 'ブロック編集';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// ブロック一覧を取得
$objPage->arrBlocList = lfgetBlocData();

// ブロックIDを取得
if (isset($_POST['bloc_id'])) {
	$bloc_id = $_POST['bloc_id'];
}else if ($_GET['bloc_id']){
	$bloc_id = $_GET['bloc_id'];
}else{
	$bloc_id = '';
}
$objPage->bloc_id = $bloc_id;

// bloc_id が指定されている場合にはブロックデータの取得
if ($bloc_id != '') {
	$arrBlocData = lfgetBlocData(" bloc_id = ? " , array($bloc_id));
	$arrBlocData[0]['tpl_path'] = ROOT_DIR . $arrBlocData[0]['tpl_path'];

	// テンプレートファイルの読み込み
	$arrBlocData[0]['tpl_data'] = file_get_contents($arrBlocData[0]['tpl_path']);
	$objPage->arrBlocData = $arrBlocData[0];
}

// データ登録処理
if ($_POST['mode'] == 'confirm') {
	
	// エラーチェック
	$objPage->arrErr = lfErrorCheck($_POST);

	// エラーがなければ更新処理を行う	
	if (count($objPage->arrErr) == 0) {
	
		
		// DBへデータを更新する
		lfEntryBlocData($_POST);
		
		// ファイルの削除
		$del_file=ROOT_DIR . BLOC_DIR . $arrBlocData[0]['filename']. '.tpl';
		if (file_exists($del_file)) {
			unlink($del_file);
		}
		
		// ファイル作成
		$fp = fopen(ROOT_DIR . BLOC_DIR . $_POST['filename'] . '.tpl',"w");
		fwrite($fp, $_POST['bloc_html']);
		fclose($fp);
		
		$arrBlocData = lfgetBlocData(" filename = ? " , array($_POST['filename']));
			
		$bloc_id = $arrBlocData[0]['bloc_id'];	
		header("location: ./bloc.php?bloc_id=$bloc_id");
	}else{
		// エラーがあれば入力時のデータを表示する
		$objPage->arrBlocData = $_POST;
	}
}

// データ削除処理
if ($_POST['mode'] == 'delete') {
	
	// DBへデータを更新する
	$objDBConn = new SC_DbConn;		// DB操作オブジェクト
	$sql = "";						// データ更新SQL生成用
	$ret = ""; 						// データ更新結果格納用
	$arrDelData = array();			// 更新データ生成用
	
	// 更新データ生成
	$arrUpdData = array($arrData['bloc_name'], BLOC_DIR . $arrData['filename'] . '.tpl', $arrData['filename']);
	
	// bloc_id が空でない場合にはdeleteを実行
	if ($_POST['bloc_id'] !== '') {
		// SQL生成
		$sql = " DELETE FROM dtb_bloc WHERE bloc_id = ?";
		// SQL実行
		$ret = $objDBConn->query($sql,array($_POST['bloc_id']));
		
		// ページに配置されているデータも削除する
		$sql = "DELETE FROM dtb_blocposition WHERE bloc_id = ?";
		// SQL実行
		$ret = $objDBConn->query($sql,array($_POST['bloc_id']));
	
		// ファイルの削除
		$del_file = ROOT_DIR . BLOC_DIR . $arrBlocData[0]['filename']. '.tpl';
		if(file_exists($del_file)){
			unlink($del_file);
		}
	}

	header("location: ./bloc.php");
}


// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

/**************************************************************************************************************
 * 関数名	：lfgetBlocData
 * 処理内容	：ブロック情報を取得する
 * 引数1	：$where  ･･･ Where句文
 * 引数2	：$arrVal ･･･ Where句の絞込条件値
 * 戻り値	：ブロック情報
 **************************************************************************************************************/
function lfgetBlocData($where = '', $arrVal = ''){
	$objDBConn = new SC_DbConn;		// DB操作オブジェクト
	$sql = "";						// データ取得SQL生成用
	$arrRet = array();				// データ取得用
	
	// SQL生成
	$sql = " SELECT ";
	$sql .= "	bloc_id";
	$sql .= "	,bloc_name";
	$sql .= "	,tpl_path";
	$sql .= "	,filename";
	$sql .= " 	,create_date";
	$sql .= " 	,update_date";
	$sql .= " 	,php_path";
	$sql .= " FROM ";
	$sql .= " 	dtb_bloc";

	// where句の指定があれば追加	
	if ($where != '') {
		$sql .= " WHERE " . $where;
	}
	
	$sql .= " ORDER BY 	bloc_id";
	
	$arrRet = $objDBConn->getAll($sql, $arrVal);
	
	return $arrRet;
}

/**************************************************************************************************************
 * 関数名	：lfEntryBlocData
 * 処理内容	：ブロック情報を更新する
 * 引数1	：$arrData  ･･･ 更新データ
 * 戻り値	：更新結果
 **************************************************************************************************************/
function lfEntryBlocData($arrData){
	$objDBConn = new SC_DbConn;		// DB操作オブジェクト
	$sql = "";						// データ更新SQL生成用
	$ret = ""; 						// データ更新結果格納用
	$arrUpdData = array();			// 更新データ生成用
	$arrChk = array();				// 排他チェック用
	
	// 更新データ生成
	$arrUpdData = array($arrData['bloc_name'], BLOC_DIR . $arrData['filename'] . '.tpl', $arrData['filename']);
	
	// データが存在しているかチェックを行う
	if($arrData['bloc_id'] !== ''){
		$arrChk = lfgetBlocData("bloc_id = ?", array($arrData['bloc_id']));
	}
	
	// bloc_id が空 若しくは データが存在していない場合にはINSERTを行う
	if ($arrData['bloc_id'] === '' or !isset($arrChk[0])) {
		// SQL生成
		$sql = " INSERT INTO dtb_bloc";
		$sql .= " ( ";
		$sql .= "     bloc_name ";		// ブロック名称
		$sql .= "     ,tpl_path ";		// テンプレート保存先
		$sql .= "     ,filename ";		// ファイル名称
		$sql .= " ) VALUES ( ?,?,? )";
		$sql .= " ";
	}else{
		// データが存在してる場合にはアップデートを行う
		// SQL生成
		$sql = " UPDATE dtb_bloc";
		$sql .= " SET";
		$sql .= "     bloc_name = ? ";	// ブロック名称
		$sql .= "     ,tpl_path = ? ";	// テンプレート保存先
		$sql .= "     ,filename = ? ";	// テンプレートファイル名
		$sql .= "     ,update_date = now()";
		$sql .= " WHERE bloc_id = ?";
		$sql .= " ";
		
		// 更新データにブロックIDを追加
		array_push($arrUpdData, $arrData['bloc_id']);
	}
	
	// SQL実行
	$ret = $objDBConn->query($sql,$arrUpdData);
	
	return $ret;

}

/**************************************************************************************************************
 * 関数名	：lfErrorCheck
 * 処理内容	：入力項目のエラーチェックを行う
 * 引数1	：$arrData  ･･･ 入力データ
 * 戻り値	：エラー情報
 **************************************************************************************************************/
function lfErrorCheck($array) {
	global $objPage;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("ブロック名", "bloc_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK" ,"ALNUM_CHECK"));
	$objErr->doFunc(array("ファイル名", "filename", STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK"));
	
	// 同一のファイル名が存在している場合にはエラー
	if(!isset($objErr->arrErr['filename']) and $array['filename'] !== ''){
		$arrChk = lfgetBlocData("filename = ?", array($array['filename']));
		
		if (count($arrChk[0]) >= 1 and $arrChk[0]['bloc_id'] != $array['bloc_id']) {
			$objErr->arrErr['filename'] = '※ 同じファイル名のデータが存在しています。別の名称を付けてください。';
		}
	}
	
	return $objErr->arrErr;
}
