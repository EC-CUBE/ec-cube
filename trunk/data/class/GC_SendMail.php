<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
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
	
	
	function setTo($to, $to_name = "") {
		if($to_name != "") {
			$name = ereg_replace("<","��", $to_name);
			$name = ereg_replace(">","��", $name);
			$name = mb_encode_mimeheader($name);
			$this->to = $name . "<" . $to . ">";
		} else {
			$this->to = $to;
		}
	}
		
	function setItem( $to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to="", $bcc="", $cc ="" ) {
		
		$this->to			 = $to;
		$this->subject		 = $subject;
		$this->body			 = $body;
		// �إå��������ܸ����Ѥ������Mb_encode_mimeheader�ǥ��󥳡��ɤ��롣
		$from_name = ereg_replace("<","��", $from_name);
		$from_name = ereg_replace(">","��", $from_name);
				
		$this->header		 = "From: ". Mb_encode_mimeheader( $from_name )."<".$fromaddress.">\n";
		$this->header		.= "Reply-To: ". $reply_to . "\n";
		$this->header		.= "Cc: " . $cc. "\n";
		$this->header		.= "Bcc: " . $bcc . "\n";
		$this->header		.= "Errors-To: ". $errors_to ."\n";
		$this->return_path   = $return_path;
	}

	
	function setItemHtml( $to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to="", $bcc="", $cc ="" ) {
			
		$this->to			 = $to;
		$this->subject		 = Mb_encode_mimeheader($subject);
		$this->body			 = mb_convert_encoding( $body, "iso-2022-jp", CHAR_CODE);
		$this->header		 = "Mime-Version: 1.0\n";
		$this->header		.= "Content-Type: text/html; charset=iso-2022-jp\n";
		$this->header		.= "Content-Transfer-Encoding: 7bit\n";
		$this->header		.= "From: ". Mb_encode_mimeheader( $from_name )."<".$fromaddress.">\n";
		$this->header		.= "Reply-To: ". $reply_to . "\n";
		$this->header		.= "Cc: " . $cc. "\n";
		$this->header		.= "Bcc: " . $bcc . "\n";
		$this->header		.= "Errors-To: ". $errors_to ."\n";
		$this->return_path   = $return_path;
	}

	//	�᡼��������¹Ԥ���
	function sendMail() {

		Mb_language( "Japanese" );
		
		//���᡼������
		if( mb_send_mail( $this->to, $this->subject, $this->body, $this->header, "" . $this->return_path ) ) {
			return true;
		}
		return false;
	}

	function sendHtmlMail() {

		Mb_language( "Japanese" );	
		
		//���᡼������
		if( mail( $this->to, $this->subject, $this->body, $this->header, "" . $this->return_path ) ) {
			return true;
		}
		return false;
	}
}

?>