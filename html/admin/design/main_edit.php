<?php

require_once("../../require.php");
require_once(ROOT_DIR."data/include/page_layout.inc");

class LC_Page {
	var $arrForm;
	var $arrHidden;

	function LC_Page() {
		$this->tpl_mainpage = 'design/main_edit.tpl';
		$this->tpl_subnavi 	= 'design/subnavi.tpl';
		$this->user_URL	 	= USER_URL;
		$this->text_row 	= 13;
		$this->tpl_subno = "main_edit";
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = 'ページ詳細設定';
	}
}
$objPage = new LC_Page();
$objView = new SC_AdminView();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// ページ一覧を取得
$objPage->arrPageList = lfgetPageData();

// ブロックIDを取得
if (isset($_POST['page_id'])) {
	$page_id = $_POST['page_id'];
}else if ($_GET['page_id']){
	$page_id = $_GET['page_id'];
}else{
	$page_id = '';
}

$objPage->page_id = $page_id;

// メッセージ表示
if ($_GET['msg'] == "on"){
	$objPage->tpl_onload="alert('登録が完了しました。');";
}

// page_id が指定されている場合にはテンプレートデータの取得
if (is_numeric($page_id) and $page_id != '') {
	$arrPageData = lfgetPageData(" page_id = ? " , array($page_id));

	if ($arrPageData[0]['tpl_dir'] === "") {
		$objPage->arrErr['page_id_err'] = "※ 指定されたページは編集できません。";
		// 画面の表示
		$objView->assignobj($objPage);
		$objView->display(MAIN_FRAME);
		exit;
	}
	
	// テンプレートファイルが存在していれば読み込む
	$tpl_file = ROOT_DIR . $arrPageData[0]['tpl_dir'] . $arrPageData[0]['filename'] . ".tpl";
	if (file_exists($tpl_file)){
		$arrPageData[0]['tpl_data'] = file_get_contents($tpl_file);		
	}
	
	// チェックボックスの値変更
	$arrPageData[0]['header_chk'] = sfChangeCheckBox($arrPageData[0]['header_chk'], true);
	$arrPageData[0]['footer_chk'] = sfChangeCheckBox($arrPageData[0]['footer_chk'], true);

	// ディレクトリを画面表示用に編集
	$arrPageData[0]['directory'] = str_replace( USER_DIR,'', $arrPageData[0]['php_dir']);
	
	$objPage->arrPageData = $arrPageData[0];
}

// プレビュー処理
if ($_POST['mode'] == 'preview') {
	
	$page_id_old = $page_id;
	$page_id = 0;
	$url = uniqid("");

	$_POST['page_id'] = $page_id;
	$_POST['url'] = $url;
	
	$arrPreData = lfgetPageData(" page_id = ? " , array($page_id));

	// tplファイルの削除
	$del_tpl = ROOT_DIR . USER_DIR . "templates/" . $arrPreData[0]['filename'] . '.tpl';
	if (file_exists($del_tpl)){
		unlink($del_tpl);	
	}
	
	// DBへデータを更新する
	lfEntryPageData($_POST);

	// TPLファイル作成
	$cre_tpl = ROOT_DIR . USER_DIR . "templates/" . $url . '.tpl';
	lfCreateFile($cre_tpl);
	
	// blocposition を削除
	$objDBConn = new SC_DbConn;		// DB操作オブジェクト
	$sql = 'delete from dtb_blocposition where page_id = 0';
	$ret = $objDBConn->query($sql);
	
	if ($page_id_old != "") {
		// blocposition を複製
		$sql = " insert into dtb_blocposition ";
		$sql .= " select ";
		$sql .= "     0,";
		$sql .= "     target_id,";
		$sql .= "     bloc_id,";
		$sql .= "     bloc_row";
		$sql .= " from dtb_blocposition";
		$sql .= " where page_id = ?";
		$ret = $objDBConn->query($sql,array($page_id_old));
	}
	
	$_SESSION['preview'] = "ON";
	
	header("location: /preview/index.php");
}

