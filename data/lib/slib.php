<?php

//---このファイルのパスを指定
$INC_PATH = realpath( dirname( __FILE__) );
require_once( $INC_PATH ."/../conf/conf.php" );
require_once( $INC_PATH ."/../class/SC_DbConn.php" );

// 全ページ共通エラー
$GLOBAL_ERR = "";

// インストール初期処理
sfInitInstall();
// アップデートで生成されたPHPを読み出す
sfLoadUpdateModule();

/* テーブルの存在チェックチェック */
function sfTabaleExists($table_name) {
	$objQuery = new SC_Query();	
	// postgresqlとmysqlとで処理を分ける
	if (DB_TYPE == "pgsql") {
		$sql = "SELECT
					relname
				FROM
				    pg_class
				WHERE
					(relkind = 'r' OR relkind = 'v') AND 
				    relname = ? 
				GROUP BY
					relname";
		$arrRet = $objQuery->getAll($sql, array($table_name));
		if(count($arrRet) > 0) {
			$flg = true;
		} else {
			$flg = false;
		}	
	}else if (DB_TYPE == "mysql") {	
		$sql = "SHOW TABLE STATUS LIKE ?";
		$arrRet = $objQuery->getAll($sql, array($table_name));
		if(count($arrRet) > 0) {
			$flg = true;
		} else {
			$flg = false;
		}
	}
	return $flg;
}

// インストール初期処理
function sfInitInstall() {
	// インストール済みが定義されていない。
	if(!defined('ECCUBE_INSTALL')) {
		if(!ereg("^/install/", $_SERVER['PHP_SELF'])) {
			header("Location: /install/");
		}	
	} else {
		$path = ROOT_DIR . "html/install/index.php";
		if(file_exists($path)) {
			sfErrorHeader(">> /install/index.phpは、インストール完了後にファイルを削除してください。");
		}
	}
}

// アップデートで生成されたPHPを読み出し
function sfLoadUpdateModule() {
	if(ereg("^/install/", $_SERVER['PHP_SELF'])) {
		return;
	}		
	//DBから設定情報を取得
	if(defined('DB_USER') && defined('DB_PASSWORD') && defined('DB_SERVER') && defined('DB_NAME')) {
		$objConn = new SC_DbConn(DEFAULT_DSN);
		// 最初に実行するPHPソースを検索する
		$arrRet = $objConn->getAll("SELECT extern_php FROM dtb_update WHERE main_php = ? OR main_php = '*'",array($_SERVER['PHP_SELF']));
		foreach($arrRet as $array) {
			if($array['extern_php'] != "") {
				$path = ROOT_DIR . $array['extern_php'];
				if(file_exists($path)) {
					require_once($path);
				}
			}
		}
	}
}

function sf_getBasisData() {
	//DBから設定情報を取得
	$objConn = new SC_DbConn(DEFAULT_DSN);
	$result = $objConn->getAll("SELECT * FROM dtb_baseinfo");
	if(is_array($result[0])) {
		foreach ( $result[0] as $key=>$value ){
			$CONF["$key"] = $value;
		}
	}
	return $CONF;
}

// 装飾付きエラーメッセージの表示
function sfErrorHeader($mess, $print = false) {
	global $GLOBAL_ERR;
	if($GLOBAL_ERR == "") {
		$GLOBAL_ERR = "<meta http-equiv='Content-Type' content='text/html; charset=EUC-JP'>\n";
	}
	$GLOBAL_ERR.= "<table width='100%' border='0' cellspacing='0' cellpadding='0' summary=' '>\n";
	$GLOBAL_ERR.= "<tr>\n";
	$GLOBAL_ERR.= "<td bgcolor='#ffeebb' height='25' colspan='2' align='center'>\n";
	$GLOBAL_ERR.= "<SPAN style='color:red; font-size:12px'><strong>" . $mess . "</strong></span>\n";
	$GLOBAL_ERR.= "</td>\n";
	$GLOBAL_ERR.= "	</tr>\n";
	$GLOBAL_ERR.= "</table>\n";
	
	if($print) {
		print($GLOBAL_ERR);
	}
}

/* エラーページの表示 */
function sfDispError($type) {
	
	class LC_ErrorPage {
		function LC_ErrorPage() {
			$this->tpl_mainpage = 'login_error.tpl';
			$this->tpl_title = 'エラー';
		}
	}

	$objPage = new LC_ErrorPage();
	$objView = new SC_AdminView();
	
	switch ($type) {
	    case LOGIN_ERROR:
			$objPage->tpl_error="ＩＤまたはパスワードが正しくありません。<br />もう一度ご確認のうえ、再度入力してください。";
	    	break;
		case ACCESS_ERROR:
			$objPage->tpl_error="ログイン認証の有効期限切れの可能性があります。<br />もう一度ご確認のうえ、再度ログインしてください。";
			break;
		case AUTH_ERROR:
			$objPage->tpl_error="このファイルにはアクセス権限がありません。<br />もう一度ご確認のうえ、再度ログインしてください。";
			break;
		case PAGE_ERROR:
			$objPage->tpl_error="不正なページ移動です。<br />もう一度ご確認のうえ、再度入力してください。";
			break;
		default:
	    	$objPage->tpl_error="エラーが発生しました。<br />もう一度ご確認のうえ、再度ログインしてください。";
			break;
	}
	
	$objView->assignobj($objPage);
	$objView->display(LOGIN_FRAME);
	
	exit;
}

/* サイトエラーページの表示 */
function sfDispSiteError($type, $objSiteSess = "") {
	
	if ($objSiteSess != "") {
		$objSiteSess->setNowPage('error');
	}
	
	class LC_ErrorPage {
		function LC_ErrorPage() {
			$this->tpl_mainpage = 'error.tpl';
			$this->tpl_css = '/css/layout/error.css';
			$this->tpl_title = 'エラー';
		}
	}
	
	$objPage = new LC_ErrorPage();
	$objView = new SC_SiteView();
	
	switch ($type) {
	    case PRODUCT_NOT_FOUND:
			$objPage->tpl_error="ご指定のページはございません。";
			break;
		case PAGE_ERROR:
			$objPage->tpl_error="不正なページ移動です。";
			break;
		case CART_EMPTY:
			$objPage->tpl_error="カートに商品ががありません。";
			break;
	    case CART_ADD_ERROR:
			$objPage->tpl_error="購入処理中は、カートに商品を追加することはできません。";
			break;
		case CANCEL_PURCHASE:
			$objPage->tpl_error="この手続きは無効となりました。以下の要因が考えられます。<br />・セッション情報の有効期限が切れてる場合<br />・購入手続き中に新しい購入手続きを実行した場合<br />・すでに購入手続きを完了している場合";
			break;
		case CATEGORY_NOT_FOUND:
			$objPage->tpl_error="ご指定のカテゴリは存在しません。";
			break;
		case SITE_LOGIN_ERROR:
			$objPage->tpl_error="メールアドレスもしくはパスワードが正しくありません。";
			break;
		case TEMP_LOGIN_ERROR:
			$objPage->tpl_error="メールアドレスもしくはパスワードが正しくありません。<br />本登録がお済みでない場合は、仮登録メールに記載されている<br />URLより本登録を行ってください。";
			break;
		case CUSTOMER_ERROR:
			$objPage->tpl_error="不正なアクセスです。";
			break;
		case SOLD_OUT:
			$objPage->tpl_error="申し訳ございませんが、ご購入の直前で売り切れた商品があります。この手続きは無効となりました。";
			break;
		case CART_NOT_FOUND:
			$objPage->tpl_error="申し訳ございませんが、カート内の商品情報の取得に失敗しました。この手続きは無効となりました。";
			break;
		case LACK_POINT:
			$objPage->tpl_error="申し訳ございませんが、ポイントが不足しております。この手続きは無効となりました。";
			break;
		case FAVORITE_ERROR:
			$objPage->tpl_error="既にお気に入りに追加されている商品です。";
			break;
		case EXTRACT_ERROR:
			$objPage->tpl_error="ファイルの解凍に失敗しました。\n指定のディレクトリに書き込み権限が与えられていない可能性があります。";
			break;
		case FTP_DOWNLOAD_ERROR:
			$objPage->tpl_error="ファイルのFTPダウンロードに失敗しました。";
			break;
		case FTP_LOGIN_ERROR:
			$objPage->tpl_error="FTPログインに失敗しました。";
			break;
		case FTP_CONNECT_ERROR:
			$objPage->tpl_error="FTPログインに失敗しました。";
			break;
		case CREATE_DB_ERROR:
			$objPage->tpl_error="DBの作成に失敗しました。\n指定のユーザーには、DB作成の権限が与えられていない可能性があります。";
			break;
		case DB_IMPORT_ERROR:
			$objPage->tpl_error="データベース構造のインポートに失敗しました。\nsqlファイルが壊れている可能性があります。";
			break;
		case FILE_NOT_FOUND:
			$objPage->tpl_error="指定のパスに、設定ファイルが存在しません。";
			break;
		case WRITE_FILE_ERROR:
			$objPage->tpl_error="設定ファイルに書き込めません。\n設定ファイルに書き込み権限を与えてください。";
			break;
 		default:
	    	$objPage->tpl_error="エラーが発生しました。";
			break;
	}
	
	$objView->assignobj($objPage);
	$objView->display(SITE_FRAME);
	exit;
}

/* 認証の可否判定 */
function sfIsSuccess($objSess, $disp_error = true) { 
	$ret = $objSess->IsSuccess();
	if($ret != SUCCESS) {
		if($disp_error) {
			// エラーページの表示
			sfDispError($ret);
		}
		return false;
	}
	return true;		
}

/* 前のページで正しく登録が行われたか判定 */
function sfIsPrePage($objSiteSess) {
	$ret = $objSiteSess->isPrePage();
	if($ret != true) {
		// エラーページの表示
		sfDispSiteError(PAGE_ERROR, $objSiteSess);
	}
}

function sfCheckNormalAccess($objSiteSess, $objCartSess) {
	// ユーザユニークIDの取得
	$uniqid = $objSiteSess->getUniqId();
	// 購入ボタンを押した時のカート内容がコピーされていない場合のみコピーする。
	$objCartSess->saveCurrentCart($uniqid);
	// POSTのユニークIDとセッションのユニークIDを比較(ユニークIDがPOSTされていない場合はスルー)
	$ret = $objSiteSess->checkUniqId();
	if($ret != true) {
		// エラーページの表示
		sfDispSiteError(CANCEL_PURCHASE, $objSiteSess);
	}
	
	// カート内が空でないか || 購入ボタンを押してから変化がないか
	$quantity = $objCartSess->getTotalQuantity();
	$ret = $objCartSess->checkChangeCart();
	if($ret == true || !($quantity > 0)) {
		// カート情報表示に強制移動する
		header("Location: ".URL_CART_TOP);
		exit;
	}
	return $uniqid;
}

