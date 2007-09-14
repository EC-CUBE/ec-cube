<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

//--- �ƥ�����/HTML���᡼������
class GC_SendMail {

	var	$html;			//	HTML �᡼��إå���
	var $to;			//	������
	var $subject;		//	��̾
	var $body;			//	��ʸ
	var $header;		//	�إå���
	var $return_path;	//��return path
	var $mailer;

	function setTo($to, $to_name = "") {
		if($to_name != "") {
			$name = ereg_replace("<","��", $to_name);
			$name = ereg_replace(">","��", $name);
			
			if(WINDOWS != true) {
				// windows�Ǥ�ʸ����������Τǻ��Ѥ��ʤ���
				$name = mb_convert_encoding($name,"JIS",CHAR_CODE);	
			}
			
			$name = mb_encode_mimeheader($name);
			$this->to = $name . "<" . $to . ">";
		} else {
			$this->to = $to;
		}
	}
	
	function setItem( $to, $subject, $body, $fromaddress, $from_name, $reply_to="", $return_path="", $errors_to="", $bcc="", $cc ="" ) {
		$this->header		 = "Mime-Version: 1.0\n";
		$this->header		.= "Content-Type: text/plain; charset=ISO-2022-JP\n";
		$this->header		.= "Content-Transfer-Encoding: 7bit\n";
		$this->setBase($to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to, $bcc, $cc);
	}
		
	function setItemHtml( $to, $subject, $body, $fromaddress, $from_name, $reply_to="", $return_path="", $errors_to="", $bcc="", $cc ="" ) {
		$this->header		 = "Mime-Version: 1.0\n";
		$this->header		.= "Content-Type: text/html; charset=ISO-2022-JP\n";
		$this->header		.= "Content-Transfer-Encoding: 7bit\n";
		$this->setBase($to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to, $bcc, $cc);
	}

	/*	�إå������Ǽ
		 $to			-> ������᡼�륢�ɥ쥹
		 $subject		-> �᡼��Υ����ȥ�
		 $body			-> �᡼����ʸ
		 $fromaddress	-> �������Υ᡼�륢�ɥ쥹
		 $header		-> �إå���
		 $from_name		-> ��������̾��������OK��
		 $reply_to		-> reply_to����
		 $return_path	-> return-path���ɥ쥹����ʥ��顼�᡼�������ѡ�
		 $cc			-> �����ܥ󥳥ԡ�
		 $bcc			-> �֥饤��ɥ����ܥ󥳥ԡ�
	*/		
	function setBase( $to, $subject, $body, $fromaddress, $from_name, $reply_to="", $return_path="", $errors_to="", $bcc="", $cc ="" ) {
		$this->to			 = $to;
		$this->subject		 = mb_encode_mimeheader($subject);

		// iso-2022-jp�����ü�ʸ�����������������Τ�JIS����Ѥ��롣
		$this->body			 = mb_convert_encoding( $body, "JIS", CHAR_CODE);
				
		// �إå��������ܸ����Ѥ������Mb_encode_mimeheader�ǥ��󥳡��ɤ��롣
		$from_name = ereg_replace("<","��", $from_name);
		$from_name = ereg_replace(">","��", $from_name);
		
		if(WINDOWS != true) {
			// windows�Ǥ�ʸ����������Τǻ��Ѥ��ʤ���
			$from_name = mb_convert_encoding($from_name,"JIS",CHAR_CODE);		
		}
		
		$this->header.= "From: ". mb_encode_mimeheader( $from_name )."<".$fromaddress.">\n";

		if($reply_to != "") {
			$this->header.= "Reply-To: ". $reply_to . "\n";			
		} else {
			$this->header.= "Reply-To: ". $fromaddress . "\n";			
		}
		
		if($cc != "") {
			$this->header.= "Cc: " . $cc. "\n";			
		}
		
		if($bcc != "") {
			$this->header.= "Bcc: " . $bcc . "\n";			
		}

		if($errors_to != "") {
			$this->header.= "Errors-To: ". $errors_to ."\n";
		}
	}
	
	//	�᡼��������¹Ԥ���
	function sendMail() {
		return $this->sendHtmlMail();
	}

	function sendHtmlMail() {
		//���᡼������
		if( mail( $this->to, $this->subject, $this->body, $this->header) ) {
			return true;
		}
		return false;
	}
}

?>