// データ登録処理
if ($_POST['mode'] == 'confirm') {
	
	// エラーチェック
	$objPage->arrErr = lfErrorCheck($_POST);

	// エラーがなければ更新処理を行う	
	if (count($objPage->arrErr) == 0) {

		// DBへデータを更新する
		lfEntryPageData($_POST);
		
		// ベースデータでなければファイルを削除し、PHPファイルを作成する
		if (!lfCheckBaseData($page_id)) {
			// ファイル削除
			lfDelFile($arrPageData[0]);
			
			sfprintr($arrPageData[0]);
			
			// PHPファイル作成
			// ディレクトリが存在していなければ作成する
			$cre_php = ROOT_DIR . USER_DIR . $_POST['url'];

			if (!is_dir(dirname($cre_php))) {
				mkdir(dirname($cre_php));
			}
			copy(USER_DEF_PHP, $cre_php . ".php");
		}

		// TPLファイル作成
		$cre_tpl = dirname( ROOT_DIR . USER_DIR . "templates/" . $_POST['url']) . "/" . basename($_POST['url']) . '.tpl';
		
		sfprintr($cre_tpl);

		lfCreateFile($cre_tpl);

		// 編集可能ページの場合にのみ処理を行う
		if ($arrPageData[0]['edit_flg'] != 2) {
			// 新規作成した場合のために改にページIDを取得する
			$arrPageData = lfgetPageData(" url = ? " , array(USER_URL.$_POST['url'].".php"));
			$page_id = $arrPageData[0]['page_id'];
		}

		//header("location: ./main_edit.php?page_id=$page_id&msg=on");
	}else{
		// エラーがあれば入力時のデータを表示する
		$objPage->arrPageData = $_POST;
		$objPage->arrPageData['header_chk'] = sfChangeCheckBox(sfChangeCheckBox($_POST['header_chk']), true);
		$objPage->arrPageData['footer_chk'] = sfChangeCheckBox(sfChangeCheckBox($_POST['footer_chk']), true);
		$objPage->arrPageData['directory'] = $_POST['url'];
		$objPage->arrPageData['filename'] = "";
	}
}

// データ削除処理 ベースデータでなければファイルを削除
if ($_POST['mode'] == 'delete' and 	!lfCheckBaseData($page_id)) {
	lfDelPageData($_POST['page_id']);
}

// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**************************************************************************************************************
 * 関数名	：lfEntryPageData
 * 処理内容	：ブロック情報を更新する
 * 引数1	：$arrData  ･･･ 更新データ
 * 戻り値	：更新結果
 **************************************************************************************************************/
function lfEntryPageData($arrData){
	$objDBConn = new SC_DbConn;		// DB操作オブジェクト
	$sql = "";						// データ更新SQL生成用
	$ret = ""; 						// データ更新結果格納用
	$arrUpdData = array();			// 更新データ生成用
	$arrChk = array();				// 排他チェック用

	// 更新データ生成
	$arrUpdData = lfGetUpdData($arrData);
	
	// データが存在しているかチェックを行う
	if($arrData['page_id'] !== ''){
		$arrChk = lfgetPageData(" page_id = ?", array($arrData['page_id']));
	}

	// page_id が空 若しくは データが存在していない場合にはINSERTを行う
	if ($arrData['page_id'] === '' or !isset($arrChk[0])) {
		// SQL生成
		$sql = " INSERT INTO dtb_pagelayout ";
		$sql .= " ( ";
		$sql .= " 	  page_name";
		$sql .= "	  ,url";
		$sql .= "	  ,php_dir";
		$sql .= "	  ,tpl_dir";
		$sql .= "	  ,filename";
		$sql .= "	  ,header_chk";
		$sql .= "	  ,footer_chk";
		$sql .= "	  ,update_url";
		$sql .= " ) VALUES ( ?,?,?,?,?,?,?,? )";
		$sql .= " ";
	}else{
		// データが存在してる場合にはアップデートを行う
		// SQL生成
		$sql = " UPDATE dtb_pagelayout ";
		$sql .= " SET";
		$sql .= "	  page_name = ? ";
		$sql .= "	  ,url = ? ";
		$sql .= "	  ,php_dir = ? ";
		$sql .= "	  ,tpl_dir = ? ";
		$sql .= "	  ,filename = ? ";
		$sql .= "	  ,header_chk = ? ";
		$sql .= "	  ,footer_chk = ? ";
		$sql .= "	  ,update_url = ? ";
		$sql .= "     ,update_date = now() ";
		$sql .= " WHERE page_id = ?";
		$sql .= " ";

		// 更新データにブロックIDを追加
		array_push($arrUpdData, $arrData['page_id']);
	}

	// SQL実行
	$ret = $objDBConn->query($sql,$arrUpdData);
	
	return $ret;
}

