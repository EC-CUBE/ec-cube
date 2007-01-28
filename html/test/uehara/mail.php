<?php
	
Mb_language( "Japanese" );

$email = $_POST['email'];
$subject = "flash + javascript + PHP";
$body = "flash + javascript + PHP テスト";
	
$result = mb_send_mail( $email, $subject, $body);

//FLASHへ値を送信(成功 = 0, 失敗 = 1)
echo "trans=". $result;


?>