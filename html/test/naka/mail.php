<?php

$to = "naka@lockon.co.jp";
$body = "����ˤ���";

mb_language( "Japanese" );
mb_send_mail($to, "test", $body) 

?>