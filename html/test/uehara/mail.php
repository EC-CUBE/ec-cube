<?php
	
Mb_language( "Japanese" );

$email = $_POST['email'];
$subject = $_POST['subject'];
$body = $_POST['body'];
	
$result = mail( $email, $subject, $body);

//FLASHへ値を送信(成功 = 0, 失敗 = 1)
echo "trans=". $result;


?>