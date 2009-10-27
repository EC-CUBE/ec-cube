#!/usr/bin/env php
<?php
/**
 * モバイルサイト/空メールテスト送信スクリプト
 */

require_once dirname(__FILE__) . '/../conf/mobile_conf.php';

if ($argc != 4) {
	echo "Usage: $argv[0] sender command token\n";
	exit(1);
}

$receive_kara_mail = dirname(__FILE__) . '/receive_kara_mail.php';
$from = $argv[1];
$command = $argv[2];
$token = $argv[3];
$to = MOBILE_KARA_MAIL_ADDRESS_USER . MOBILE_KARA_MAIL_ADDRESS_DELIMITER . $command . '_' . $token . '@' . MOBILE_KARA_MAIL_ADDRESS_DOMAIN;

$pipe = popen($receive_kara_mail, 'w');
fwrite($pipe, "From $from " . date('D M j H:i:s Y') . "\n");
fwrite($pipe, "From: $from\n");
fwrite($pipe, "To: $to\n");
fwrite($pipe, "\n");
pclose($pipe);
?>
