<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 *
 * モバイルサイト共有設定ファイル
 */

// MOBILE_COMPILE_DIR が無ければ作る
if (!file_exists(MOBILE_COMPILE_DIR)) {
        mkdir(MOBILE_COMPILE_DIR);
}
/**
 * モバイルサイトであることを表す定数
 */
define('MOBILE_SITE', true);
?>