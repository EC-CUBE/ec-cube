<?php
    require_once("../../require.php");
	$objMail = new GC_SendMail();
	
	$objMail->setItem("naka@lockon.co.jp", "足立", "<b>足立くんです。</b>", "test@lockon.co.jp", "from", "test@lockon.co.jp", "test@lockon.co.jp");
	$objMail->sendMail();
	
	$objMail->setItemHtml("naka@lockon.co.jp", "足立HTML", "<b>足立くんです。</b>", "test@lockon.co.jp", "from", "test@lockon.co.jp", "test@lockon.co.jp");
	$objMail->sendMail();	
	
	
?>