/* DB用日付文字列取得 */
function sfGetTimestamp($year, $month, $day, $last = false) {
	if($year != "" && $month != "" && $day != "") {	
		if($last) {
			$time = "23:59:59";
		} else {
			$time = "00:00:00";
		}
		$date = $year."-".$month."-".$day." ".$time;
	} else {
		$date = "";
	}
	return 	$date;
}

// INT型の数値チェック
function sfIsInt($value) {
	if($value != "" && strlen($value) <= INT_LEN && is_numeric($value)) {
		return true;
	}
	return false;
}

function sfCSVDownload($data, $prefix = ""){
	
	if($prefix == "") {
		$dir_name = sfUpDirName();
		$file_name = $dir_name . date("ymdHis") .".csv";
	} else {
		$file_name = $prefix . date("ymdHis") .".csv";
	}
	
	/* HTTPヘッダの出力 */
	Header("Content-disposition: attachment; filename=${file_name}");
	Header("Content-type: application/octet-stream; name=${file_name}");
	Header("Cache-Control: ");
	Header("Pragma: ");
	
	/* i18n~ だと正常に動作しないため、mb~ に変更
	if (i18n_discover_encoding($data) == 'EUC-JP'){
		$data = i18n_convert($data,'SJIS','EUC-JP');
	}
	*/
	if (mb_internal_encoding() == 'EUC-JP'){
		$data = mb_convert_encoding($data,'SJIS','EUC-JP');
	}
	
	/* データを出力 */
	echo $data;
}

/* 1階層上のディレクトリ名を取得する */
function sfUpDirName() {
	$path = $_SERVER['PHP_SELF'];
	$arrVal = split("/", $path);
	$cnt = count($arrVal);
	return $arrVal[($cnt - 2)];
}

// 現在のサイトを更新（ただしポストは行わない）
function sfReload($get = "") {
	if ($_SERVER["SERVER_PORT"] == "443" ){
		$protocol = "https";
	} else {
		$protocol = "http";
	}
		
	if($get != "") {
		header("Location: ".$protocol."://" .$_SERVER["SERVER_NAME"] . $_SERVER['PHP_SELF'] . "?" . $get);
	} else {
		header("Location: ".$protocol."://" .$_SERVER["SERVER_NAME"] . $_SERVER['PHP_SELF']);
	}
	exit;
}

// ランキングを上げる。
function sfRankUp($table, $colname, $id, $andwhere = "") {
	$objQuery = new SC_Query();
	$objQuery->begin();
	$where = "$colname = ?";
	if($andwhere != "") {
		$where.= " AND $andwhere";
	}
	// 対象項目のランクを取得
	$rank = $objQuery->get($table, "rank", $where, array($id));
	// ランクの最大値を取得
	$maxrank = $objQuery->max($table, "rank", $andwhere);
	// ランクが最大値よりも小さい場合に実行する。
	if($rank < $maxrank) {
		// ランクが一つ上のIDを取得する。
		$where = "rank = ?";
		if($andwhere != "") {
			$where.= " AND $andwhere";
		}
		$uprank = $rank + 1;
		$up_id = $objQuery->get($table, $colname, $where, array($uprank));
		// ランク入れ替えの実行
		$sqlup = "UPDATE $table SET rank = ?, update_date = Now() WHERE $colname = ?";
		$objQuery->exec($sqlup, array($rank + 1, $id));
		$objQuery->exec($sqlup, array($rank, $up_id));
	}
	$objQuery->commit();
}

// ランキングを下げる。
function sfRankDown($table, $colname, $id, $andwhere = "") {
	$objQuery = new SC_Query();
	$objQuery->begin();
	$where = "$colname = ?";
	if($andwhere != "") {
		$where.= " AND $andwhere";
	}
	// 対象項目のランクを取得
	$rank = $objQuery->get($table, "rank", $where, array($id));
		
	// ランクが1(最小値)よりも大きい場合に実行する。
	if($rank > 1) {
		// ランクが一つ下のIDを取得する。
		$where = "rank = ?";
		if($andwhere != "") {
			$where.= " AND $andwhere";
		}
		$downrank = $rank - 1;
		$down_id = $objQuery->get($table, $colname, $where, array($downrank));
		// ランク入れ替えの実行
		$sqlup = "UPDATE $table SET rank = ?, update_date = Now() WHERE $colname = ?";
		$objQuery->exec($sqlup, array($rank - 1, $id));
		$objQuery->exec($sqlup, array($rank, $down_id));
	}
	$objQuery->commit();
}

//----　指定順位へ移動
function sfMoveRank($tableName, $keyIdColumn, $keyId, $pos, $where = "") {
	$objQuery = new SC_Query();
	$objQuery->begin();
		
	// 自身のランクを取得する
	$rank = $objQuery->get($tableName, "rank", "$keyIdColumn = ?", array($keyId));	
	$max = $objQuery->max($tableName, "rank", $where);
		
	// 値の調整（逆順）
	if($pos > $max) {
		$position = 1;
	} else if($pos < 1) {
		$position = $max;
	} else {
		$position = $max - $pos + 1;
	}
	
	if( $position > $rank ) $term = "rank - 1";	//入れ替え先の順位が入れ換え元の順位より大きい場合
	if( $position < $rank ) $term = "rank + 1";	//入れ替え先の順位が入れ換え元の順位より小さい場合

	//--　指定した順位の商品から移動させる商品までのrankを１つずらす
	$sql = "UPDATE $tableName SET rank = $term, update_date = NOW() WHERE rank BETWEEN ? AND ? AND del_flg = 0";
	if($where != "") {
		$sql.= " AND $where";
	}
	
	if( $position > $rank ) $objQuery->exec( $sql, array( $rank + 1, $position ));
	if( $position < $rank ) $objQuery->exec( $sql, array( $position, $rank - 1 ));

	//-- 指定した順位へrankを書き換える。
	$sql  = "UPDATE $tableName SET rank = ?, update_date = NOW() WHERE $keyIdColumn = ? AND del_flg = 0 ";
	if($where != "") {
		$sql.= " AND $where";
	}
	
	$objQuery->exec( $sql, array( $position, $keyId ) );
	$objQuery->commit();
}

// ランクを含むレコードの削除
// レコードごと削除する場合は、$deleteをtrueにする。
function sfDeleteRankRecord($table, $colname, $id, $andwhere = "", $delete = false) {
	$objQuery = new SC_Query();
	$objQuery->begin();
	// 削除レコードのランクを取得する。		
	$where = "$colname = ?";
	if($andwhere != "") {
		$where.= " AND $andwhere";
	}
	$rank = $objQuery->get($table, "rank", $where, array($id));

	if(!$delete) {
		// ランクを最下位にする、DELフラグON
		$sqlup = "UPDATE $table SET rank = 0, del_flg = 1, update_date = Now() ";
		$sqlup.= "WHERE $colname = ?";
		// UPDATEの実行
		$objQuery->exec($sqlup, array($id));
	} else {
		$objQuery->delete($table, "$colname = ?", array($id));
	}
	
	// 追加レコードのランクより上のレコードを一つずらす。
	$where = "rank > ?";
	if($andwhere != "") {
		$where.= " AND $andwhere";
	}
	$sqlup = "UPDATE $table SET rank = (rank - 1) WHERE $where";
	$objQuery->exec($sqlup, array($rank));
	$objQuery->commit();
}

// レコードの存在チェック
function sfIsRecord($table, $col, $arrval, $addwhere = "") {
	$objQuery = new SC_Query();
	$arrCol = split("[, ]", $col);
		
	$where = "del_flg = 0";
	
	if($addwhere != "") {
		$where.= " AND $addwhere";
	}
		
	foreach($arrCol as $val) {
		if($val != "") {
			if($where == "") {
				$where = "$val = ?";
			} else {
				$where.= " AND $val = ?";
			}
		}
	}
	$ret = $objQuery->get($table, $col, $where, $arrval);
	
	if($ret != "") {
		return true;
	}
	return false;
}

// チェックボックスの値をマージ
function sfMergeCBValue($keyname, $max) {
	$conv = "";
	$cnt = 1;
	for($cnt = 1; $cnt <= $max; $cnt++) {
		if ($_POST[$keyname . $cnt] == "1") {
			$conv.= "1";
		} else {
			$conv.= "0";
		}
	}
	return $conv;
}

// html_checkboxesの値をマージして2進数形式に変更する。
function sfMergeCheckBoxes($array, $max) {
	$ret = "";
	if(is_array($array)) {	
		foreach($array as $val) {
			$arrTmp[$val] = "1";
		}
	}
	for($i = 1; $i <= $max; $i++) {	
		if($arrTmp[$i] == "1") {
			$ret.= "1";
		} else {
			$ret.= "0";
		}
	}
	return $ret;
}


// html_checkboxesの値をマージして「-」でつなげる。
function sfMergeParamCheckBoxes($array) {
	if(is_array($array)) {
		foreach($array as $val) {
			if($ret != "") {
				$ret.= "-$val";
			} else {
				$ret = $val;			
			}
		}
	} else {
		$ret = $array;
	}
	return $ret;
}

// html_checkboxesの値をマージしてSQL検索用に変更する。
function sfSearchCheckBoxes($array) {
	$max = 0;
	$ret = "";
	foreach($array as $val) {
		$arrTmp[$val] = "1";
		if($val > $max) {
			$max = $val;
		}
	}
	for($i = 1; $i <= $max; $i++) {	
		if($arrTmp[$i] == "1") {
			$ret.= "1";
		} else {
			$ret.= "_";
		}
	}
	
	if($ret != "") {	
		$ret.= "%";
	}
	return $ret;
}

// 2進数形式の値をhtml_checkboxes対応の値に切り替える
function sfSplitCheckBoxes($val) {
	$len = strlen($val);
	for($i = 0; $i < $len; $i++) {
		if(substr($val, $i, 1) == "1") {
			$arrRet[] = ($i + 1);
		}
	}
	return $arrRet;
}

// チェックボックスの値をマージ
function sfMergeCBSearchValue($keyname, $max) {
	$conv = "";
	$cnt = 1;
	for($cnt = 1; $cnt <= $max; $cnt++) {
		if ($_POST[$keyname . $cnt] == "1") {
			$conv.= "1";
		} else {
			$conv.= "_";
		}
	}
	return $conv;
}

