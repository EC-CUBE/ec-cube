<?php
require_once("../../require.php");
require_once(DATA_PATH . "module/Request.php");

switch($_GET['mode']) {
case 'combz_reserve':
	print("combz_reserve");
	sfCombzReserve();
	break;
case 'combz_regist':
	print("combz_regist");	
	break;
default:
	print("default");
	break;
}

function sfCombzReserve() {
	//http://menu2.combzmail.jp/ex_eccube.cgi
	$arrPost = array(
		'test' => 'test'
	);
	
	$req = new HTTP_Request('http://menu2.combzmail.jp/ex_eccube.cgi');
	$req->setMethod(HTTP_REQUEST_METHOD_POST);
	$req->addPostDataArray($arrPost);
	$ret = $req->sendRequest();
		
	if (!PEAR::isError($ret)) {
	    $response = $req->getResponseBody();
		print($response);	    
	} else {
		print($ret->getMessage());
	}
	$req->clearPostData();
}

?>