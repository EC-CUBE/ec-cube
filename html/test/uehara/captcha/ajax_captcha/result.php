<?php

// セッションスタート
session_start();

// 入力値を受け取る
$input_data = $_POST["input_data"];
$session_data = $_SESSION["security_code"];

// 正しくデータが送られてきているか
if ($input_data == "" || $session_data == "") { 
 echo "値を入力して下さい。";
 exit;
}

// 入力値が正しいかチェック
if ($input_data == $session_data) {
	
	// ここに認証成功時の処理を書いて下さい。
	echo "認証成功！！";

} else {

	// ここに認証失敗時の処理を書いて下さい。
	echo "<font color=\"red\">認証失敗！！</font>";

}
?>