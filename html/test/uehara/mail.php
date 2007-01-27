<?php

$subject = Mb_encode_mimeheader($_POST['subject']);
$body = mb_convert_encoding( $_POST['body'], "iso-2022-jp", "EUC-JP");

Mb_language( "Japanese" );
	
$result = mb_send_mail( $_POST['email'], $subject, $body);

//FLASHへ値を送信(成功 = 0, 失敗 = 1)
echo "trans=". $result;


?>