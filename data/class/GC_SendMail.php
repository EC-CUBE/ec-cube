<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
 
require_once(dirname(__FILE__) . '/../module/Mail/Mail.php');
require_once(dirname(__FILE__) . '/../module/Mail/mime.php');

//--- テキスト/HTML　メール送信
class GC_SendMail {

	var $to;			//	送信先
	var $subject;		//	題名
	var $body;			//	本文
	var $cc;			// CC
	var $bcc;			// BCC
	var $replay_to;		// replay_to
	var $return_path;	// return_path
	var $arrEncode;
	var $objMailMime;
	var $arrTEXTEncode;
	var $arrHTMLEncode;
	var $objMail;
			
	// コンストラクタ
	function GC_SendMail() {
		$this->to = "";
		$this->subject = "";
		$this->body = "";
		$this->cc = "";
		$this->bcc = "";
		$this->replay_to = "";
		$this->return_path = "";
		$this->arrEncode = array();
		$this->host = SMTP_HOST;
		$this->port = SMTP_PORT;
		$this->objMailMime = new Mail_mime();
		mb_language( "Japanese" );
		$this->arrTEXTEncode['text_charset'] = "ISO-2022-JP";
		$this->arrHTMLEncode['head_charset'] = "ISO-2022-JP";
        $this->arrHTMLEncode['html_encoding'] = "ISO-2022-JP";
        $this->arrHTMLEncode['html_charset'] = "ISO-2022-JP";
        $arrHost = array(   
                'host' => $this->host,
                'port' => $this->port
        );
        //-- PEAR::Mailを使ってメール送信オブジェクト作成
        $this->objMail =& Mail::factory("smtp", $arrHost);
	}
	
	// 宛先の設定
	function setTo($to, $to_name = "") {
		$this->to = $this->getNameAddress($to_name, $to);
	}
	
	// 送信元の設定
	function setFrom($from, $from_name = "") {
		$this->from = $this->getNameAddress($from_name, $from);
	}
	
	// CCの設定
	function setCc($cc, $cc_name = "") {
		if($cc != "") {
			$this->cc = $this->getNameAddress($cc_name, $cc);
		}
	}
	
	// BCCの設定
	function setBCc($bcc) {
		if($bcc != "") {
			$this->bcc = $bcc;
		}
	}
	
	// Reply-Toの設定
	function setReplyTo($reply_to) {
		if($reply_to != "") {
			$this->reply_to = $reply_to;			
		}		
	}
	
	// Return-Pathの設定
	function setReturnPath($return_path) {
		$this->return_path = $return_path;
	}	
	
	// 件名の設定
	function setSubject($subject) {
		$this->subject = mb_encode_mimeheader($subject);
	}
	
	// 本文の設定
	function setBody($body) {
		$this->body = mb_convert_encoding($body, "JIS", CHAR_CODE);
	}
	
	// SMTPサーバの設定
	function setHost($host) {
		$this->host = $host;
		$arrHost = array(   
                'host' => $this->host,
                'port' => $this->port
        );
        //-- PEAR::Mailを使ってメール送信オブジェクト作成
        $this->objMail =& Mail::factory("smtp", $arrHost);
		
	}
	
	// SMTPポートの設定
	function setPort($port) {
		$this->port = $port;
		$arrHost = array(   
                'host' => $this->host,
                'port' => $this->port
        );
        //-- PEAR::Mailを使ってメール送信オブジェクト作成
        $this->objMail =& Mail::factory("smtp", $arrHost);		
	}
	
	// 名前<メールアドレス>の形式を生成
	function getNameAddress($name, $mail_address) {
			if($name != "") {
				// 制御文字を変換する。				
				$_name = $name;				
				$_name = ereg_replace("<","＜", $_name);
				$_name = ereg_replace(">","＞", $_name);
				if(OS_TYPE != 'WIN') {
					// windowsでは文字化けするので使用しない。
					$_name = mb_convert_encoding($_name,"JIS",CHAR_CODE);	
				}
				$_name = mb_encode_mimeheader($_name);
				$name_address = "\"". $_name . "\"<" . $mail_address . ">";
			} else {
				$name_address = $mail_address;
			}
			return $name_address;
	}
	
