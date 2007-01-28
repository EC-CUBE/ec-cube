<?php
	
Mb_language( "Japanese" );

$email = $_POST['email'];
$subject = mb_convert_encoding( $_POST['subject'], "iso-2022-jp", "S-JIS");;
$body = $_POST['body'];
	
$result = mb_send_mail( $email, $subject, $body);

//FLASHへ値を送信(成功 = 0, 失敗 = 1)
echo "trans=". $result;


?>