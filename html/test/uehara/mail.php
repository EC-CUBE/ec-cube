<?php

$subject = Mb_encode_mimeheader($_POST['subject']);
$body = mb_convert_encoding( $_POST['body'], "iso-2022-jp", "EUC-JP");
$header		 = "Mime-Version: 1.0\n";
$header		.= "Content-Type: text/html; charset=iso-2022-jp\n";
$header		.= "Content-Transfer-Encoding: 7bit\n";
		
Mb_language( "Japanese" );
	
$result = mb_send_mail( $_POST['email'], $subject, $body, $header);

//FLASHへ値を送信(成功 = 0, 失敗 = 1)
echo "trans=". $result;


?>