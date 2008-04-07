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
define('MDL_CYBS_MOD_CYBS', 'mod_cybs');

// サブスクリプションID登録数上限
define('MDL_CYBS_SUBS_ID_MAX', 5);

/**
 * mod_cybsをロードする
 *
 * @return boolean
 */
function lfLoadModCybs() {
    if (!extension_loaded(MDL_CYBS_MOD_CYBS)) {
        if (@!dl(MDL_CYBS_MOD_CYBS . ".so")) {
            return false;
        }
    }
    return true;
}

// 本番URL
define('MDL_CYBS_REQUEST_URL', 'ics2.ic3.com');
// テストURL
define('MDL_CYBS_REQUEST_URL_TEST', 'ics2test.ic3.com');

// ICSエラー
$arrIcsErr = array(
    'DCALL' => 'カード会社が与信判定を取り消しました。',
    'DCARDEXPIRED' => 'カードの有効期限切れです。',
    'DCARDREFUSED' => 'カード会社が与信を承認しませんでした。',
    'DINVALIDCARD' => 'クレジットカード番号が正しくありません。',
    'DINVALIDDATA' => 'リクエストに含まれるデータが不正です。',
    'DMISSINGFIELD' => 'リクエストに必須フィールドが欠けています。',
    'ESYSTEM' => 'システムエラーが発生しました。',
    'ETIMEOUT' => 'リクエストがタイムアウトしました。',
    'DAUTHENTICATE' => 'ユーザは本人認証登録をしています。',
);

// リクエスト先URL配列
$arrCybsRequestURL = array(
    '1' => MDL_CYBS_REQUEST_URL_TEST,
    '2' => MDL_CYBS_REQUEST_URL
);

/**
 *  支払い方法
 *  array(
 *      '[支払い方法]-[分割回数]' => '支払い方法名称・分割回数',
 *      '[支払い方法]-[分割回数]' => '支払い方法名称・分割回数',
 *      ...
 *  );
 */
$arrPayMethod = array(
    '1-0' => "一括払い",
    '4-3' => "分割3回払い",
    '4-6' => "分割6回払い",
    '4-10'=> "分割10回払い",
    '4-15'=> "分割15回払い",
    '4-20'=> "分割20回払い",
    '5-0' => "リボルビング払い"
);

// カード会社の種類
$arrCardCompany = array(
    '001' => 'Visa',
    '002' => 'MasterCard',
    '003' => 'American Express',
    '004' => 'Discover',
    '005' => 'Diners Club',
    '006' => 'Carte Blanche',
    '007' => 'JCB',
    '008' => 'Optima',
    '010' => 'Benificial Private Label',
    '014' => 'EnRoute',
    '017' => 'MBNA',
    '021' => 'JAL',
    '027' => 'Nicos',
);

?>
