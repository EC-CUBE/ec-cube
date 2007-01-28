<?php
	
Mb_language( "Japanese" );

$subject = $_POST['subject'];
$body = $_POST['body'];
	
$result = mb_send_mail( $_POST['email'], $subject, $body, $header);

//FLASHへ値を送信(成功 = 0, 失敗 = 1)
echo "trans=". $result;


?>