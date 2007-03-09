<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/**************************************************************************************************************
 * 関数名	：sfGetPageLayout
 * 処理内容	：ページのレイアウト情報をセットする
 * 引数1	：$objPage
 * 引数2	：$preview
 * 戻り値	：ページのレイアウト情報
 **************************************************************************************************************/
function sfGetPageLayout($objPage, $preview = false, $url = ""){
	$arrPageLayout = array();

	// 現在のURLの取得
	if ($preview === false) {
		if ($url == "") {
			$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		// URLを元にページデザインを取得
		$arrPageData = lfgetPageData(" url = ? " , array($url));
	}else{
		$arrPageData = lfgetPageData(" page_id = ? " , array("0"));
	}

	// メインテンプレートファイルを設定
	if (!isset($objPage->tpl_mainpage)) {
		$objPage->tpl_mainpage = HTML_PATH . $arrPageData[0]['tpl_dir'] . $arrPageData[0]['filename'] . ".tpl";
	}
	
	// ページタイトルを設定
	if (!isset($objPage->tpl_title)) {
		$objPage->tpl_title = $arrPageData[0]['page_name'];
	}

	$arrPageLayout = $arrPageData[0];

	// 全ナビデータを取得する
	$arrNavi = lfGetNaviData($url, $preview);
	
	$arrPageLayout['LeftNavi']  = lfGetNavi($arrNavi,1);	// LEFT NAVI
	$arrPageLayout['MainHead']  = lfGetNavi($arrNavi,2);	// メイン上部
	$arrPageLayout['RightNavi'] = lfGetNavi($arrNavi,3);	// RIGHT NAVI
	$arrPageLayout['MainFoot']  = lfGetNavi($arrNavi,4);	// メイン下部
	
	$objPage->arrPageLayout = $arrPageLayout;
	
	return $objPage;
}

/**************************************************************************************************************
 * 関数名	：lfgetPageData
 * 処理内容	：ページ情報を取得する
 * 引数1	：$where  ･･･ Where句文
 * 引数2	：$arrVal ･･･ Where句の絞込条件値
 * 戻り値	：ページ情報
 **************************************************************************************************************/
function lfgetPageData($where = '', $arrVal = ''){
	$objDBConn = new SC_DbConn;		// DB操作オブジェクト
	$sql = "";						// データ取得SQL生成用
	$arrRet = array();				// データ取得用
	
	// SQL生成
	$sql .= " SELECT";
	$sql .= " page_id";				// ページID
	$sql .= " ,page_name";			// 名称
	$sql .= " ,url";				// URL
	$sql .= " ,php_dir";			// php保存先ディレクトリ
	$sql .= " ,tpl_dir";			// tpl保存先ディdレクトリ
	$sql .= " ,filename";			// ファイル名称
	$sql .= " ,header_chk ";		// ヘッダー使用FLG
	$sql .= " ,footer_chk ";		// フッター使用FLG
	$sql .= " ,edit_flg ";			// 編集可能FLG
	$sql .= " ,author";				// authorタグ
	$sql .= " ,description";		// descriptionタグ
	$sql .= " ,keyword";			// keywordタグ
	$sql .= " ,update_url";			// 更新URL
	$sql .= " ,create_date";		// データ作成日
	$sql .= " ,update_date";		// データ更新日
	$sql .= " FROM ";
	$sql .= "     dtb_pagelayout";
	$sql .= " WHERE ";
	
	// where句の指定があれば追加
	if ($where != '') {
		$sql .= $where;
	}else{
		$sql .= "     page_id != 0 ";
	}

	$sql .= " ORDER BY 	page_id";
	
	$arrRet = $objDBConn->getAll($sql, $arrVal);
	
	return $arrRet;
}


/**************************************************************************************************************
 * 関数名	：lfGetNaviData
 * 処理内容	：ナビ情報を取得する
 * 戻り値	：ブロック情報
 **************************************************************************************************************/
function lfGetNaviData($url, $preview=false){
	$objDBConn = new SC_DbConn;		// DB操作オブジェクト
	$sql = "";						// データ取得SQL生成用
	$arrRet = array();				// データ取得用
	$arrData = array();

	// SQL文生成		
	$sql = "";
	$sql .= " SELECT ";
	$sql .= "     target_id ";
	$sql .= "     ,(SELECT bloc_name FROM dtb_bloc AS bloc WHERE bloc.bloc_id = pos.bloc_id) AS bloc_name";
	$sql .= "     ,(SELECT tpl_path FROM dtb_bloc AS bloc WHERE bloc.bloc_id = pos.bloc_id) AS tpl_path";
	$sql .= "     ,(SELECT php_path FROM dtb_bloc AS bloc WHERE bloc.bloc_id = pos.bloc_id) AS php_path";
	$sql .= " FROM";
	$sql .= "     dtb_blocposition AS pos";
	$sql .= " WHERE";
	if ($preview == true) {
		$sql .= "     page_id = (SELECT page_id FROM dtb_pagelayout WHERE page_id = '0')";
	}else{
		$sql .= "     page_id = (SELECT page_id FROM dtb_pagelayout WHERE url = ?)";
		$arrData = array($url);
	}
	$sql .= " ORDER BY target_id,bloc_row";
	$sql .= " ";
	
	// SQL実行
	$arrRet = $objDBConn->getAll($sql, $arrData);
	
	// 結果を返す
	return $arrRet;
}


/**************************************************************************************************************
 * 関数名	：lfGetNavi
 * 処理内容	：各部分のナビ情報を取得する
 * 戻り値	：ブロック情報
 **************************************************************************************************************/
function lfGetNavi($arrNavi, $target_id){
	$arrRet = array();
	if(is_array($arrNavi)) {
		foreach($arrNavi as $key => $val){
			// 指定された箇所と同じデータだけを取得する
			if ($target_id == $val['target_id']){
				if ($val['php_path'] != '') {
					$arrNavi[$key]['php_path'] = HTML_PATH . $val['php_path'];
					$arrNavi[$key]['include'] = "<!--{include file='".$val['php_path']."'}-->";
				}else{
					$arrNavi[$key]['tpl_path'] = USER_PATH . $val['tpl_path'];
					$arrNavi[$key]['include'] = "<!--{include file='". USER_PATH . $val['tpl_path'] ."'}-->";
				}
				
				$arrRet[] = $arrNavi[$key];
			}
		}
	}
	return $arrRet;	
}


function lfDelPageData($page_id){
	// DBへデータを更新する
	$objDBConn = new SC_DbConn;		// DB操作オブジェクト
	$sql = "";						// データ更新SQL生成用
	$ret = ""; 						// データ更新結果格納用
	$arrDelData = array();			// 更新データ生成用
	
	// page_id が空でない場合にはdeleteを実行
	if ($page_id !== '') {
		// SQL生成
		$sql = " DELETE FROM dtb_pagelayout WHERE page_id = ?";

		// SQL実行
		$ret = $objDBConn->query($sql,array($page_id));

		// ファイルの削除
		lfDelFile($arrPageData[0]);
	}

	header("location: ".$_SERVER['REQUEST_URI']);

}

/**************************************************************************************************************
 * 関数名	：lfDelFile	
 * 処理内容	：ファイルを削除する
 * 引数1	：$data  ･･･ 削除データ
 * 戻り値	：なし
 **************************************************************************************************************/
function lfDelFile($arrData){
	// ファイルディレクトリ取得
	$del_php = HTML_PATH . $arrData['php_dir'] . $arrData['filename'] . ".php";
	$del_tpl = HTML_PATH . $arrData['tpl_dir'] . $arrData['filename'] . ".tpl";

	// phpファイルの削除
	if (file_exists($del_php)){
		unlink($del_php);	
	}

	// tplファイルの削除
	if (file_exists($del_tpl)){
		unlink($del_tpl);	
	}
}

/**************************************************************************************************************
 * 関数名	：lfCheckBaseData
 * 処理内容	：データがベースデータなのかを判断する
 * 引数1	：$data  ･･･ 判断データ（ページID）
 * 戻り値	：true･･･ベースデータ、false･･･ベースデータでない
 **************************************************************************************************************/
function lfCheckBaseData($data){
	$ret = false;
	
	if ($data == 0) {
		return $ret;		
	}
	
	$arrChkData = lfgetPageData("page_id = ?", array($data));	
	
	if ($arrChkData[0]['edit_flg'] == 2){
		$ret = true;
	}
	
	return $ret;
}

?>