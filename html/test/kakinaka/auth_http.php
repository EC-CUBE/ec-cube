<?php  
require_once($include_dir . "/pear/Auth_HTTP.php");	// PEARのAuth_HTTPファイルの読み込み

//define("DSN", "pgsql://kakinaka_db_user:password@kakinaka.ec-cube.net/kakinaka_db");
define("DSN", "mysql://eccube_db_user:password@210.18.212.165:3308/eccube_db");

// データベース接続オプションの設定
$arrDbConn = array(
	'dsn'=>DSN,
	'table'=>"dtb_member",              // テーブル名 
	'usernamecol'=>"login_id",			// ユーザ名のカラム
	'passwordcol'=>"password",			// パスワードのカラム
	'cryptType'=>"none",				// パスワードの暗号化形式(暗号化なしのときはnone)
	'db_fields'=>"*",					// その他のカラムを取得する場合にはカラムを指定する
);

$objAuthHttp = new Auth_HTTP("DB", $arrDbConn);		// オブジェクト生成

$objAuthHttp->setRealm('user realm');				// 領域 (realm) 名
$objAuthHttp->setCancelText('接続エラー'); 		   	// 認証失敗時、表示されるメッセージ

$objAuthHttp->start();								// 認証開始

// 認証チェック(成功：TRUE　失敗：FALSE)
if($objAuthHttp->getAuth())				
{
	echo "認証成功";
	echo "ようこそ " . $objAuthHttp->getAuthData('name') . "さん";	// 取得したデータを使用する
}

?>
