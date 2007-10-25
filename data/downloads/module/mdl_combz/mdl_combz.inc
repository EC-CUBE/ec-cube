<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @link		http://www.lockon.co.jp/
 *
 */
require_once(DATA_PATH . "module/Request.php");
require_once(DATA_PATH . "lib/slib.php");

/**************************************************************************************************************
 * 関数名	：sfCombzReserve
 * 処理内容	：
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfCombzReserve() {
	$arrPost = array(
		'mode' => 'reserve',
		'pubid' => 'u7gu',
		'pw' => 'iejkngt67',
		'magid' => 'j7zb',
		'subject' => 'lockon test subject',
		'body' => 'this is body',		
	);
	
	$req = new HTTP_Request('http://menu2.combzmail.jp/ex_eccube.cgi');
	$req->setMethod(HTTP_REQUEST_METHOD_POST);
	$req->addPostDataArray($arrPost);
	$ret = $req->sendRequest();
		
	if (!PEAR::isError($ret)) {
	    $response = $req->getResponseBody();
	    $response = mb_convert_encoding($response, "EUC-JP", "Shift-JIS");
		print($response);	    
	} else {
		print($ret->getMessage());
	}
	$req->clearPostData();
}

/**************************************************************************************************************
 * 関数名	：sfCombzRegist
 * 処理内容	：
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfCombzRegist() {
	$arrPost = array(
		'mode' => 'add_form',
		'pubid' => 'u7gu',
		'pw' => 'iejkngt67',
		'magid' => 'j7zb',
		'set_data' => 'test03@lockon.co.jp',
		'set_data' => 'test04@lockon.co.jp',
	);
	
	$req = new HTTP_Request('http://menu2.combzmail.jp/ex_eccube.cgi');
	$req->setMethod(HTTP_REQUEST_METHOD_POST);
	$req->addPostDataArray($arrPost);
	$ret = $req->sendRequest();
		
	if (!PEAR::isError($ret)) {
	    $response = $req->getResponseBody();
	    //$response = mb_convert_encoding($response, "EUC-JP", "Shift-JIS");
		print($response);	    
	} else {
		print($ret->getMessage());
	}
	$req->clearPostData();
}

/**************************************************************************************************************
 * 関数名	：sfCombzPost
 * 処理内容	：
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfCombzPost($combz_type) {
	switch($combz_type) {
	case 'reserve':
		sfCombzReserve();
		break;
	case 'regist':
		sfCombzRegist();
		break;
	default:
		print("コンビーズ連携：不正な値です。");
		break;	
	}
	exit;
}