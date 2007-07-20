#!/usr/bin/env php
<?php
/**
 * モバイルサイト/空メール受け付けスクリプト
 */

require_once dirname(__FILE__) . '/../conf/mobile_conf.php';
require_once DATA_PATH . '/include/php_ini.inc';
require_once DATA_PATH . '/include/mobile.inc';
require_once DATA_PATH . '/lib/slib.php';
require_once DATA_PATH . '/class/GC_MobileKaraMail.php';
require_once DATA_PATH . '/class/GC_SendMail.php';

$CONF = sf_getBasisData();

$objMail =& GC_MobileKaraMail::factory();
$objMail->parse();
$token = $objMail->getToken();
$sender = $objMail->getSender();

if (gfRegisterKaraMail($token, $sender)) {
	$subject = '空メールを受け付けました';
	$reply_body = "下記のリンクをクリックして、次の手続きに進んでください。\n" .
	              SITE_URL . "redirect.php?token=$token";
} else {
	$subject = '空メール手続きが失敗しました';
	$reply_body = "時間切れなどにより、空メール手続きを行うことができませんでした。\n" .
	              "サイトのトップへ戻ってやり直してください。\n" .
	              SITE_URL . "\n";
}

$objReply = new GC_SendMail;
$objReply->setItem($sender, "【{$CONF["shop_name"]}】$subject", $reply_body,
                   $CONF['email03'], $CONF['shop_name'], $CONF['email03'], $CONF['email04']);
$objReply->sendMail();

$objMail->success();
?>
