<?php
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
?>
