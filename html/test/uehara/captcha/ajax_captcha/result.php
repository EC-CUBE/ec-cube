<?
// セッションスタート
session_start();

// ヘッダー情報出力
header("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=EUC-JP\">");

// 入力値を受け取る
$input_data = $_POST["input_data"];
$session_data = $_SESSION["security_code"];

// POSTデータのみ受け付ける
if ($input_data == "") { 
 echo "FORMから入力して下さい。";
 exit;
}

// セッションの値が正しいかチェック
if (($input_data == $session_data) && ($input_data != "" && $session_data != "")) {
	echo "認証成功！！";
} else {
	echo "認証失敗！！";
}
?>