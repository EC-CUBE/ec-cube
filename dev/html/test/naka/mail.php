<?php

ini_set("mbstring.http_output", "UTF-8");
ini_set("mbstring.internal_encoding", "UTF-8");

$to = "naka@lockon.co.jp";
$body = "テストです。アイウロエ??????彅??";

$body = mb_convert_encoding($body, 'UTF-8', "EUC-JP");

print("<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></head><body>\n");
print("<b>" . $body . "</b>");

echo("</body></html>");

/*
mb_language("uni");

if(mb_send_mail($to, "test", $body)){
	print("ok");
}
*/



?>