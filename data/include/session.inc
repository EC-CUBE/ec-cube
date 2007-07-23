<?php

$SESSION_HANDLER_PHP_PATH = realpath( dirname( __FILE__) );
require_once($SESSION_HANDLER_PHP_PATH ."/../class/SC_Query.php" );
require_once($SESSION_HANDLER_PHP_PATH ."/../lib/glib.php" );

// セッションの有効期限(秒)
define("MAX_LIFETIME", 7200);

function sfSessOpen($save_path, $session_name) 
{
  return true;
}

function sfSessClose() 
{
  return true;
}

// セッション読み込み
function sfSessRead($id) 
{
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("sess_data", "dtb_session", "sess_id = ?", array($id));
	return($arrRet[0]['sess_data']);
}

// セッション書込み
function sfSessWrite($id, $sess_data) 
{
	$objQuery = new SC_Query();
	$count = $objQuery->count("dtb_session", "sess_id = ?", array($id));
	$sqlval = array();
	if($count > 0) {
		// レコード更新
		$sqlval['sess_data'] = $sess_data;
		$sqlval['update_date'] = 'Now()';
		$objQuery->update("dtb_session", $sqlval, "sess_id = ?", array($id));
	} else {
		// セッションデータがある場合は、レコード作成
		if(strlen($sess_data) > 0) {
			$sqlval['sess_id'] = $id;
			$sqlval['sess_data'] = $sess_data;
			$sqlval['update_date'] = 'Now()';		
			$sqlval['create_date'] = 'Now()';		
			$objQuery->insert("dtb_session", $sqlval);
		}
	}
	return true;
}

// セッション破棄
function sfSessDestroy($id) 
{
	$objQuery = new SC_Query();
	$objQuery->delete("dtb_session", "sess_id = ?", array($id));
	return true;
}

function sfSessGc($maxlifetime) 
{
	// ※$maxlifetimeは、利用しない。
	// MAX_LIFETIME以上更新されていないセッションを削除する。
	$objQuery = new SC_Query();
	$where = "update_date < current_timestamp + '-". MAX_LIFETIME . " secs'";
	$objQuery->delete("dtb_session", $where);
	return true;
}

if(sfTabaleExists("dtb_session")) {
	session_set_save_handler("sfSessOpen", "sfSessClose", "sfSessRead", "sfSessWrite", "sfSessDestroy", "sfSessGc");
}
?>