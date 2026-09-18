<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Polyfill\Php84\Php84;

require dirname(__DIR__).'/vendor/autoload.php';

// symfony/polyfill-php84 の Php84 クラスをテスト実行前に確実にロードしておく。
// テスト env では Symfony の DebugClassLoader が有効で、kernel 起動後に
// グローバル関数 bcround() から Php84 を遅延 autoload すると
// Php84::bcround() が解決できず、PHP 8.2/8.3 で税計算(bcround)を通す
// 全テストが「Call to undefined method」で失敗する。事前ロードで回避する。
class_exists(Php84::class);

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}
