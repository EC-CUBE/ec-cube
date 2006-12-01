<?php  
// データベースからの列データの取得と MD5 パスワードを使用した例
require_once("../../require.php");
$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/pear/Auth_HTTP.php");

define("DSN", "pgsql://kakinaka_db_user:password@kakinaka.ec-cube.net/kakinaka_db");

// データベース接続オプションの設定
$AuthOptions = array(
'dsn'=>DSN,
'table'=>"dtb_member",                            // テーブル名 
'usernamecol'=>"login_id",			// ユーザ名のコラム
'passwordcol'=>"password",			// パスワードのコラム
//'cryptType'=>"none",				// データベース中でのパスワードの暗号化形式
'cryptType'=>"none",				// データベース中でのパスワードの暗号化形式
'dbFields'=>"name",				// 他のコラムの取得を可能にする
);

$a = new Auth_HTTP("DB", $AuthOptions);

$a->setRealm('user realm');			// 領域 (realm) 名
$a->setCancelText('<h2>Error 401</h2>');        // 認証が失敗した際に表示されるメッセージ
$a->start();					// 認証プロセスの開始

if($a->getAuth())				// 認証すべきユーザかどうかの確認 
{
	echo "Hello " . $a->username . " welcome to my secret page <BR>";
	echo "Your details on file are: <BR>";
	echo $a->getAuthData('name');		// データベースから他のデータを取得している。
	echo $a->getAuthData('telephone');      // この例では、ユーザID (userid)、電話番号 (telephone)
	echo $a->getAuthData('name');		// およびメールアドレス (email) を取得。
};

sfprintr($a);

?>
