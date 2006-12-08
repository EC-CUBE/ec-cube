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

// セッションの値が正しいかチェック
if ($input_data == $session_data) {
	echo "認証成功！！";
} else {
	echo "認証失敗！！";
}
?>