// チェックボックスの値を分解
function sfSplitCBValue($val, $keyname = "") {
	$len = strlen($val);
	$no = 1;
	for ($cnt = 0; $cnt < $len; $cnt++) {
		if($keyname != "") {
			$arr[$keyname . $no] = substr($val, $cnt, 1);
		} else {
			$arr[] = substr($val, $cnt, 1);
		}
		$no++;
	}
	return $arr;
}

// キーと値をセットした配列を取得
function sfArrKeyValue($arrList, $keyname, $valname, $len_max = "", $keysize = "") {
	
	$max = count($arrList);
	
	if($len_max != "" && $max > $len_max) {
		$max = $len_max;
	}
	
	for($cnt = 0; $cnt < $max; $cnt++) {
		if($keysize != "") {
			$key = sfCutString($arrList[$cnt][$keyname], $keysize);
		} else {
			$key = $arrList[$cnt][$keyname];
		}
		$val = $arrList[$cnt][$valname];
		
		if(!isset($arrRet[$key])) {
			$arrRet[$key] = $val;
		}
		
	}
	return $arrRet;
}

// キーと値をセットした配列を取得(値が複数の場合)
function sfArrKeyValues($arrList, $keyname, $valname, $len_max = "", $keysize = "", $connect = "") {
	
	$max = count($arrList);
	
	if($len_max != "" && $max > $len_max) {
		$max = $len_max;
	}
	
	for($cnt = 0; $cnt < $max; $cnt++) {
		if($keysize != "") {
			$key = sfCutString($arrList[$cnt][$keyname], $keysize);
		} else {
			$key = $arrList[$cnt][$keyname];
		}
		$val = $arrList[$cnt][$valname];
		
		if($connect != "") {
			$arrRet[$key].= "$val".$connect;
		} else {
			$arrRet[$key][] = $val;		
		}
	}
	return $arrRet;
}

// 配列の値をカンマ区切りで返す。
function sfGetCommaList($array) {
	if (count($array) > 0) {
		foreach($array as $val) {
			$line .= $val . ", ";
		}
		$line = ereg_replace(", $", "", $line);
		return $line;
	}else{
		return false;
	}
	
}

/* 配列の要素をCSVフォーマットで出力する。*/
function sfGetCSVList($array) {
	if (count($array) > 0) {
		foreach($array as $key => $val) {
			$line .= "\"".$val."\",";
		}
		$line = ereg_replace(",$", "\n", $line);
		return $line;
	}else{
		return false;
	}
}

/* 配列の要素をPDFフォーマットで出力する。*/
function sfGetPDFList($array) {
	foreach($array as $key => $val) {
		$line .= "\t".$val;
	}
	$line.="\n";
	return $line;
}



/*-----------------------------------------------------------------*/
/*	check_set_term
/*	年月日に別れた2つの期間の妥当性をチェックし、整合性と期間を返す
/*　引数 (開始年,開始月,開始日,終了年,終了月,終了日)
/*　戻値 array(１，２，３）
/*  		１．開始年月日 (YYYY/MM/DD 000000)
/*			２．終了年月日 (YYYY/MM/DD 235959)
/*			３．エラー ( 0 = OK, 1 = NG )
/*-----------------------------------------------------------------*/
function sfCheckSetTerm ( $start_year, $start_month, $start_day, $end_year, $end_month, $end_day ) {

	// 期間指定
	$error = 0;
	if ( $start_month || $start_day || $start_year){
		if ( ! checkdate($start_month, $start_day , $start_year) ) $error = 1;
	} else {
		$error = 1;
	}
	if ( $end_month || $end_day || $end_year){
		if ( ! checkdate($end_month ,$end_day ,$end_year) ) $error = 2;
	}
	if ( ! $error ){
		$date1 = $start_year ."/".sprintf("%02d",$start_month) ."/".sprintf("%02d",$start_day) ." 000000";
		$date2 = $end_year   ."/".sprintf("%02d",$end_month)   ."/".sprintf("%02d",$end_day)   ." 235959";
		if ($date1 > $date2) $error = 3;
	} else {
		$error = 1;
	}
	return array($date1, $date2, $error);
}

// エラー箇所の背景色を変更するためのfunction SC_Viewで読み込む
function sfSetErrorStyle(){
	return 'style="background-color:'.ERR_COLOR.'"';
}

/* DBに渡す数値のチェック
 * 10桁以上はオーバーフローエラーを起こすので。
 */
function sfCheckNumLength( $value ){
	if ( ! is_numeric($value)  ){
		return false;
	} 
	
	if ( strlen($value) > 9 ) {
		return false;
	}
	
	return true;
}

// 一致した値のキー名を取得
function sfSearchKey($array, $word, $default) {
	foreach($array as $key => $val) {
		if($val == $word) {
			return $key;
		}
	}
	return $default;
}

// カテゴリツリーの取得($products_check:true商品登録済みのものだけ取得)
function sfGetCategoryList($addwhere = "", $products_check = false, $head = CATEGORY_HEAD) {
	$objQuery = new SC_Query();
	$where = "del_flg = 0";
	
	if($addwhere != "") {
		$where.= " AND $addwhere";
	}
		
	$objQuery->setoption("ORDER BY rank DESC");
	
	if($products_check) {
		$col = "T1.category_id, category_name, level";
		$from = "dtb_category AS T1 LEFT JOIN dtb_category_total_count AS T2 ON T1.category_id = T2.category_id";
		$where .= " AND product_count > 0";
	} else {
		$col = "category_id, category_name, level";
		$from = "dtb_category";
	}
	
	$arrRet = $objQuery->select($col, $from, $where);
			
	$max = count($arrRet);
	for($cnt = 0; $cnt < $max; $cnt++) {
		$id = $arrRet[$cnt]['category_id'];
		$name = $arrRet[$cnt]['category_name'];
		$arrList[$id] = "";
		/*
		for($n = 1; $n < $arrRet[$cnt]['level']; $n++) {
			$arrList[$id].= "　";
		}
		*/
		for($cat_cnt = 0; $cat_cnt < $arrRet[$cnt]['level']; $cat_cnt++) {
			$arrList[$id].= $head;
		}
		$arrList[$id].= $name;
	}
	return $arrList;
}

// カテゴリツリーの取得（親カテゴリのValue:0)
function sfGetLevelCatList($parent_zero = true) {
	$objQuery = new SC_Query();
	$col = "category_id, category_name, level";
	$where = "del_flg = 0";
	$objQuery->setoption("ORDER BY rank DESC");
	$arrRet = $objQuery->select($col, "dtb_category", $where);
	$max = count($arrRet);
	
	for($cnt = 0; $cnt < $max; $cnt++) {
		if($parent_zero) {
			if($arrRet[$cnt]['level'] == LEVEL_MAX) {
				$arrValue[$cnt] = $arrRet[$cnt]['category_id'];
			} else {
				$arrValue[$cnt] = ""; 
			}
		} else {
			$arrValue[$cnt] = $arrRet[$cnt]['category_id'];
		}
		
		$arrOutput[$cnt] = "";
		/*	 		
		for($n = 1; $n < $arrRet[$cnt]['level']; $n++) {
			$arrOutput[$cnt].= "　";
		}
		*/
		for($cat_cnt = 0; $cat_cnt < $arrRet[$cnt]['level']; $cat_cnt++) {
			$arrOutput[$cnt].= CATEGORY_HEAD;
		}
		$arrOutput[$cnt].= $arrRet[$cnt]['category_name'];
	}
	return array($arrValue, $arrOutput);
}

function sfGetErrorColor($val) {
	if($val != "") {
		return "background-color:" . ERR_COLOR;
	}
	return "";
}


function sfGetEnabled($val) {
	if( ! $val ) {
		return " disabled=\"disabled\"";
	}
	return "";
}

function sfGetChecked($param, $value) {
	if($param == $value) {
		return "checked=\"checked\"";
	}
	return "";
}

// SELECTボックス用リストの作成
function sfGetIDValueList($table, $keyname, $valname) {
	$objQuery = new SC_Query();
	$col = "$keyname, $valname";
	$objQuery->setwhere("del_flg = 0");
	$objQuery->setorder("rank DESC");
	$arrList = $objQuery->select($col, $table);
	$count = count($arrList);
	for($cnt = 0; $cnt < $count; $cnt++) {
		$key = $arrList[$cnt][$keyname];
		$val = $arrList[$cnt][$valname];
		$arrRet[$key] = $val;
	}
	return $arrRet;
}

function sfTrim($str) {
	$ret = ereg_replace("^[　 \n\r]*", "", $str);
	$ret = ereg_replace("[　 \n\r]*$", "", $ret);
	return $ret;
}

/* 所属するすべての階層の親IDを配列で返す */
function sfGetParents($objQuery, $table, $pid_name, $id_name, $id) {
	$arrRet = sfGetParentsArray($table, $pid_name, $id_name, $id);
	// 配列の先頭1つを削除する。
	array_shift($arrRet);
	return $arrRet;
}


/* 親IDの配列を元に特定のカラムを取得する。*/
function sfGetParentsCol($objQuery, $table, $id_name, $col_name, $arrId ) {
	$col = $col_name;
	$len = count($arrId);
	$where = "";
	
	for($cnt = 0; $cnt < $len; $cnt++) {
		if($where == "") {
			$where = "$id_name = ?";
		} else {
			$where.= " OR $id_name = ?";
		}
	}
	
	$objQuery->setorder("level");
	$arrRet = $objQuery->select($col, $table, $where, $arrId);
	return $arrRet;	
}

/* 子IDの配列を返す */
function sfGetChildsID($table, $pid_name, $id_name, $id) {
	$arrRet = sfGetChildrenArray($table, $pid_name, $id_name, $id);
	return $arrRet;
}

/* カテゴリ変更時の移動処理 */
function sfMoveCatRank($objQuery, $table, $id_name, $cat_name, $old_catid, $new_catid, $id) {
	if ($old_catid == $new_catid) {
		return;
	}
	// 旧カテゴリでのランク削除処理
	// 移動レコードのランクを取得する。		
	$where = "$id_name = ?";
	$rank = $objQuery->get($table, "rank", $where, array($id));
	// 削除レコードのランクより上のレコードを一つ下にずらす。
	$where = "rank > ? AND $cat_name = ?";
	$sqlup = "UPDATE $table SET rank = (rank - 1) WHERE $where";
	$objQuery->exec($sqlup, array($rank, $old_catid));
	// 新カテゴリでの登録処理
	// 新カテゴリの最大ランクを取得する。
	$max_rank = $objQuery->max($table, "rank", "$cat_name = ?", array($new_catid)) + 1;
	$where = "$id_name = ?";
	$sqlup = "UPDATE $table SET rank = ? WHERE $where";
	$objQuery->exec($sqlup, array($max_rank, $id));
}

