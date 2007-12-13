<?php
/**
 * サイバーソースモジュールで共通使用する定数・配列・関数群
 *
 */

// モジュール番号
define('MDL_CYBS_ID', 14);

// ログパス
define('MDL_CYBS_LOG', DATA_PATH . 'logs/cybs.log');

// 拡張モジュール名
define('MDL_CYBS_EXT', 'mod_cybs.so');

// 本番URL
define('MDL_CYBS_REQUEST_URL', 'ics2.ic3.com');
// テストURL
define('MDL_CYBS_REQUEST_URL_TEST', 'ics2test.ic3.com');

// リクエスト先URL配列
$arrCybsRequestURL = array(
    '1' => MDL_CYBS_REQUEST_URL_TEST,
    '2' => MDL_CYBS_REQUEST_URL
);

// 鍵・証明書のパス
define('MDL_CYBS_KEY_PATH', '/opt/CyberSource/SDK/keys/');

// 支払い方法
$arrPayMethod = array(
    '1-0' => "一括払い",
    '4-3' => "分割3回払い",
    '4-6' => "分割6回払い",
    '4-10'=> "分割10回払い",
    '4-15'=> "分割15回払い",
    '4-20'=> "分割20回払い",
    '5-0' => "リボルビング払い"
);

// ics_authのエラーメッセージ
$arrIcsAuthErr = array(
    'DCALL' => 'カード会社が与信判定を取り消しました。',
    'DCARDEXPIRED' => 'カードの有効期限切れです。',
    'DCARDREFUSED' => 'カード会社が与信を承認しませんでした。',
    'DINVALIDCARD' => 'クレジットカード番号が正しくありません。',
    'DINVALIDDATA' => 'リクエストに含まれるデータが不正です。',
    'DMISSINGFIELD' => 'リクエストに必須フィールドが欠けています。',
    'ESYSTEM' => 'システムエラーが発生しました。',
    'ETIMEOUT' => 'リクエストがタイムアウトしました。',
);

// 使用する課金頻度
define('MDL_CYBS_SUBS_FREQ', '1');

// 課金頻度
$arrSubsFrequency = array(
    '1' => 'weekly',
    '2' => 'bi-weekly',
    '3' => 'semi-monthly',
    '4' => 'monthly',
    '5' => 'quarterly',
    '6' => 'quad-weekly',
    '7' => 'semi-annually',
    '8' => 'annually',
);

// 課金頻度の表示名
$arrSubsFrequencyDisp = array(
    '1' => '週1 回',
    '2' => '2 週1 回',
    '3' => '半月1回',
    '4' => '1 ヶ月1 回',
    '5' => '3 ヶ月1 回',
    '6' => '4 週1 回',
    '7' => '半年1回',
    '8' => '1 年1 回',
);

// サブスクリプションを使用するカードの種類
define('MDL_CYBS_SUBS_CARD', '001');

//
?>
