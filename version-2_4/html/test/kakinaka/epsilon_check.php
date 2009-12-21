<?php
require_once("../../require.php");
require_once(MODULE_PATH . "mdl_epsilon/mdl_epsilon.inc");
require_once(DATA_PATH . "module/Request.php");


$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT']; 
gfprintlog($HTTP_USER_AGENT);



$objQuery = new SC_Query();
/*
// trans_code を指定されていて且つ、入金済みの場合
if($_POST["trans_code"] != "" and $_POST["paid"] == 1 and $_POST["order_number"] != ""){
	// ステータスを入金済みに変更する
	$sql = "UPDATE dtb_order SET status = 6, update_date = now() WHERE order_id = ? AND memo04 = ? ";
	$objQuery->query($sql, array($_POST["order_number"], $_POST["trans_code"]));
	
	// POSTの内容を全てログ保存
	$log_path = DATA_PATH . "logs/epsilon.log";
	gfPrintLog("epsilon conveni start---------------------------------------------------------", $log_path);
	foreach($_POST as $key => $val){
		gfPrintLog( "\t" . $key . " => " . $val, $log_path);
	}
	gfPrintLog("epsilon conveni end-----------------------------------------------------------", $log_path);
*/

    // URIから各情報を取得
    $info = parse_url( $_SERVER["REQUEST_URI"] );
    $scheme = $info['scheme'];
    $host = $info['host'];
    $port = $info['port'];
    $path = $info['path'];

	$req =& new HTTP_Request($_SERVER["REQUEST_URI"]);
	$req->addHeader("Content-Type", "text/plan");
	
echo "Content-Type: text/plain

1";

//	$req->setBody($body);
//	sfprintr($info);
	
	$req->clearPostData();
	
	if (PEAR::isError($req)) {
	    echo $req->getMessage();
	} else {
	    echo $req->getResponseBody();
	}
//}

?>