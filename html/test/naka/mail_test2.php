<?php
    require_once("../../require.php");
	$objMail = new GC_SendMail();
	
	$objMail->setItemHtml("naka@lockon.co.jp", "�ƥ��ȣ�", "<b>��</b>", "test@lockon.co.jp", "from", "test@lockon.co.jp", "test@lockon.co.jp");
	
	//���᡼������
	if( mb_send_mail($objMail->to, $objMail->subject, $objMail->body, $objMail->header) ) {
		return true;
	}
	
	
?>