	function setItem( $to, $subject, $body, $fromaddress, $from_name, $reply_to="", $return_path="", $errors_to="", $bcc="", $cc ="" ) {
		$this->setBase($to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to, $bcc, $cc);
	}
	
	function setItemHtml( $to, $subject, $body, $fromaddress, $from_name, $reply_to="", $return_path="", $errors_to="", $bcc="", $cc ="" ) {
		$this->setBase($to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to, $bcc, $cc);
	}

	/*	ヘッダ等を格納
		 $to			-> 送信先メールアドレス
		 $subject		-> メールのタイトル
		 $body			-> メール本文
		 $fromaddress	-> 送信元のメールアドレス
		 $header		-> ヘッダー
		 $from_name		-> 送信元の名前（全角OK）
		 $reply_to		-> reply_to設定
		 $return_path	-> return-pathアドレス設定（エラーメール返送用）
		 $cc			-> カーボンコピー
		 $bcc			-> ブラインドカーボンコピー
	*/		
	function setBase( $to, $subject, $body, $fromaddress, $from_name, $reply_to="", $return_path="", $errors_to="", $bcc="", $cc ="" ) {
		// 宛先設定
		$this->to = $to;	
		// 件名設定
		$this->setSubject($subject);
		// 本文設定(iso-2022-jpだと特殊文字が？で送信されるのでJISを使用する)
		$this->setBody($body);
		// 送信元設定
		$this->setFrom($fromaddress, $from_name);
		// 返信先設定
		$this->setReplyTo($reply_to);
		// CC設定
		$this->setCc($cc);
		// BCC設定
		$this->setBcc($bcc);
		
		// Errors-Toは、ほとんどのSMTPで無視され、Return-Pathが優先されるためReturn_Pathに設定する。
		if($errors_to != "") {
			$this->return_path = $errors_to;
		} else if($return_path != "") {
			$this->return_path = $return_path;
		} else {
			$this->return_path = $fromaddress;
		}
	}
	
	// ヘッダーを返す
	function getHeader() {
		//-- 送信するメールの内容と送信先
		$arrHeader['To'] = $this->to;
		$arrHeader['Subject'] = $this->subject;
		$arrHeader['From'] = $this->from;
		$arrHeader['Return-Path'] = $this->return_path;
		
		if($this->reply_to != "") {
			$arrHeader['Reply-To'] = $this->reply_to;
		}
		
		if($this->cc != "") {
			$arrHeader['Cc'] = $this->cc;
		}
		
		if($this->bcc != "") {		
			$arrHeader['Bcc'] = $this->bcc;
		}
		return $arrHeader;
	}
	
	//	TXTメール送信を実行する
	function sendMail() {
		$this->objMailMime->setTXTBody($this->body);
		$body = $this->objMailMime->get($this->arrTEXTEncode);
		$header = $this->getHeader();
        // メール送信
        $result = $this->objMail->send($this->to, $header, $body);           
		if (PEAR::isError($result)) { 
			GC_Utils_Ex::gfPrintLog($result->getMessage());
			GC_Utils_Ex::gfDebugLog($header);
			return false;		
		}
		return true;		
	}
	
	// HTMLメール送信を実行する
	function sendHtmlMail() {
		$this->objMailMime->setHTMLBody($this->body);
		$body = $this->objMailMime->get($this->arrHTMLEncode);
		$header = $this->getHeader();
        // メール送信
        $result = $this->objMail->send($this->to, $header, $body);           
		if (PEAR::isError($result)) { 
			GC_Utils_Ex::gfPrintLog($result->getMessage());
			GC_Utils_Ex::gfDebugLog($header);	
			return false;
		}
		return true;
	}
}

?>