/* 税金計算 */
function sfTax($price, $tax, $tax_rule) {
	$real_tax = $tax / 100;
	$ret = $price * $real_tax;
	switch($tax_rule) {
	// 四捨五入
	case 1:
		$ret = round($ret);
		break;
	// 切り捨て
	case 2:
		$ret = floor($ret);
		break;
	// 切り上げ
	case 3:
		$ret = ceil($ret);
		break;
	// デフォルト:切り上げ
	default:
		$ret = ceil($ret);
		break;
	}
	return $ret;
}

/* 税金付与 */
function sfPreTax($price, $tax, $tax_rule) {
	$real_tax = $tax / 100;
	$ret = $price * (1 + $real_tax);
	switch($tax_rule) {
	// 四捨五入
	case 1:
		$ret = round($ret);
		break;
	// 切り捨て
	case 2:
		$ret = floor($ret);
		break;
	// 切り上げ
	case 3:
		$ret = ceil($ret);
		break;
	// デフォルト:切り上げ
	default:
		$ret = ceil($ret);
		break;
	}
	return $ret;
}

/* ポイント付与 */
function sfPrePoint($price, $point_rate, $rule = POINT_RULE, $product_id = "") {
	if(sfIsInt($product_id)) {
		$objQuery = new SC_Query();
		$where = "to_char(now(),'YYYY/MM/DD/HH24') >= to_char(start_date,'YYYY/MM/DD/HH24') AND ";
		$where .= "to_char(now(),'YYYY/MM/DD/HH24') < to_char(end_date,'YYYY/MM/DD/HH24') AND ";
		$where .= "del_flg = 0 AND campaign_id IN (SELECT campaign_id FROM dtb_campaign_detail where product_id = ? )";
		//登録(更新)日付順
		$objQuery->setorder('update_date DESC');
		//キャンペーンポイントの取得
		$arrRet = $objQuery->select("campaign_name, campaign_point_rate", "dtb_campaign", $where, array($product_id));
	}
	//複数のキャンペーンに登録されている商品は、最新のキャンペーンからポイントを取得
	if($arrRet[0]['campaign_point_rate'] != "") {
		$campaign_point_rate = $arrRet[0]['campaign_point_rate'];
		$real_point = $campaign_point_rate / 100;
	} else {
		$real_point = $point_rate / 100;
	}
	$ret = $price * $real_point;
	switch($rule) {
	// 四捨五入
	case 1:
		$ret = round($ret);
		break;
	// 切り捨て
	case 2:
		$ret = floor($ret);
		break;
	// 切り上げ
	case 3:
		$ret = ceil($ret);
		break;
	// デフォルト:切り上げ
	default:
		$ret = ceil($ret);
		break;
	}
	//キャンペーン商品の場合
	if($campaign_point_rate != "") {
		$ret = "(".$arrRet[0]['campaign_name']."ポイント率".$campaign_point_rate."%)".$ret;
	}
	return $ret;
}

/* 規格分類の件数取得 */
function sfGetClassCatCount() {
	$sql = "select count(dtb_class.class_id) as count, dtb_class.class_id ";
	$sql.= "from dtb_class inner join dtb_classcategory on dtb_class.class_id = dtb_classcategory.class_id ";
	$sql.= "where dtb_class.del_flg = 0 AND dtb_classcategory.del_flg = 0 ";
	$sql.= "group by dtb_class.class_id, dtb_class.name";
	$objQuery = new SC_Query();
	$arrList = $objQuery->getall($sql);
	// キーと値をセットした配列を取得
	$arrRet = sfArrKeyValue($arrList, 'class_id', 'count');
	
	return $arrRet;
}

/* 規格の登録 */
function sfInsertProductClass($objQuery, $arrList, $product_id) {
	// すでに規格登録があるかどうかをチェックする。
	$where = "product_id = ? AND classcategory_id1 <> 0 AND classcategory_id1 <> 0";
	$count = $objQuery->count("dtb_products_class", $where,  array($product_id));
	
	// すでに規格登録がない場合
	if($count == 0) {
		// 既存規格の削除
		$where = "product_id = ?";
		$objQuery->delete("dtb_products_class", $where, array($product_id));
		$sqlval['product_id'] = $product_id;
		$sqlval['classcategory_id1'] = '0';
		$sqlval['classcategory_id2'] = '0';
		$sqlval['product_code'] = $arrList["product_code"];
		$sqlval['stock'] = $arrList["stock"];
		$sqlval['stock_unlimited'] = $arrList["stock_unlimited"];
		$sqlval['price01'] = $arrList['price01'];
		$sqlval['price02'] = $arrList['price02'];
		$sqlval['creator_id'] = $_SESSION['member_id'];
		
		if($_SESSION['member_id'] == "") {
			$sqlval['creator_id'] = '0';
		}
		
		// INSERTの実行
		$objQuery->insert("dtb_products_class", $sqlval);
	}
}

function sfGetProductClassId($product_id, $classcategory_id1, $classcategory_id2) {
	$where = "product_id = ? AND classcategory_id1 = ? AND classcategory_id2 = ?";
	$objQuery = new SC_Query();
	$ret = $objQuery->get("dtb_products_class", "product_class_id", $where, Array($product_id, $classcategory_id1, $classcategory_id2));
	return $ret;
}

/* 文末の「/」をなくす */
function sfTrimURL($url) {
	$ret = ereg_replace("[/]+$", "", $url);
	return $ret;
}

/* 商品規格情報の取得 */
function sfGetProductsClass($arrID) {
	list($product_id, $classcategory_id1, $classcategory_id2) = $arrID;	
	
	if($classcategory_id1 == "") {
		$classcategory_id1 = '0';
	}
	if($classcategory_id2 == "") {
		$classcategory_id2 = '0';
	}
		
	// 商品規格取得
	$objQuery = new SC_Query();
	$col = "product_id, deliv_fee, name, product_code, main_list_image, main_image, price01, price02, point_rate, product_class_id, classcategory_id1, classcategory_id2, class_id1, class_id2, stock, stock_unlimited, sale_limit, sale_unlimited";
	$table = "vw_product_class";
	$where = "product_id = ? AND classcategory_id1 = ? AND classcategory_id2 = ?";
	$objQuery->setorder("rank1 DESC, rank2 DESC");
	$arrRet = $objQuery->select($col, $table, $where, array($product_id, $classcategory_id1, $classcategory_id2));
	return $arrRet[0];
}

/* 集計情報を元に最終計算 */
function sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo, $objCustomer = "") {
	// 商品の合計個数
	$total_quantity = $objCartSess->getTotalQuantity(true);
	
	// 税金の取得
	$arrData['tax'] = $objPage->tpl_total_tax;
	// 小計の取得
	$arrData['subtotal'] = $objPage->tpl_total_pretax;	
	
	// 合計送料の取得
	$arrData['deliv_fee'] = 0;
		
	// 商品ごとの送料が有効の場合
	if (OPTION_PRODUCT_DELIV_FEE == 1) {
		$arrData['deliv_fee']+= $objCartSess->getAllProductsDelivFee();
	}
	
	// 配送業者の送料が有効の場合
	if (OPTION_DELIV_FEE == 1) {
		// 送料の合計を計算する
		$arrData['deliv_fee']+= sfGetDelivFee($arrData['deliv_pref'], $arrData['payment_id']);
	}
	
	// 送料無料の購入数が設定されている場合
	if(DELIV_FREE_AMOUNT > 0) {
		if($total_quantity >= DELIV_FREE_AMOUNT) {
			$arrData['deliv_fee'] = 0;
		}	
	}
		
	// 送料無料条件が設定されている場合
	if($arrInfo['free_rule'] > 0) {
		// 小計が無料条件を超えている場合
		if($arrData['subtotal'] >= $arrInfo['free_rule']) {
			$arrData['deliv_fee'] = 0;
		}
	}

	// 合計の計算
	$arrData['total'] = $objPage->tpl_total_pretax;	// 商品合計
	$arrData['total']+= $arrData['deliv_fee'];		// 送料
	$arrData['total']+= $arrData['charge'];			// 手数料
	// お支払い合計
	$arrData['payment_total'] = $arrData['total'] - ($arrData['use_point'] * POINT_VALUE);
	// 加算ポイントの計算
	$arrData['add_point'] = sfGetAddPoint($objPage->tpl_total_point, $arrData['use_point'], $arrInfo);
	
	if($objCustomer != "") {
		// 誕生日月であった場合
		if($objCustomer->isBirthMonth()) {
			$arrData['birth_point'] = BIRTH_MONTH_POINT;
			$arrData['add_point'] += $arrData['birth_point'];
		}
	}
	
	if($arrData['add_point'] < 0) {
		$arrData['add_point'] = 0;
	}
	
	return $arrData;
}

