<?php
class SC_PageLayout {
	
    var $arrPageData;		// ページデータ格納用
    var $arrPageList;		// ページデータ格納用
	
    // コンストラクタ
    function SC_PageLayout() {
		$this->arrPageList = $this->getPageData();
	}
    
	/**************************************************************************************************************
	 * 関数名	：getPageData
	 * 処理内容	：ブロック情報を取得する
	 * 引数1	：$where  ･･･ Where句文
	 * 引数2	：$arrVal ･･･ Where句の絞込条件値
	 * 戻り値	：ブロック情報
	 **************************************************************************************************************/
	function getPageData($where = '', $arrVal = ''){
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
		$sql .= " ,author";				// authorタグ
		$sql .= " ,description";		// descriptionタグ
		$sql .= " ,keyword";			// keywordタグ
		$sql .= " ,update_url";			// 更新URL
		$sql .= " ,create_date";		// データ作成日
		$sql .= " ,update_date";		// データ更新日
		$sql .= " FROM ";
		$sql .= "     dtb_pagelayout";
		
		// where句の指定があれば追加	
		if ($where != '') {
			$sql .= " WHERE " . $where;
		}
		
		$sql .= " ORDER BY 	page_id";
		
		$arrRet = $objDBConn->getAll($sql, $arrVal);
		
		$this->arrPageData = $arrRet;
		
		return $arrRet;
	}


}
?>