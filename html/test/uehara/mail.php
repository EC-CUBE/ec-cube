<?php

$subject = $_POST['subject'];
$body = $_POST['body'];

Mb_language( "Japanese" );
	
$result = mb_send_mail( $_POST['email'], $subject, $body);

//FLASHへ値を送信(成功 = 0, 失敗 = 1)
echo "trans=". $result;


?>