/* カート内商品の集計処理 */
function sfTotalCart($objPage, $objCartSess, $arrInfo) {
	// 規格名一覧
	$arrClassName = sfGetIDValueList("dtb_class", "class_id", "name");
	// 規格分類名一覧
	$arrClassCatName = sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
	
	$objPage->tpl_total_pretax = 0;		// 費用合計(税込み)
	$objPage->tpl_total_tax = 0;		// 消費税合計
	$objPage->tpl_total_point = 0;		// ポイント合計
	
	// カート内情報の取得
	$arrCart = $objCartSess->getCartList();
	$max = count($arrCart);
	$cnt = 0;

	for ($i = 0; $i < $max; $i++) {
		// 商品規格情報の取得	
		$arrData = sfGetProductsClass($arrCart[$i]['id']);
				
		// DBに存在する商品
		if (count($arrData) > 0) {
			
			// 購入制限数を求める。			
			if ($arrData['stock_unlimited'] != '1' && $arrData['sale_unlimited'] != '1') {
				if($arrData['sale_limit'] < $arrData['stock']) {
					$limit = $arrData['sale_limit'];
				} else {
					$limit = $arrData['stock'];
				}
			} else {
				if ($arrData['sale_unlimited'] != '1') {
					$limit = $arrData['sale_limit'];
				}
				if ($arrData['stock_unlimited'] != '1') {
					$limit = $arrData['stock'];
				}
			}
						
			if($limit != "" && $limit < $arrCart[$i]['quantity']) {
				// カート内商品数を制限に合わせる
				$objCartSess->setProductValue($arrCart[$i]['id'], 'quantity', $limit);
				$quantity = $limit;
				$objPage->tpl_message = "※「" . $arrData['name'] . "」は販売制限しております、一度にこれ以上の購入はできません。";
			} else {
				$quantity = $arrCart[$i]['quantity'];
			}
			
			$objPage->arrProductsClass[$cnt] = $arrData;
			$objPage->arrProductsClass[$cnt]['quantity'] = $quantity;
			$objPage->arrProductsClass[$cnt]['cart_no'] = $arrCart[$i]['cart_no'];
			$objPage->arrProductsClass[$cnt]['class_name1'] = $arrClassName[$arrData['class_id1']];
			$objPage->arrProductsClass[$cnt]['class_name2'] = $arrClassName[$arrData['class_id2']];
			$objPage->arrProductsClass[$cnt]['classcategory_name1'] = $arrClassCatName[$arrData['classcategory_id1']];
			$objPage->arrProductsClass[$cnt]['classcategory_name2'] = $arrClassCatName[$arrData['classcategory_id2']];
			
			// 価格の登録
			if ($arrData['price02'] != "") {
				$objCartSess->setProductValue($arrCart[$i]['id'], 'price', $arrData['price02']);
				$objPage->arrProductsClass[$cnt]['uniq_price'] = $arrData['price02'];
			} else {
				$objCartSess->setProductValue($arrCart[$i]['id'], 'price', $arrData['price01']);
				$objPage->arrProductsClass[$cnt]['uniq_price'] = $arrData['price01'];
			}
			// ポイント付与率の登録
			$objCartSess->setProductValue($arrCart[$i]['id'], 'point_rate', $arrData['point_rate']);
			// 商品ごとの合計金額
			$objPage->arrProductsClass[$cnt]['total_pretax'] = $objCartSess->getProductTotal($arrInfo, $arrCart[$i]['id']);
			// 送料の合計を計算する
			$objPage->tpl_total_deliv_fee+= ($arrData['deliv_fee'] * $arrCart[$i]['quantity']);
			$cnt++;
		} else {
			// DBに商品が見つからない場合はカート商品の削除
			$objCartSess->delProductKey('id', $arrCart[$i]['id']);
		}
	}
	
	// 全商品合計金額(税込み)
	$objPage->tpl_total_pretax = $objCartSess->getAllProductsTotal($arrInfo);
	// 全商品合計消費税
	$objPage->tpl_total_tax = $objCartSess->getAllProductsTax($arrInfo);
	// 全商品合計ポイント
	$objPage->tpl_total_point = $objCartSess->getAllProductsPoint();
	
	return $objPage;	
}

/* DBから取り出した日付の文字列を調整する。*/
function sfDispDBDate($dbdate, $time = true) {
	list($y, $m, $d, $H, $M) = split("[- :]", $dbdate);

	if(strlen($y) > 0 && strlen($m) > 0 && strlen($d) > 0) {
		if ($time) {
			$str = sprintf("%04d/%02d/%02d %02d:%02d", $y, $m, $d, $H, $M);
		} else {
			$str = sprintf("%04d/%02d/%02d", $y, $m, $d, $H, $M);						
		}
	} else {
		$str = "";
	}
	return $str;
}

function sfGetDelivTime($payment_id = "") {
	$objQuery = new SC_Query();
	
	$deliv_id = "";
	
	if($payment_id != "") {
		$where = "del_flg = 0 AND payment_id = ?";
		$arrRet = $objQuery->select("deliv_id", "dtb_payment", $where, array($payment_id));
		$deliv_id = $arrRet[0]['deliv_id'];
	}
	
	if($deliv_id != "") {
		$objQuery->setorder("time_id");
		$where = "deliv_id = ?";
		$arrRet= $objQuery->select("time_id, time", "dtb_delivtime", $where, array($deliv_id));
	}
	return $arrRet;	
}


// 都道府県、支払い方法から配送料金を取得する
function sfGetDelivFee($pref, $payment_id = "") {
	$objQuery = new SC_Query();
	
	$deliv_id = "";
	
	// 支払い方法が指定されている場合は、対応した配送業者を取得する
	if($payment_id != "") {
		$where = "del_flg = 0 AND payment_id = ?";
		$arrRet = $objQuery->select("deliv_id", "dtb_payment", $where, array($payment_id));
		$deliv_id = $arrRet[0]['deliv_id'];
	// 支払い方法が指定されていない場合は、先頭の配送業者を取得する
	} else {
		$where = "del_flg = 0";
		$objQuery->setOrder("rank DESC");
		$objQuery->setLimitOffset(1);
		$arrRet = $objQuery->select("deliv_id", "dtb_deliv", $where);
		$deliv_id = $arrRet[0]['deliv_id'];	
	}
	
	// 配送業者から配送料を取得
	if($deliv_id != "") {
		
		// 都道府県が指定されていない場合は、東京都の番号を指定しておく
		if($pref == "") {
			$pref = 13;
		}
		
		$objQuery = new SC_Query();
		$where = "deliv_id = ? AND pref = ?";
		$arrRet= $objQuery->select("fee", "dtb_delivfee", $where, array($deliv_id, $pref));
	}	
	return $arrRet[0]['fee'];	
}

/* 支払い方法の取得 */
function sfGetPayment() {
	$objQuery = new SC_Query();
	// 購入金額が条件額以下の項目を取得
	$where = "del_flg = 0";
	$objQuery->setorder("fix, rank DESC");
	$arrRet = $objQuery->select("payment_id, payment_method, rule", "dtb_payment", $where);
	return $arrRet;	
}

/* 配列をキー名ごとの配列に変更する */
function sfSwapArray($array) {
	$max = count($array);
	for($i = 0; $i < $max; $i++) {
		foreach($array[$i] as $key => $val) {
			$arrRet[$key][] = $val;
		}
	}
	return $arrRet;
}

/* かけ算をする（Smarty用) */
function sfMultiply($num1, $num2) {
	return ($num1 * $num2);
}

/* DBに登録されたテンプレートメールの送信 */
function sfSendTemplateMail($to, $to_name, $template_id, $objPage) {
	global $arrMAILTPLPATH;
	$objQuery = new SC_Query();
	// メールテンプレート情報の取得
	$where = "template_id = ?";
	$arrRet = $objQuery->select("subject, header, footer", "dtb_mailtemplate", $where, array($template_id));
	$objPage->tpl_header = $arrRet[0]['header'];
	$objPage->tpl_footer = $arrRet[0]['footer'];
	$tmp_subject = $arrRet[0]['subject'];
	
	$objSiteInfo = new SC_SiteInfo();
	$arrInfo = $objSiteInfo->data;
	
	$objMailView = new SC_SiteView();
	// メール本文の取得
	$objMailView->assignobj($objPage);
	$body = $objMailView->fetch($arrMAILTPLPATH[$template_id]);
	
	// メール送信処理
	$objSendMail = new GC_SendMail();
	$from = $arrInfo['email03'];
	$error = $arrInfo['email04'];
	$tosubject = $tmp_subject;
	$objSendMail->setItem('', $tosubject, $body, $from, $arrInfo['shop_name'], $from, $error, $error);
	$objSendMail->setTo($to, $to_name);
	$objSendMail->sendMail();	// メール送信
}

/* 受注完了メール送信 */
function sfSendOrderMail($order_id, $template_id, $subject = "", $header = "", $footer = "", $send = true) {
	global $arrMAILTPLPATH;
	
	$objPage = new LC_Page();
	$objSiteInfo = new SC_SiteInfo();
	$arrInfo = $objSiteInfo->data;
	$objPage->arrInfo = $arrInfo;
	
	$objQuery = new SC_Query();
		
	if($subject == "" && $header == "" && $footer == "") {
		// メールテンプレート情報の取得
		$where = "template_id = ?";
		$arrRet = $objQuery->select("subject, header, footer", "dtb_mailtemplate", $where, array('1'));
		$objPage->tpl_header = $arrRet[0]['header'];
		$objPage->tpl_footer = $arrRet[0]['footer'];
		$tmp_subject = $arrRet[0]['subject'];
	} else {
		$objPage->tpl_header = $header;
		$objPage->tpl_footer = $footer;
		$tmp_subject = $subject;
	}
	
	// 受注情報の取得
	$where = "order_id = ?";
	$arrRet = $objQuery->select("*", "dtb_order", $where, array($order_id));
	$arrOrder = $arrRet[0];
	$arrOrderDetail = $objQuery->select("*", "dtb_order_detail", $where, array($order_id));
	
	$objPage->Message_tmp = $arrOrder['message'];
		
	// 顧客情報の取得
	$customer_id = $arrOrder['customer_id'];
	$arrRet = $objQuery->select("point", "dtb_customer", "customer_id = ?", array($customer_id));
	$arrCustomer = $arrRet[0];

	$objPage->arrCustomer = $arrCustomer;
	$objPage->arrOrder = $arrOrder;

	//コンビニ決済情報
	if($arrOrder['conveni_data'] != "") {
		global $arrCONVENIENCE;
		global $arrCONVENIMESSAGE;
		$objPage->arrCONVENIENCE = $arrCONVENIENCE;
		$objPage->arrCONVENIMESSAGE = $arrCONVENIMESSAGE;
		$arrConv = unserialize($arrOrder['conveni_data']);
		$objPage->arrConv = $arrConv;
	}

	// 都道府県変換
	global $arrPref;
	$objPage->arrOrder['deliv_pref'] = $arrPref[$objPage->arrOrder['deliv_pref']];
	
	$objPage->arrOrderDetail = $arrOrderDetail;
	
	$objCustomer = new SC_Customer();
	$objPage->tpl_user_point = $objCustomer->getValue('point');
	
	$objMailView = new SC_SiteView();
	// メール本文の取得
	$objMailView->assignobj($objPage);
	$body = $objMailView->fetch($arrMAILTPLPATH[$template_id]);
	
	// メール送信処理
	$objSendMail = new GC_SendMail();
	$bcc = $arrInfo['email01'];
	$from = $arrInfo['email03'];
	$error = $arrInfo['email04'];
	
	$tosubject = sfMakeSubject($tmp_subject);
	
	$objSendMail->setItem('', $tosubject, $body, $from, $arrInfo['shop_name'], $from, $error, $error, $bcc);
	$objSendMail->setTo($arrOrder["order_email"], $arrOrder["order_name01"] . " ". $arrOrder["order_name02"] ." 様");


	// 送信フラグ:trueの場合は、送信する。
	if($send) {
		if ($objSendMail->sendMail()) {
			sfSaveMailHistory($order_id, $template_id, $tosubject, $body);
		}
	}

	return $objSendMail;
}

// テンプレートを使用したメールの送信
function sfSendTplMail($to, $subject, $tplpath, $objPage) {
	$objMailView = new SC_SiteView();
	$objSiteInfo = new SC_SiteInfo();
	$arrInfo = $objSiteInfo->data;
	// メール本文の取得
	$objPage->tpl_shopname=$arrInfo['shop_name'];
	$objPage->tpl_infoemail = $arrInfo['email02'];
	$objMailView->assignobj($objPage);
	$body = $objMailView->fetch($tplpath);
	// メール送信処理
	$objSendMail = new GC_SendMail();
	$to = mb_encode_mimeheader($to);
	$bcc = $arrInfo['email01'];
	$from = $arrInfo['email03'];
	$error = $arrInfo['email04'];
	$objSendMail->setItem($to, $subject, $body, $from, $arrInfo['shop_name'], $from, $error, $error, $bcc);
	$objSendMail->sendMail();	
}

