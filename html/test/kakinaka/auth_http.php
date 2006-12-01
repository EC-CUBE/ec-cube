<?php  
// データベースからの列データの取得と MD5 パスワードを使用した例
require_once("../../require.php");
$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/pear/Auth_HTTP.php");

//define("DSN", "pgsql://kakinaka_db_user:password@kakinaka.ec-cube.net/kakinaka_db");
define("DSN", "mysql://eccube_db_user:password@210.18.212.165:3308/eccube_db");

// データベース接続オプションの設定
$arrDbConn = array(
	'dsn'=>DSN,
	'table'=>"dtb_member",              // テーブル名 
	'usernamecol'=>"login_id",			// ユーザ名のカラム
	'passwordcol'=>"password",			// パスワードのカラム
	'cryptType'=>"none",					// パスワードの暗号化形式(暗号化なしのときはnone)
	'db_fields'=>"*",					// その他のカラムを取得する場合にはカラムを指定する
);

$objAuthHttp = new Auth_HTTP("DB", $arrDbConn);		// オブジェクト生成

$objAuthHttp->setRealm('user realm');				// 領域 (realm) 名
$objAuthHttp->setCancelText('接続エラー'); 		   	// 認証が失敗した際に表示されるメッセージ
$objAuthHttp->start();								// 認証プロセスの開始

// 認証チェック(成功：TRUE　失敗：FALSE)
if($objAuthHttp->getAuth())				
{
	echo "認証成功";
	echo $objAuthHttp->getAuthData('name');		// およびメールアドレス (email) を取得。
}

?>
