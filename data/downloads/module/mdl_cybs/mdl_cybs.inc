<?php
// モジュール番号
define('MDL_CYBS_ID', 14);

// 拡張モジュール名
define('MDL_CYBS_EXT', 'mod_cybs.so');

// 本番URL
define('MDL_CYBS_REQUEST_URL', 'ics2.ic3.com');
// テストURL
define('MDL_CYBS_REQUEST_URL_TEST', 'ics2test.ic3.com');
//
$arrCybsRequestURL = array(
    '1' => MDL_CYBS_REQUEST_URL_TEST,
    '2' => MDL_CYBS_REQUEST_URL
);

// 鍵・証明書のパス
define('MDL_CYBS_KEY_PATH', '/opt/CyberSource/SDK/keys/');
?>