// 通常のメール送信
function sfSendMail($to, $subject, $body) {
	$objSiteInfo = new SC_SiteInfo();
	$arrInfo = $objSiteInfo->data;
	// メール送信処理
	$objSendMail = new GC_SendMail();
	$bcc = $arrInfo['email01'];
	$from = $arrInfo['email03'];
	$error = $arrInfo['email04'];
	$objSendMail->setItem($to, $subject, $body, $from, $arrInfo['shop_name'], $from, $error, $error, $bcc);
	$objSendMail->sendMail();
}

//件名にテンプレートを用いる
function sfMakeSubject($subject){
	
	$objQuery = new SC_Query();
	$objMailView = new SC_SiteView();
	$objPage = new LC_Page();
	
	$arrInfo = $objQuery->select("*","dtb_baseinfo");
	$arrInfo = $arrInfo[0];
	$objPage->tpl_shopname=$arrInfo['shop_name'];
	$objPage->tpl_infoemail=$subject;
	$objMailView->assignobj($objPage);
	$mailtitle = $objMailView->fetch('mail_templates/mail_title.tpl');
	$ret = $mailtitle.$subject;
	return $ret; 
}

// メール配信履歴への登録
function sfSaveMailHistory($order_id, $template_id, $subject, $body) {
	$sqlval['subject'] = $subject;
	$sqlval['order_id'] = $order_id;
	$sqlval['template_id'] = $template_id;
	$sqlval['send_date'] = "Now()";
	if($_SESSION['member_id'] != "") {
		$sqlval['creator_id'] = $_SESSION['member_id'];
	} else {
		$sqlval['creator_id'] = '0';
	}
	$sqlval['mail_body'] = $body;
	
	$objQuery = new SC_Query();
	$objQuery->insert("dtb_mail_history", $sqlval);
}

/* 会員情報を一時受注テーブルへ */
function sfGetCustomerSqlVal($uniqid, $sqlval) {
	$objCustomer = new SC_Customer();
	// 会員情報登録処理
	if ($objCustomer->isLoginSuccess()) {
		// 登録データの作成
		$sqlval['order_temp_id'] = $uniqid;
		$sqlval['update_date'] = 'Now()';
		$sqlval['customer_id'] = $objCustomer->getValue('customer_id');
	    $sqlval['order_name01'] = $objCustomer->getValue('name01');
	    $sqlval['order_name02'] = $objCustomer->getValue('name02');
	    $sqlval['order_kana01'] = $objCustomer->getValue('kana01');
	    $sqlval['order_kana02'] = $objCustomer->getValue('kana02');
	    $sqlval['order_sex'] = $objCustomer->getValue('sex');
	    $sqlval['order_zip01'] = $objCustomer->getValue('zip01');
	    $sqlval['order_zip02'] = $objCustomer->getValue('zip02');
	    $sqlval['order_pref'] = $objCustomer->getValue('pref');
	    $sqlval['order_addr01'] = $objCustomer->getValue('addr01');
	    $sqlval['order_addr02'] = $objCustomer->getValue('addr02');
	    $sqlval['order_tel01'] = $objCustomer->getValue('tel01');
	    $sqlval['order_tel02'] = $objCustomer->getValue('tel02');
		$sqlval['order_tel03'] = $objCustomer->getValue('tel03');
		$sqlval['order_email'] = $objCustomer->getValue('email');
		$sqlval['order_job'] = $objCustomer->getValue('job');
		$sqlval['order_birth'] = $objCustomer->getValue('birth');
	}
	return $sqlval;
}

// 受注一時テーブルへの書き込み処理
function sfRegistTempOrder($uniqid, $sqlval) {
	if($uniqid != "") {
		// 既存データのチェック
		$objQuery = new SC_Query();
		$where = "order_temp_id = ?";
		$cnt = $objQuery->count("dtb_order_temp", $where, array($uniqid));
		// 既存データがない場合
		if ($cnt == 0) {
			// 初回書き込み時に会員の登録済み情報を取り込む
			$sqlval = sfGetCustomerSqlVal($uniqid, $sqlval);
			$objQuery->insert("dtb_order_temp", $sqlval);
		} else {
			$objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
		}
	}
}

/* 会員のメルマガ登録があるかどうかのチェック(仮会員を含まない) */
function sfCheckCustomerMailMaga($email) {
	$col = "T1.email, T1.mail_flag, T2.customer_id";
	$from = "dtb_customer_mail AS T1 LEFT JOIN dtb_customer AS T2 ON T1.email = T2.email";
	$where = "T1.email = ? AND T2.status = 2";
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select($col, $from, $where, array($email));
	// 会員のメールアドレスが登録されている
	if($arrRet[0]['customer_id'] != "") {
		return true;
	}
	return false;
}

// カードの処理結果を返す
function sfGetAuthonlyResult($dir, $file_name, $name01, $name02, $card_no, $card_exp, $amount, $order_id, $jpo_info = "10"){

	$path = $dir .$file_name;		// cgiファイルのフルパス生成
	$now_dir = getcwd();			// requireがうまくいかないので、cgi実行ディレクトリに移動する
	chdir($dir);
	
	// パイプ渡しでコマンドラインからcgi起動
	$cmd = "$path card_no=$card_no name01=$name01 name02=$name02 card_exp=$card_exp amount=$amount order_id=$order_id jpo_info=$jpo_info";

	$tmpResult = popen($cmd, "r");
	
	// 結果取得
	while( ! FEOF ( $tmpResult ) ) {
		$result .= FGETS($tmpResult);
	}
	pclose($tmpResult);				// 	パイプを閉じる
	chdir($now_dir);				//　元にいたディレクトリに帰る
	
	// 結果を連想配列へ格納
	$result = ereg_replace("&$", "", $result);
	foreach (explode("&",$result) as $data) {
		list($key, $val) = explode("=", $data, 2);
		$return[$key] = $val;
	}
	
	return $return;
}

// 受注一時テーブルから情報を取得する
function sfGetOrderTemp($order_temp_id) {
	$objQuery = new SC_Query();
	$where = "order_temp_id = ?";
	$arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($order_temp_id));
	return $arrRet[0];
}

// カテゴリID取得判定用のグローバル変数(一度取得されていたら再取得しないようにする)
$g_category_on = false;
$g_category_id = "";

/* 選択中のカテゴリを取得する */
function sfGetCategoryId($product_id, $category_id) {
	global $g_category_on;
	global $g_category_id;
	if(!$g_category_on)	{
		$g_category_on = true;
		if(sfIsInt($category_id) && sfIsRecord("dtb_category","category_id", $category_id)) {
			$g_category_id = $category_id;
		} else if (sfIsInt($product_id) && sfIsRecord("dtb_products","product_id", $product_id, "status = 1")) {
			$objQuery = new SC_Query();
			$where = "product_id = ?";
			$category_id = $objQuery->get("dtb_products", "category_id", $where, array($product_id));
			$g_category_id = $category_id;
		} else {
			// 不正な場合は、0を返す。
			$g_category_id = 0;
		}
	}
	return $g_category_id;
}

// ROOTID取得判定用のグローバル変数(一度取得されていたら再取得しないようにする)
$g_root_on = false;
$g_root_id = "";

/* 選択中のアイテムのルートカテゴリIDを取得する */
function sfGetRootId() {
	global $g_root_on;
	global $g_root_id;
	if(!$g_root_on)	{
		$g_root_on = true;
		$objQuery = new SC_Query();
		if($_GET['product_id'] != "" || $_GET['category_id'] != "") {
			// 選択中のカテゴリIDを判定する
			$category_id = sfGetCategoryId($_GET['product_id'], $_GET['category_id']);
			// ROOTカテゴリIDの取得
			 $arrRet = sfGetParents($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $category_id);
			 $root_id = $arrRet[0];
		} else {
			// ROOTカテゴリIDをなしに設定する
			$root_id = "";
		}
		$g_root_id = $root_id;
	}
	return $g_root_id;
}

/* カテゴリから商品を検索する場合のWHERE文と値を返す */
function sfGetCatWhere($category_id) {
	// 子カテゴリIDの取得
	$arrRet = sfGetChildsID("dtb_category", "parent_category_id", "category_id", $category_id);
	$tmp_where = "";
	foreach ($arrRet as $val) {
		if($tmp_where == "") {
			$tmp_where.= " category_id IN ( ? ";
		} else {
			$tmp_where.= " ,? ";
		}
		$arrval[] = $val;
	}
	$tmp_where.= " ) ";
	return array($tmp_where, $arrval);
}

/* 加算ポイントの計算式 */
function sfGetAddPoint($totalpoint, $use_point, $arrInfo) {
	// 購入商品の合計ポイントから利用したポイントのポイント換算価値を引く方式
	$add_point = $totalpoint - intval($use_point * ($arrInfo['point_rate'] / 100));
	
	if($add_point < 0) {
		$add_point = '0';
	}
	return $add_point;
}

/* 一意かつ予測されにくいID */
function sfGetUniqRandomId($head = "") {
	// 予測されないようにランダム文字列を付与する。
	$random = gfMakePassword(8);
	// 同一ホスト内で一意なIDを生成
	$id = uniqid($head);
	return ($id . $random);
}

// カテゴリ別オススメ品の取得
function sfGetBestProducts( $conn, $category_id = 0){
	// 既に登録されている内容を取得する
	$sql = "SELECT name, main_image, main_list_image, price01_min, price01_max, price02_min, price02_max, point_rate,
			 A.product_id, A.comment FROM dtb_best_products as A LEFT JOIN vw_products_allclass as B 
			USING (product_id) WHERE A.category_id = ? AND A.del_flg = 0 AND status = 1 ORDER BY A.rank";
	$arrItems = $conn->getAll($sql, array($category_id));
	return $arrItems;
}

// 特殊制御文字の手動エスケープ
function sfManualEscape($data) {
	// 配列でない場合
	if(!is_array($data)) {			
		$ret = pg_escape_string($data);
		$ret = ereg_replace("%", "\\%", $ret);
		$ret = ereg_replace("_", "\\_", $ret);
		return $ret;
	}
	
	// 配列の場合
	foreach($data as $val) {
		$ret = pg_escape_string($val);
		$ret = ereg_replace("%", "\\%", $ret);
		$ret = ereg_replace("_", "\\_", $ret);
		$arrRet[] = $ret;
	}
	return $arrRet;
}

