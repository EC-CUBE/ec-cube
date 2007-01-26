<?php
/*
//データ受信
$subject = "Support Of Duddy";
//$address = $_POST['val1'];		//アドレス格納
$address = "Katsuya_Uehara@lockon.co.jp";		//アドレス格納
$message = $_REQUEST['val2'].": test!!";		//本文格納

//メール送信
$success = mail($address, $subject, $message);
if($success){
	//送信成功
	$flg = 0;
//	print "送信成功";
}else{
	//送信失敗
	$flg = 1;
//	print "送信失敗";
}


//FLASHへ値を送信(成功 = 0, 失敗 = 1)
print "trans=".$_REQUEST['val2'];
*/

print "trans=". urlencode("いろいろ");


?>