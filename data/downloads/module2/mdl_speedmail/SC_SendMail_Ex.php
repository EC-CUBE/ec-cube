<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "SC_SendMail.php");
require_once(realpath(dirname( __FILE__)) . "/include.php");

/**
 * メール送信クラス(拡張).
 *
 * SC_SendMail をカスタマイズする場合はこのクラスを使用する.
 *
 * @package 
 * @author LOCKON CO.,LTD.
 * @version $Id: SC_SendMail_Ex.php 16326 2007-10-08 10:26:55Z naka $
 */
class SC_SendMail_Ex extends SC_SendMail {
	function SC_SendMail_Ex() {
		parent::SC_SendMail();
	}
	
	//	TXTメール送信を実行する
	function sendMail() {
		$this->setReturnPath(ERROR_MAIL);
		return parent::sendMail();
	}
	
	// HTMLメール送信を実行する
	function sendHtmlMail() {
		$this->setReturnPath(ERROR_MAIL);
		return parent::sendHtmlMail();
	}	
}
?>
