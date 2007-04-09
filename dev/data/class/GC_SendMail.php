<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

//--- テキスト/HTML　メール送信
class GC_SendMail {

	var	$html;			//	HTML メールヘッダー
	var $to;			//	送信先
	var $subject;		//	題名
	var $body;			//	本文
	var $header;		//	ヘッダー
	var $return_path;	//　return path
	var $mailer;

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
	
	
	function setTo($to, $to_name = "") {
		if($to_name != "") {
			$name = ereg_replace("<","＜", $to_name);
			$name = ereg_replace(">","＞", $name);
			$name = mb_encode_mimeheader(mb_convert_encoding($name, "JIS", CHAR_CODE));
			$this->to = $name . "<" . $to . ">";
		} else {
			$this->to = $to;
		}
	}
		
	function setItem( $to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to="", $bcc="", $cc ="" ) {
		
		$this->to			 = $to;
		$this->subject		 = $subject;

		// iso-2022-jpだと特殊文字が？で送信されるのでJISを使用する。
		$this->body			 = mb_convert_encoding( $body, "JIS", CHAR_CODE);

		// ヘッダーに日本語を使用する場合はMb_encode_mimeheaderでエンコードする。
		$from_name = ereg_replace("<","＜", $from_name);
		$from_name = ereg_replace(">","＞", $from_name);
		$from_name = mb_convert_encoding($from_name,"JIS",CHAR_CODE); 
		$this->header		 = "From: ". Mb_encode_mimeheader( $from_name )."<".$fromaddress.">\n";
		$this->header		.= "Reply-To: ". $reply_to . "\n";
		$this->header		.= "Cc: " . $cc. "\n";
		$this->header		.= "Bcc: " . $bcc . "\n";
		$this->header		.= "Errors-To: ". $errors_to ."\n";
		
		$this->return_path   = $return_path;
	}

	function setItemHtml( $to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to="", $bcc="", $cc ="" ) {
			
		$this->to			 = $to;
		$this->subject		 = mb_encode_mimeheader($subject);
		$this->body			 = mb_convert_encoding( $body, "JIS", CHAR_CODE);
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

	//	メール送信を実行する
	function sendMail() {

		Mb_language( "Japanese" );
		
		//　メール送信
		if( mb_send_mail( $this->to, $this->subject, $this->body, $this->header, "" . $this->return_path ) ) {
			return true;
		}
		return false;
	}

	function sendHtmlMail() {

		Mb_language( "Japanese" );	
		
		//　メール送信
		if( mail( $this->to, $this->subject, $this->body, $this->header, "" . $this->return_path ) ) {
			return true;
		}
		return false;
	}
}

?>