/**************************************************************************************************************
 * 関数名	：lfGetUpdData
 * 処理内容	：DBへ更新を行うデータを生成する
 * 引数1	：$arrData  ･･･ 更新データ
 * 戻り値	：更新データ
 **************************************************************************************************************/
function lfGetUpdData($arrData){
	
	// ベースデータの場合には変更しない。
	if (lfCheckBaseData($arrData['page_id'])) {
		$arrPageData = lfgetPageData( ' page_id = ? ' , array($arrData['page_id']));

		$name = $arrPageData[0]['page_name'] ;
		$url = $arrPageData[0]['url'];
		$php_dir = $arrPageData[0]['php_dir'];
		$tpl_dir = $arrPageData[0]['tpl_dir'];
		$filename = $arrPageData[0]['filename'];
	}else{
		$name = $arrData['page_name'] ;
		$url = USER_URL.$arrData['url'].".php";
		$php_dir = dirname(USER_DIR.$arrData['url'])."/";
		$tpl_dir = dirname(USER_DIR."templates/".$arrData['url'])."/";
		$filename = basename($arrData['url']);
	}

	// 更新データ配列の作成
	$arrUpdData = array(
					$name										// 名称	
					,$url										// URL
					,$php_dir									// PHPディレクトリ
					,$tpl_dir									// TPLディレクトリ
					,$filename									// ファイル名
					,sfChangeCheckBox($arrData['header_chk'])	// ヘッダー使用
					,sfChangeCheckBox($arrData['footer_chk'])	// フッター使用
					,$_SERVER['HTTP_REFERER']					// 更新URL
					);
					
	return $arrUpdData;
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
	$objErr->doFunc(array("名称", "page_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("URL", "url", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));

	// URLチェック
	if (substr(strrev(trim($array['url'])),0,1) == "/") {
		$objErr->arrErr['url'] = "※ URLを正しく入力してください。<br />";
	}
	
	$check_url = USER_URL . $array['url'] . ".php";
	if( strlen($array['url']) > 0 && !ereg( "^https?://+($|[a-zA-Z0-9_~=&\?\.\/-])+$", $check_url ) ) {
		$objErr->arrErr['url'] = "※ URLを正しく入力してください。<br />";
	}

	// 同一のURLが存在している場合にはエラー
	if(!isset($objErr->arrErr['url']) and $array['url'] !== ''){
		$arrChk = lfgetPageData(" url = ? " , array(USER_URL . $array['url'].".php"));

		if (count($arrChk[0]) >= 1 and $arrChk[0]['page_id'] != $array['page_id']) {
			$objErr->arrErr['url'] = '※ 同じURLのデータが存在しています。別のURLを付けてください。';
		}
	}
	
	return $objErr->arrErr;
}

/**************************************************************************************************************
 * 関数名	：lfCreateFile
 * 処理内容	：TPLファイルを作成する
 * 引数1	：$path･･･テンプレートファイルのパス
 * 戻り値	：なし
 **************************************************************************************************************/
function lfCreateFile($path){
	
	// ディレクトリが存在していなければ作成する		
	if (!is_dir(dirname($path))) {
		mkdir(dirname($path));
	}
	
	if(file_exists($path)){
		sfprintr("dd");
	};
	
	// ファイル作成
	$fp = fopen($path,"w");
	fwrite($fp, $_POST['tpl_data']);
	fclose($fp);	
}
