<?php

$result = mb_send_mail( $_POST['email'], $_POST['subject'], $_POST['body']);

//FLASHへ値を送信(成功 = 0, 失敗 = 1)
echo "trans=". $result;


?>