// 受注番号、利用ポイント、加算ポイントから最終ポイントを取得
function sfGetCustomerPoint($order_id, $use_point, $add_point) {
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("customer_id", "dtb_order", "order_id = ?", array($order_id));
	$customer_id = $arrRet[0]['customer_id'];
	if($customer_id != "" && $customer_id >= 1) {
		$arrRet = $objQuery->select("point", "dtb_customer", "customer_id = ?", array($customer_id));
		$point = $arrRet[0]['point'];
		$total_point = $arrRet[0]['point'] - $use_point + $add_point;
	} else {
		$total_point = "";
		$point = "";
	}
	return array($point, $total_point);
}

/* ドメイン間で有効なセッションのスタート */
function sfDomainSessionStart() {
	$ret = session_id();
/*
	ヘッダーを送信していてもsession_start()が必要なページがあるので
	コメントアウトしておく
	if($ret == "" && !headers_sent()) {
*/
	if($ret == "") {
		/* セッションパラメータの指定
		 ・ブラウザを閉じるまで有効
		 ・すべてのパスで有効
		 ・同じドメイン間で共有 */
		session_set_cookie_params (0, "/", DOMAIN_NAME);

		if(!ini_get("session.auto_start")){
			// セッション開始
			session_start();
		}
	}
}

/* 文字列に強制的に改行を入れる */
function sfPutBR($str, $size) {
	$i = 0;
	$cnt = 0;
	$line = array();
	$ret = "";
	
	while($str[$i] != "") {
		$line[$cnt].=$str[$i];
		$i++;
		if(strlen($line[$cnt]) > $size) {
			$line[$cnt].="<br />";
			$cnt++;
		}
	}
	
	foreach($line as $val) {
		$ret.=$val;
	}
	return $ret;
}

// 二回以上繰り返されているスラッシュ[/]を一つに変換する。
function sfRmDupSlash($istr){
	if(ereg("^http://", $istr)) {
		$str = substr($istr, 7);
		$head = "http://";
	} else if(ereg("^https://", $istr)) {
		$str = substr($istr, 8);
		$head = "https://";
	} else {
		$str = $istr;
	}
	$str = ereg_replace("[/]+", "/", $str);
	$ret = $head . $str;
	return $ret;	
}

function sfEncodeFile($filepath, $enc_type, $out_dir) {
	$ifp = fopen($filepath, "r");
	
	$basename = basename($filepath);
	$outpath = $out_dir . "enc_" . $basename;
	
	$ofp = fopen($outpath, "w+");
	
	while(!feof($ifp)) {
		$line = fgets($ifp);
		$line = mb_convert_encoding($line, $enc_type, "auto");
		fwrite($ofp,  $line);
	}
	
	fclose($ofp);
	fclose($ifp);
	
	return 	$outpath;
}

function sfCutString($str, $len, $byte = true) {
	if($byte) {
		if(strlen($str) > ($len + 2)) {
			$ret =substr($str, 0, $len) . "...";
		} else {
			$ret = $str;
		}
	} else {
		if(mb_strlen($str) > ($len + 1)) {
			$ret = mb_substr($str, 0, $len) . "...";
		} else {
			$ret = $str;
		}
	}
	return $ret;
}

// 年、月、締め日から、先月の締め日+1、今月の締め日を求める。
function sfTermMonth($year, $month, $close_day) {
	$end_year = $year;
	$end_month = $month;
	
	// 開始月が終了月と同じか否か
	$same_month = false;
	
	// 該当月の末日を求める。
	$end_last_day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));
	
	// 月の末日が締め日より少ない場合
	if($end_last_day < $close_day) {
		// 締め日を月末日に合わせる
		$end_day = $end_last_day;
	} else {
		$end_day = $close_day;
	}
	
	// 前月の取得
	$tmp_year = date("Y", mktime(0, 0, 0, $month, 0, $year));
	$tmp_month = date("m", mktime(0, 0, 0, $month, 0, $year));
	// 前月の末日を求める。
	$start_last_day = date("d", mktime(0, 0, 0, $month, 0, $year));
	
	// 前月の末日が締め日より少ない場合
	if ($start_last_day < $close_day) {
		// 月末日に合わせる
		$tmp_day = $start_last_day;
	} else {
		$tmp_day = $close_day;
	}
	
	// 先月の末日の翌日を取得する
	$start_year = date("Y", mktime(0, 0, 0, $tmp_month, $tmp_day + 1, $tmp_year));
	$start_month = date("m", mktime(0, 0, 0, $tmp_month, $tmp_day + 1, $tmp_year));
	$start_day = date("d", mktime(0, 0, 0, $tmp_month, $tmp_day + 1, $tmp_year));
	
	// 日付の作成
	$start_date = sprintf("%d/%d/%d 00:00:00", $start_year, $start_month, $start_day);
	$end_date = sprintf("%d/%d/%d 23:59:59", $end_year, $end_month, $end_day);
	
	return array($start_date, $end_date);
}

// PDF用のRGBカラーを返す
function sfGetPdfRgb($hexrgb) {
	$hex = substr($hexrgb, 0, 2);
	$r = hexdec($hex) / 255;
	
	$hex = substr($hexrgb, 2, 2);
	$g = hexdec($hex) / 255;
	
	$hex = substr($hexrgb, 4, 2);
	$b = hexdec($hex) / 255;
	
	return array($r, $g, $b);	
}

//メルマガ仮登録とメール配信
function sfRegistTmpMailData($mail_flag, $email){
	$objQuery = new SC_Query();
	$objConn = new SC_DBConn();
	$objPage = new LC_Page();
	
	$random_id = sfGetUniqRandomId();
	$arrRegistMailMagazine["mail_flag"] = $mail_flag;
	$arrRegistMailMagazine["email"] = $email;
	$arrRegistMailMagazine["temp_id"] =$random_id;
	$arrRegistMailMagazine["end_flag"]='0';
	$arrRegistMailMagazine["update_date"] = 'now()';
	
	//メルマガ仮登録用フラグ
	$flag = $objQuery->count("dtb_customer_mail_temp", "email=?", array($email));
	$objConn->query("BEGIN");
	switch ($flag){
		case '0':
		$objConn->autoExecute("dtb_customer_mail_temp",$arrRegistMailMagazine);
		break;
	
		case '1':
		$objConn->autoExecute("dtb_customer_mail_temp",$arrRegistMailMagazine, "email = '" .addslashes($email). "'");
		break;
	}
	$objConn->query("COMMIT");
	$subject = sfMakeSubject('メルマガ仮登録が完了しました。');
	$objPage->tpl_url = SSL_URL."mailmagazine/regist.php?temp_id=".$arrRegistMailMagazine['temp_id'];
	switch ($mail_flag){
		case '1':
		$objPage->tpl_name = "登録";
		$objPage->tpl_kindname = "HTML";
		break;
		
		case '2':
		$objPage->tpl_name = "登録";
		$objPage->tpl_kindname = "テキスト";
		break;
		
		case '3':
		$objPage->tpl_name = "解除";
		break;
	}
		$objPage->tpl_email = $email;
	sfSendTplMail($email, $subject, 'mail_templates/mailmagazine_temp.tpl', $objPage);
}

// 再帰的に多段配列を検索して一次元配列(Hidden引渡し用配列)に変換する。
function sfMakeHiddenArray($arrSrc, $arrDst = array(), $parent_key = "") {
	if(is_array($arrSrc)) {
		foreach($arrSrc as $key => $val) {
			if($parent_key != "") {
				$keyname = $parent_key . "[". $key . "]";
			} else {
				$keyname = $key;
			}
			if(is_array($val)) {
				$arrDst = sfMakeHiddenArray($val, $arrDst, $keyname);
			} else {
				$arrDst[$keyname] = $val;
			}
		}
	}
	return $arrDst;
}

// DB取得日時をタイムに変換
function sfDBDatetoTime($db_date) {
	$date = ereg_replace("\..*$","",$db_date);
	$time = strtotime($date);
	return $time;
}

// 出力の際にテンプレートを切り替えられる
/*
	index.php?tpl=test.tpl
*/
function sfCustomDisplay($objPage) {
	$basename = basename($_SERVER["REQUEST_URI"]);

	if($basename == "") {
		$path = $_SERVER["REQUEST_URI"] . "index.php";
	} else {
		$path = $_SERVER["REQUEST_URI"];
	}	

	if($_GET['tpl'] != "") {
		$tpl_name = $_GET['tpl'];
	} else {
		$tpl_name = ereg_replace("^/", "", $path);
		$tpl_name = ereg_replace("/", "_", $tpl_name);
		$tpl_name = ereg_replace("(\.php$|\.html$)", ".tpl", $tpl_name);
	}

	$template_path = TEMPLATE_FTP_DIR . $tpl_name;

	if(file_exists($template_path)) {
		$objView = new SC_UserView(TEMPLATE_FTP_DIR, COMPILE_FTP_DIR);
		$objView->assignobj($objPage);
		$objView->display($tpl_name);
	} else {
		$objView = new SC_SiteView();
		$objView->assignobj($objPage);
		$objView->display(SITE_FRAME);
	}
}

//会員編集登録処理
function sfEditCustomerData($array, $arrRegistColumn) {
	$objQuery = new SC_Query();
	
	foreach ($arrRegistColumn as $data) {
		if ($data["column"] != "password") {
			if($array[ $data['column'] ] != "") {
				$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
			} else {
				$arrRegist[ $data['column'] ] = NULL;
			}
		}
	}
	if (strlen($array["year"]) > 0 && strlen($array["month"]) > 0 && strlen($array["day"]) > 0) {
		$arrRegist["birth"] = $array["year"] ."/". $array["month"] ."/". $array["day"] ." 00:00:00";
	} else {
		$arrRegist["birth"] = NULL;
	}

	//-- パスワードの更新がある場合は暗号化。（更新がない場合はUPDATE文を構成しない）
	if ($array["password"] != DEFAULT_PASSWORD) $arrRegist["password"] = sha1($array["password"] . ":" . AUTH_MAGIC); 
	$arrRegist["update_date"] = "NOW()";
	
	$sqlval['email'] = $array['email'];
	$sqlval['mail_flag'] = $array['mail_flag'];
	//-- 編集登録実行
	$objQuery->begin();
	$objQuery->update("dtb_customer", $arrRegist, "customer_id = ? ", array($array['customer_id']));
	$objQuery->delete("dtb_customer_mail", "email = ?", array($array['email']));
	$objQuery->insert("dtb_customer_mail", $sqlval);
	$objQuery->commit();
}

