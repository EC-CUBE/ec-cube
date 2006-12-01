<?php  
// データベースからの列データの取得と MD5 パスワードを使用した例
require_once("../../require.php");
$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/pear/Auth_HTTP.php");


$params = Array("../../.htpasswd","authType"=>"basic","cryptType"=>"MD5");

$objAuth = new Auth_HTTP("File",$params);

// realm領域名
$objAuth->setRealm('Please Enter Your Password');

// 認証をキャンセルや認証エラーした際に表示されるメッセージ
$objAuth->setCancelText('<h2>Authorization Required</h2>');

// 認証プロセスの開始
$objAuth->start();

if($objAuth->getAuth()) {
    echo $objAuth->username . "の認証に成功!";
}

define("DEFAULT_DSN", "pgsql://kakinaka_db_user:password@kakinaka.ec-cube.net/kakinaka_db");


// データベース接続オプションの設定
$AuthOptions = array(
//'dsn'=>"pgsql://test:test@localhost/testdb",
'dsn'=>DEFAULT_DSN,
'table'=>"dtb_member",                            // テーブル名 
'usernamecol'=>"login_id",			// ユーザ名のコラム
'passwordcol'=>"password",			// パスワードのコラム
//'cryptType'=>"md5",				// データベース中でのパスワードの暗号化形式
'cryptType'=>"none",				// データベース中でのパスワードの暗号化形式
'dbFields'=>"*",				// 他のコラムの取得を可能にする
);

$a = new Auth_HTTP("DB", $AuthOptions);


sfprintr($a);

//$a->setRealm('yourrealm');			// 領域 (realm) 名
//$a->setCancelText('<h2>Error 401</h2>');        // 認証が失敗した際に表示されるメッセージ
$a->start();					// 認証プロセスの開始

/*

if($a->getAuth())				// 認証すべきユーザかどうかの確認 
{
	echo "Hello $a->username welcome to my secret page <BR>";
	echo "Your details on file are: <BR>";
	echo $a->getAuthData('userid');		// データベースから他のデータを取得している。
	echo $a->getAuthData('telephone');      // この例では、ユーザID (userid)、電話番号 (telephone)
	echo $a->getAuthData('email');		// およびメールアドレス (email) を取得。
};
*/

?>