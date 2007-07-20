<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: mdl_remise.inc 7162 2006-11-18 09:53:33Z inoue $
 * @link		http://www.lockon.co.jp/
 *
 */

// ルミーズモジュールID
define("MDL_REMISE_ID", 5);

// EC-CUBE・ルミーズ共通ID
define("MDL_REMISE_POST_VALUE", A0000155);

// ルミーズへのデータ送信用エンコード
define("REMISE_SEND_ENCODE", 'SJIS-win');

// クレジット決済・コンビニ決済上限・下限金額
define("REMISE_CREDIT_UPPER", 500000);
define("REMISE_CONVENIENCE_UPPER", 500000);
define("REMISE_CONVENIENCE_BOTTOM", 200);

// クレジット支払いコード(10:一括 61:分割 80:リボ)
define("REMISE_PAYMENT_METHOD_LUMP", 10);
define("REMISE_PAYMENT_METHOD_DIVIDE", 61);
define("REMISE_PAYMENT_METHOD_REVO", 80);

// クレジット最大支払い回数
// ECサイト様の条件に合わせて変更して下さい
define("REMISE_PAYMENT_DIVIDE_MAX", 2);

// ECサイト様の条件に合わせて変更して下さい
// AUTH(仮売上)
// CAPTURE(実売上)
define("REMISE_PAYMENT_JOB_CODE", "AUTH");

// 分割回数
$arrCreditDivide = array(
	2 => "2"
);

// クレジット結果ステータス
define("REMISE_PAYMENT_CHARGE_OK", "<SDBKDATA>STATUS=800</SDBKDATA>");

// コンビニ収納受信フラグ(0:自動受信しない 1:自動受信する)
define("REMISE_CONVENIENCE_RECIVE", 1);

// コンビニ収納済フラグ
define("REMISE_CONVENIENCE_CHARGE", 1);

// コンビニ結果ステータス
define("REMISE_CONVENIENCE_CHARGE_OK", "<SDBKDATA>STATUS=800</SDBKDATA>");

// エラー内容
$arrRemiseErrorWord = array(
	"OK" => "0:0000"
);

// ルミーズからの通信のIP制御(0:しない 1:する)
define("REMISE_IP_ADDRESS_DENY", 0);

// ルミーズサーバのIPアドレス
// 実際に使用する場合は最新のIPアドレス帯域を確認して下さい
define("REMISE_IP_ADDRESS_S", "211.0.149.169");
define("REMISE_IP_ADDRESS_E", "211.0.149.169");

// 決済方法
$arrPayment = array(
	1 => "コンビニ"
);

// 支払い方法
$arrCredit = array(
	10 => "一括払い",
	61 => "分割払い",
	80 => "リボルビング払い"
);

// コンビニの種類
$arrConvenience = array(
	D001 => "セブンイレブン"
	,D002 => "ローソン"
	,D030 => "ファミリーマート"
	,D004 => "サークスＫ"
	,D003 => "サンクス"
	,D015 => "セイコーマート"
	,D005 => "ミニストップ"
	,D010 => "デイリーヤマザキ"
	,D011 => "ヤマザキデイリーストア"
);

?>