// PHPのmb_convert_encoding関数をSmartyでも使えるようにする
function sf_mb_convert_encoding($str, $encode = 'EUC-JP') {
	return  mb_convert_encoding($str, $encode);
}	

// PHPのmktime関数をSmartyでも使えるようにする
function sf_mktime($format, $hour=0, $minute=0, $second=0, $month=1, $day=1, $year=1999) {
	return  date($format,mktime($hour, $minute, $second, $month, $day, $year));
}	

// PHPのdate関数をSmartyでも使えるようにする
function sf_date($format, $timestamp = '') {
	return  date( $format, $timestamp);
}

// チェックボックスの型を変換する
function sfChangeCheckBox($data , $tpl = false){
	if ($tpl) {
		if ($data == 1){
			return 'checked';
		}else{
			return "";
		}
	}else{
		if ($data == "on"){
			return 1;
		}else{
			return 2;
		}
	}
}

function sfCategory_Count($objQuery){
	$sql = "";
	
	//テーブル内容の削除
	$objQuery->query("DELETE FROM dtb_category_count");
	$objQuery->query("DELETE FROM dtb_category_total_count");
	
	//各カテゴリ内の商品数を数えて格納
	$sql = " INSERT INTO dtb_category_count(category_id, product_count) ";
	$sql .= " SELECT T1.category_id, count(T2.category_id) FROM dtb_category AS T1 LEFT JOIN dtb_products AS T2 ";
	$sql .= " ON T1.category_id = T2.category_id  ";
	$sql .= " WHERE T2.del_flg = 0 AND T2.status = 1 ";
	$sql .= " GROUP BY T1.category_id, T2.category_id ";
	$objQuery->query($sql);
	
	//子カテゴリ内の商品数を集計する
	$arrCat = $objQuery->getAll("SELECT * FROM dtb_category");
	
	$sql = "";
	foreach($arrCat as $key => $val){
		
		// 子ID一覧を取得
		$arrRet = sfGetChildrenArray('dtb_category', 'parent_category_id', 'category_id', $val['category_id']);	
		$line = sfGetCommaList($arrRet);
		
		$sql = " INSERT INTO dtb_category_total_count(category_id, product_count) ";
		$sql .= " SELECT ?, SUM(product_count) FROM dtb_category_count ";
		$sql .= " WHERE category_id IN (" . $line . ")";
				
		$objQuery->query($sql, array($val['category_id']));
	}
}

// 2つの配列を用いて連想配列を作成する
function sfarrCombine($arrKeys, $arrValues) {

	if(count($arrKeys) <= 0 and count($arrValues) <= 0) return array();
	
    $keys = array_values($arrKeys);
    $vals = array_values($arrValues); 
	
    $max = max( count( $keys ), count( $vals ) ); 
    $combine_ary = array(); 
    for($i=0; $i<$max; $i++) { 
        $combine_ary[$keys[$i]] = $vals[$i]; 
    } 
    if(is_array($combine_ary)) return $combine_ary; 
    
	return false; 
}

/* 階層構造のテーブルから子ID配列を取得する */
function sfGetChildrenArray($table, $pid_name, $id_name, $id) {
	$objQuery = new SC_Query();
	$col = $pid_name . "," . $id_name;
 	$arrData = $objQuery->select($col, $table);
	
	$arrPID = array();
	$arrPID[] = $id;
	$arrChildren = array();
	$arrChildren[] = $id;
	
	$arrRet = sfGetChildrenArraySub($arrData, $pid_name, $id_name, $arrPID);
	
	while(count($arrRet) > 0) {
		$arrChildren = array_merge($arrChildren, $arrRet);
		$arrRet = sfGetChildrenArraySub($arrData, $pid_name, $id_name, $arrRet);
	}
	
	return $arrChildren;
}

/* 親ID直下の子IDをすべて取得する */
function sfGetChildrenArraySub($arrData, $pid_name, $id_name, $arrPID) {
	$arrChildren = array();
	$max = count($arrData);
	
	for($i = 0; $i < $max; $i++) {
		foreach($arrPID as $val) {
			if($arrData[$i][$pid_name] == $val) {
				$arrChildren[] = $arrData[$i][$id_name];
			}
		}
	}	
	return $arrChildren;
}


/* 階層構造のテーブルから親ID配列を取得する */
function sfGetParentsArray($table, $pid_name, $id_name, $id) {
	$objQuery = new SC_Query();
	$col = $pid_name . "," . $id_name;
 	$arrData = $objQuery->select($col, $table);
	
	$arrParents = array();
	$arrParents[] = $id;
	$child = $id;
	
	$ret = sfGetParentsArraySub($arrData, $pid_name, $id_name, $child);

	while($ret != "") {
		$arrParents[] = $ret;
		$ret = sfGetParentsArraySub($arrData, $pid_name, $id_name, $ret);
	}
	
	$arrParents = array_reverse($arrParents);
	
	return $arrParents;
}

/* 子ID所属する親IDを取得する */
function sfGetParentsArraySub($arrData, $pid_name, $id_name, $child) {
	$max = count($arrData);
	$parent = "";
	for($i = 0; $i < $max; $i++) {
		if($arrData[$i][$id_name] == $child) {
			$parent = $arrData[$i][$pid_name];
			break;
		}
	}
	return $parent;
}

/* 階層構造のテーブルから与えられたIDの兄弟を取得する */
function sfGetBrothersArray($arrData, $pid_name, $id_name, $arrPID) {
	$max = count($arrData);
	
	$arrBrothers = array();
	foreach($arrPID as $id) {
		// 親IDを検索する
		for($i = 0; $i < $max; $i++) {
			if($arrData[$i][$id_name] == $id) {
				$parent = $arrData[$i][$pid_name];
				break;
			}
		}
		// 兄弟IDを検索する
		for($i = 0; $i < $max; $i++) {
			if($arrData[$i][$pid_name] == $parent) {
				$arrBrothers[] = $arrData[$i][$id_name];
			}
		}					
	}
	return $arrBrothers;
}

/* 階層構造のテーブルから与えられたIDの直属の子を取得する */
function sfGetUnderChildrenArray($arrData, $pid_name, $id_name, $parent) {
	$max = count($arrData);
	
	$arrChildren = array();
	// 子IDを検索する
	for($i = 0; $i < $max; $i++) {
		if($arrData[$i][$pid_name] == $parent) {
			$arrChildren[] = $arrData[$i][$id_name];
		}
	}					
	return $arrChildren;
}


// カテゴリツリーの取得
function sfGetCatTree($parent_category_id, $count_check = false) {
	$objQuery = new SC_Query();
	$col = "";
	$col .= " cat.category_id,";
	$col .= " cat.category_name,";
	$col .= " cat.parent_category_id,";
	$col .= " cat.level,";
	$col .= " cat.rank,";
	$col .= " cat.creator_id,";
	$col .= " cat.create_date,";
	$col .= " cat.update_date,";
	$col .= " cat.del_flg, ";
	$col .= " ttl.product_count";	
	$from = "dtb_category as cat left join dtb_category_total_count as ttl on ttl.category_id = cat.category_id";
	// 登録商品数のチェック
	if($count_check) {
		$where = "del_flg = 0 AND product_count > 0";
	} else {
		$where = "del_flg = 0";
	}
	$objQuery->setoption("ORDER BY rank DESC");
	$arrRet = $objQuery->select($col, $from, $where);
	
	$arrParentID = sfGetParents($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $parent_category_id);
	
	foreach($arrRet as $key => $array) {
		foreach($arrParentID as $val) {
			if($array['category_id'] == $val) {
				$arrRet[$key]['display'] = 1;
				break;
			}
		}
	}
	
	return $arrRet;
}

// 親カテゴリーを連結した文字列を取得する
function sfGetCatCombName($category_id){
	// 商品が属するカテゴリIDを縦に取得
	$objQuery = new SC_Query();
	$arrCatID = sfGetParents($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);	
	$ConbName = "";
	
	// カテゴリー名称を取得する
	foreach($arrCatID as $key => $val){
		$sql = "SELECT category_name FROM dtb_category WHERE category_id = ?";
		$arrVal = array($val);
		$CatName = $objQuery->getOne($sql,$arrVal);
		$ConbName .= $CatName . ' | ';
	}
	// 最後の ｜ をカットする
	$ConbName = substr_replace($ConbName, "", strlen($ConbName) - 2, 2);
	
	return $ConbName;
}

// 指定したカテゴリーIDの大カテゴリーを取得する
function GetFirstCat($category_id){
	// 商品が属するカテゴリIDを縦に取得
	$objQuery = new SC_Query();
	$arrRet = array();
	$arrCatID = sfGetParents($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);	
	$arrRet['id'] = $arrCatID[0];
	
	// カテゴリー名称を取得する
	$sql = "SELECT category_name FROM dtb_category WHERE category_id = ?";
	$arrVal = array($arrRet['id']);
	$arrRet['name'] = $objQuery->getOne($sql,$arrVal);
	
	return $arrRet;
}

//MySQL用のSQL文に変更する
function sfChangeMySQL($sql){
	// 改行、タブを1スペースに変換
	$sql = preg_replace("/[\r\n\t]/"," ",$sql);
	
	$sql = sfChangeView($sql);		// view表をインラインビューに変換する
	$sql = sfChangeILIKE($sql);		// ILIKE検索をLIKE検索に変換する
	$sql = sfChangeRANDOM($sql);	// RANDOM()をRAND()に変換する
	return $sql;
}

// 配列の中にデータが存在しているかチェックを行う(大文字小文字の区別なし)
function sfInArray($sql){
	global $arrView;

	foreach($arrView as $key => $val){
		if (strcasecmp($sql, $val) == 0){
			$changesql = eregi_replace("($key)", "$val", $sql);
			sfInArray($changesql);
		}
	}
	return false;
}

// view表をインラインビューに変換する
function sfChangeView($sql){
	global $arrView;

	$changesql = strtr($sql,$arrView);

	return $changesql;
}

// ILIKE検索をLIKE検索に変換する
function sfChangeILIKE($sql){
	$changesql = eregi_replace("(ILIKE )", "LIKE BINARY ", $sql);
	return $changesql;
}

// RANDOM()をRAND()に変換する
function sfChangeRANDOM($sql){
	$changesql = eregi_replace("( RANDOM() )", " RAND() ", $sql);
	return $changesql;
}


/* デバッグ用 ------------------------------------------------------------------------------------------------*/
function sfPrintR($obj) {
	print("<div style='font-size: 12px'>\n");
	print("<strong>**デバッグ中**</strong><br />\n");
	print("<pre>\n");
	print_r($obj);
	print("</pre>\n");
	print("<strong>**デバッグ中**</strong></div>\n");
}

?>