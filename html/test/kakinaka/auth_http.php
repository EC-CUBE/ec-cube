<?php  
// データベースからの列データの取得と MD5 パスワードを使用した例
require_once("../../require.php");
$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/pear/Auth_HTTP.php");

//define("DSN", "pgsql://kakinaka_db_user:password@kakinaka.ec-cube.net/kakinaka_db");
define("DSN", "mysql://eccube_db:password@210.18.212.165:3308/eccube_db");

// データベース接続オプションの設定
$arrDbConn = array(
	'dsn'=>DSN,
	'table'=>"dtb_member",              // テーブル名 
	'usernamecol'=>"login_id",			// ユーザ名のカラム
	'passwordcol'=>"password",			// パスワードのカラム
	'cryptType'=>"none",				// データベース中でのパスワードの暗号化形式
	'db_fields'=>"*",					// 他のカラムの取得を可能にする
);

$objAuthHttp = new Auth_HTTP("DB", $AuthOptions);

$objAuthHttp->setRealm('user realm');				// 領域 (realm) 名
$objAuthHttp->setCancelText('接続エラー'); 	   	// 認証が失敗した際に表示されるメッセージ
$objAuthHttp->start();					// 認証プロセスの開始

if($objAuthHttp->getAuth())				// 認証すべきユーザかどうかの確認 
{
	echo "認証成功";
	echo $objAuthHttp->getAuthData('name');		// およびメールアドレス (email) を取得。
}

?>
