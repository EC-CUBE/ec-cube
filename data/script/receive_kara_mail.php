#!/usr/bin/env php
<?php
/**
 * ��Х��륵����/���᡼������դ�������ץ�
 */

require_once dirname(__FILE__) . '/../conf/mobile.conf';
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
	$subject = '���᡼�������դ��ޤ���';
	$reply_body = "�����Υ�󥯤򥯥�å����ơ����μ�³���˿ʤ�Ǥ���������\n" .
	              SITE_URL . "redirect.php?token=$token";
} else {
	$subject = '���᡼���³�������Ԥ��ޤ���';
	$reply_body = "�����ڤ�ʤɤˤ�ꡢ���᡼���³����Ԥ����Ȥ��Ǥ��ޤ���Ǥ�����\n" .
	              "�����ȤΥȥåפ���äƤ��ľ���Ƥ���������\n" .
	              SITE_URL . "\n";
}

$objReply = new GC_SendMail;
$objReply->setItem($sender, "��{$CONF["shop_name"]}��$subject", $reply_body,
                   $CONF['email03'], $CONF['shop_name'], $CONF['email03'], $CONF['email04']);
$objReply->sendMail();

$objMail->success();
?>
