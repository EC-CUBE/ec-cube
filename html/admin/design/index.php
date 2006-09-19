<?php

require_once("../../require.php");
require_once(ROOT_DIR."data/include/page_layout.inc");

class LC_Page {
	var $arrForm;
	var $arrHidden;

	function LC_Page() {
		$this->tpl_mainpage = 'design/index.tpl';
		$this->tpl_subnavi = 'design/subnavi.tpl';
		$this->tpl_subno = "layout";		
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = 'レイアウト編集';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// ページIDを取得
if (isset($_GET['page_id'])) {
	$page_id = $_GET['page_id'];
}else if ($_POST['page_id']){
	$page_id = $_POST['page_id'];
}else{
	$page_id = 1;
}

// 編集可能ページを取得
$objPage->arrEditPage = lfgetPageData();

// ブロック配置用データを取得
$sel   = ", pos.target_id, pos.bloc_id, pos.bloc_row ";
$from  = ", dtb_blocposition AS pos";
$where = " where ";
$where .= " lay.page_id = ? AND ";
$where .= "lay.page_id = pos.page_id AND exists (select bloc_id from dtb_bloc as blc where pos.bloc_id = blc.bloc_id) ORDER BY lay.page_id,pos.target_id, pos.bloc_row, pos.bloc_id ";
$arrData = array($page_id);
$arrBlocPos = lfgetLayoutData($sel, $from, $where, $arrData );

// データの存在チェックを行う
$arrPageData = lfgetPageData("page_id = ?", array($page_id));
if (count($arrPageData) <= 0) {
	$exists_page = 0;
}else{
	$exists_page = 1;
}
$objPage->exists_page = $exists_page;

// メッセージ表示
if ($_GET['msg'] == "on") {
	$objPage->complate_msg="alert('登録が完了しました。');";
}

// ブロックを取得
$arrBloc = lfgetBlocData();

// 新規ブロック作成
if ($_POST['mode'] == 'new_bloc') {
	header("location: ./bloc.php");
}

// 新規ページ作成
if ($_POST['mode'] == 'new_page') {
	header("location: ./main_edit.php");
}

// データ登録処理
if ($_POST['mode'] == 'confirm' or $_POST['mode'] == 'preview') {
	
	$arrPageData = array();
	if ($_POST['mode'] == 'preview') {
		$arrPageData = lfgetPageData(" page_id = ? " , array($page_id));
		$page_id = 0;
		$_POST['page_id'] = 0;
	}
	
	// 更新用にデータを整える
	$arrUpdBlocData = array();
	$arrTargetFlip = array_flip($arrTarget);
	
	$upd_cnt = 1;
	$arrUpdData[$upd_cnt]['page_id'] = $_POST['page_id'];
	
	// POSTのデータを使いやすいように修正
	for($upd_cnt = 1; $upd_cnt <= $_POST['bloc_cnt']; $upd_cnt++){
		if (!isset($_POST['id_'.$upd_cnt])) {
			break;
		}
		$arrUpdBlocData[$upd_cnt]['name'] 		= $_POST['name_'.$upd_cnt];							// ブロック名称
		$arrUpdBlocData[$upd_cnt]['id']	  		= $_POST['id_'.$upd_cnt];							// ブロックID 
		$arrUpdBlocData[$upd_cnt]['target_id'] 	= $arrTargetFlip[$_POST['target_id_'.$upd_cnt]];	// ターゲットID
		$arrUpdBlocData[$upd_cnt]['top'] 		= $_POST['top_'.$upd_cnt];							// TOP座標
		$arrUpdBlocData[$upd_cnt]['update_url']	= $_SERVER['HTTP_REFERER'];							// 更新URL
	}

	// データの更新を行う
	$objDBConn = new SC_DbConn;		// DB操作オブジェクト
	$arrRet = array();				// データ取得用
	
	// delete実行
	$del_sql = "";
	$del_sql .= "DELETE FROM dtb_blocposition WHERE page_id = ? ";
	$arrRet = $objDBConn->query($del_sql,array($page_id));
	
	// ブロックの順序を取得し、更新を行う
	foreach($arrUpdBlocData as $key => $val){
		// ブロックの順序を取得
		$bloc_row = lfGetRowID($arrUpdBlocData, $val);
		$arrUpdBlocData[$key]['bloc_row'] = $bloc_row;
		$arrUpdBlocData[$key]['page_id'] 	= $_POST['page_id'];	// ページID
		
		if ($arrUpdBlocData[$key]['target_id'] == 5) {
			$arrUpdBlocData[$key]['bloc_row'] = "0";
		}
		
		// insert文生成
		$ins_sql = "";
		$ins_sql .= "INSERT INTO dtb_blocposition ";
		$ins_sql .= " values ( ";
		$ins_sql .= "	?  ";			// ページID
		$ins_sql .= "	,? ";			// ターゲットID
		$ins_sql .= "	,? ";			// ブロックID
		$ins_sql .= "	,? ";			// ブロックの並び順序
		$ins_sql .= "	,(SELECT filename FROM dtb_bloc WHERE bloc_id = ?) ";			// ファイル名称
		$ins_sql .= "	)  ";

		// insertデータ生成
		$arrInsData = array($page_id,
							 $arrUpdBlocData[$key]['target_id'],
							 $arrUpdBlocData[$key]['id'],
							 $arrUpdBlocData[$key]['bloc_row'],
							 $arrUpdBlocData[$key]['id']
							);
		// SQL実行
		$arrRet = $objDBConn->query($ins_sql,$arrInsData);
	}

	// プレビュー処理
	if ($_POST['mode'] == 'preview') {
		if ($page_id === "") {
			header("location: ./index.php");
		}
		lfSetPreData($arrPageData);
		
		$_SESSION['preview'] = "ON";
		header("location: /preview/index.php");
	}else{
		header("location: ./index.php?page_id=$page_id&msg=on");
	}
}

// データ削除処理 ベースデータでなければファイルを削除
if ($_POST['mode'] == 'delete' and 	!lfCheckBaseData($page_id)) {
	lfDelPageData($page_id);
}

// ブロック情報を画面配置用に編集
$tpl_arrBloc = array();
$cnt = 0;
// 使用されているブロックデータを生成
foreach($arrBlocPos as $key => $val){
	if ($val['page_id'] == $page_id) {
		$tpl_arrBloc = lfSetBlocData($arrBloc, $val, $tpl_arrBloc, $cnt);
		$cnt++;
	}
}

// 未使用のブロックデータを追加
foreach($arrBloc as $key => $val){
	if (!lfChkBloc($val, $tpl_arrBloc)) {
		$val['target_id'] = 5;	// 未使用に追加する
		$tpl_arrBloc = lfSetBlocData($arrBloc, $val, $tpl_arrBloc, $cnt);
		$cnt++;
	}
}

$objPage->tpl_arrBloc = $tpl_arrBloc;
$objPage->bloc_cnt = count($tpl_arrBloc);
$objPage->page_id = $page_id;

// ページ名称を取得
$arrPageData = lfgetPageData(' page_id = ?', array($page_id));
$objPage->arrPageData = $arrPageData[0];

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

global $GLOBAL_ERR;
$errCnt = 0;
if ($GLOBAL_ERR != "") {
	$arrGlobalErr = explode("\n",$GLOBAL_ERR);
	$errCnt = count($arrGlobalErr) - 8;
	if ($errCnt < 0 ) {
		$errCnt = 0;
	}
}
$objPage->errCnt = $errCnt;

// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

/**************************************************************************************************************
 * 関数名	：lfgetLayoutData
 * 処理内容	：編集可能なページ情報を取得する
 * 引数1	：$sel    ･･･ Select句文
 * 引数2	：$where  ･･･ Where句文
 * 引数3	：$arrVal ･･･ Where句の絞込条件値
 * 戻り値	：ページレイアウト情報
 **************************************************************************************************************/
function lfgetLayoutData($sel = '' , $from = '', $where = '', $arrVal = ''){
	$objDBConn = new SC_DbConn;		// DB操作オブジェクト
	$sql = "";						// データ取得SQL生成用
	$arrRet = array();				// データ取得用
	
	// SQL生成

	$sql = "";
	$sql .= " select "; 
	$sql .= "     lay.page_id ";
	$sql .= "     ,lay.page_name ";
	$sql .= "     ,lay.url ";
	$sql .= "     ,lay.author ";
	$sql .= "     ,lay.description ";
	$sql .= "     ,lay.keyword ";
	$sql .= "     ,lay.update_url ";
	$sql .= "     ,lay.create_date ";
	$sql .= "     ,lay.update_date ";
	
	// Select句の指定があれば追加	
	if ($sel != '') {
		$sql .= $sel;
	}
	
	$sql .= " from dtb_pagelayout AS lay ";
	// From句の指定があれば追加	
	if ($from != '') {
		$sql .= $from;
	}

	// where句の指定があれば追加	
	if ($where != '') {
		$sql .= $where;
	}else{
		$sql .= " ORDER BY lay.page_id ";
	}
	
	$arrRet = $objDBConn->getAll($sql, $arrVal);
	
	return $arrRet;
}

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
	$sql = "";
	$sql .= " SELECT ";
	$sql .= "	bloc_id";
	$sql .= "	,bloc_name";
	$sql .= "	,tpl_path";
	$sql .= "	,filename";
	$sql .= " 	,update_date";
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
 * 関数名	：lfSetBlocData
 * 処理内容	：ブロック情報の配列を生成する
 * 引数1	：$arrBloc    	･･･ Bloc情報
 * 引数2	：$tpl_arrBloc	･･･ データをセットする配列
 * 引数3	：$cnt			･･･ 配列番号
 * 戻り値	：データをセットした配列
 **************************************************************************************************************/
function lfSetBlocData($arrBloc, $val, $tpl_arrBloc, $cnt) {
	global $arrTarget;
	
	$tpl_arrBloc[$cnt]['target_id'] = $arrTarget[$val['target_id']];
	$tpl_arrBloc[$cnt]['bloc_id'] = $val['bloc_id'];
	$tpl_arrBloc[$cnt]['bloc_row'] = $val['bloc_row'];

	foreach($arrBloc as $bloc_key => $bloc_val){
		if ($bloc_val['bloc_id'] == $val['bloc_id']) {
			$bloc_name = $bloc_val['bloc_name'];
			break;
		}
	}
	$tpl_arrBloc[$cnt]['name'] = $bloc_name;
	
	return $tpl_arrBloc;
}

/**************************************************************************************************************
 * 関数名	：lfChkBloc
 * 処理内容	：ブロックIDが配列に追加されているかのチェックを行う
 * 引数1	：$arrBloc    ･･･ Bloc情報
 * 引数2	：$arrChkData ･･･ チェックを行うデータ配列
 * 戻り値	：True	･･･ 存在する
 * 			　False	･･･ 存在しない
 **************************************************************************************************************/
function lfChkBloc($arrBloc, $arrChkData) {
	foreach($arrChkData as $key => $val){
		if ($val['bloc_id'] === $arrBloc['bloc_id'] ) {
			// 配列に存在すればTrueを返す
			return true;
		}
	}
	
	// 配列に存在しなければFlaseを返す
	return false;
}

/**************************************************************************************************************
 * 関数名	：lfGetRowID
 * 処理内容	：ブロックIDが何番目に配置されているかを調べる
 * 引数1	：$arrUpdData   ･･･ 更新情報
 * 引数2	：$arrObj 		･･･ チェックを行うデータ配列
 * 戻り値	：順番
 **************************************************************************************************************/
function lfGetRowID($arrUpdData, $arrObj){
	$no = 0; // カウント用（同じデータが必ず1件あるので、初期値は0）
	
	// 対象データが何番目に配置されているのかを取得する。
	foreach ($arrUpdData as $key => $val) {
		if ($val['target_id'] === $arrObj['target_id'] and $val['top'] <= $arrObj['top']){
			$no++;
		}
	}
	// 番号を返す
	return $no;
}

/**************************************************************************************************************
 * 関数名	：lfGetRowID
 * 処理内容	：ブロックIDが何番目に配置されているかを調べる
 * 引数1	：$arrUpdData   ･･･ 更新情報
 * 引数2	：$arrObj 		･･･ チェックを行うデータ配列
 * 戻り値	：順番
 **************************************************************************************************************/
function lfSetPreData($arrPageData){
	$objDBConn = new SC_DbConn;		// DB操作オブジェクト
	$sql = "";						// データ更新SQL生成用
	$ret = ""; 						// データ更新結果格納用
	$arrUpdData = array();			// 更新データ生成用
	$filename = uniqid("");

	$arrPreData = lfgetPageData(" page_id = ? " , array(0));

	// tplファイルの削除
	$del_tpl = ROOT_DIR . USER_DIR . "templates/" . $arrPreData[0]['filename'] . '.tpl';
	if (file_exists($del_tpl)){
		unlink($del_tpl);	
	}

	// tplファイルのコピー
	copy(ROOT_DIR . $arrPageData[0]['tpl_dir'].$arrPageData[0]['filename'].".tpl", ROOT_DIR . USER_DIR."templates/".$filename.".tpl");
	
	sfprintr($arrPageData[0]['page_id']);
	
	// 更新データの取得
	$sql = "select page_name, header_chk, footer_chk from dtb_pagelayout where page_id = ?";
	$ret = $objDBConn->getAll($sql, $arrPageData[0]['page_id']);
	
	sfprintr($ret);
	exit;
	// dbデータのコピー
	$sql = " update dtb_pagelayout set ";
	$sql .= "     page_name = (select page_name from dtb_pagelayout where page_id = ?)";
	$sql .= "     ,header_chk = (select header_chk from dtb_pagelayout where page_id = ?)";
	$sql .= "     ,footer_chk = (select footer_chk from dtb_pagelayout where page_id = ?)";
	$sql .= "     ,url = ?";
	$sql .= "     ,tpl_dir = ?";
	$sql .= "     ,filename = ?";
	$sql .= " where page_id = 0";
	
	$arrUpdData = array($arrPageData[0]['page_id']
						,$arrPageData[0]['page_id']
						,$arrPageData[0]['page_id']
						,USER_DIR."templates/"
						,USER_DIR."templates/"
						,$filename
						);
	
	$objDBConn->query($sql,$arrUpdData);
}
