<?php
    require_once("../../require.php");
	$objMail = new GC_SendMail();
	
	$objMail->setItem("naka@lockon.co.jp", "­Ω", "<b>­Ω����Ǥ���</b>", "test@lockon.co.jp", "from", "test@lockon.co.jp", "test@lockon.co.jp");
	$objMail->sendMail();
	
	$objMail->setItemHtml("naka@lockon.co.jp", "­ΩHTML", "<b>­Ω����Ǥ���</b>", "test@lockon.co.jp", "from", "test@lockon.co.jp", "test@lockon.co.jp");
	$objMail->sendMail();	
	
	
?>