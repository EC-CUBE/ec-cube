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

/**
 * .env 系ファイルを読み込む.
 *
 * Dotenv::bootEnv() は .env.local.php（dump-env の最適化済みスナップショット）を優先し,
 * 無ければ .env → .env.local → .env.$env → .env.$env.local をカスケード読み込みする.
 *
 * ただし bootEnv() は APP_ENV が prod 以外のとき APP_DEBUG を自動導出して
 * $_SERVER / $_ENV へ書き込む. EC-CUBE は APP_DEBUG 未定義を「未指定」として扱い,
 * デバッグ判定は index.php / bin/console 側で, 画面表示は env('APP_DEBUG') で行うため,
 * 導出値が混入すると .env に APP_DEBUG を持たない環境（APP_ENV=codeception 等）で
 * 挙動が変わってしまう. これを避けるため, 導出先を未使用のキーへ逃がして破棄する.
 * .env 系や OS 環境変数に APP_DEBUG が定義されている場合は, 従来どおりその値が使われる.
 *
 * @param string $path .env ファイルのパス
 * @param bool $overrideExistingVars true の場合, .env 系の値が既存の環境変数より優先される
 */
function boot_env(string $path, bool $overrideExistingVars = false): void
{
    $unusedDebugKey = '__ECCUBE_UNUSED_APP_DEBUG';

    (new Dotenv('APP_ENV', $unusedDebugKey))->bootEnv($path, 'dev', ['test'], $overrideExistingVars);

    unset($_SERVER[$unusedDebugKey], $_ENV[$unusedDebugKey]);
}

/**
 * ECCUBE_UMASK が設定されている場合に umask を適用する.
 *
 * umask はコンテナを生成するより前に決める必要があるため, コンテナのパラメータではなく
 * 環境変数から読み込む (app/config/eccube/packages/eccube.yaml の eccube_umask で既定値を宣言している).
 *
 * 未設定の場合は OS / PHP-FPM の既定 umask に従う. Web サーバーと CLI が別ユーザーで,
 * かつ双方が同じファイルへ書き込む必要がある環境では '0000' を設定すると, 生成される
 * ディレクトリが 0777, ファイルが 0666 になり相互に書き込めるようになる (4.3 以前の既定).
 * ただし同一サーバーの他ユーザーからも書き換え可能になるため, 権限を分離できる環境では
 * 設定しないこと.
 *
 * 値は 8 進数表記の文字列 (例: '0022', '0000'). 解釈できない値は無視する.
 */
function apply_umask(): void
{
    $value = env('ECCUBE_UMASK');
    if ($value === null || $value === '') {
        return;
    }

    $value = (string) $value;
    if (!preg_match('/\A0?[0-7]{1,4}\z/', $value)) {
        return;
    }

    umask((int) octdec($value));
}

/**
 * Gets the value of an environment variable. Supports boolean, null and empty values.
 *
 * Symfony Dotenv は putenv() をデフォルトで使用しないため（スレッドセーフではないため）、
 * $_ENV と $_SERVER を優先的に使用します。
 *
 * @param string $key The environment variable key
 * @param mixed $default The default value to return if the environment variable does not exist
 *
 * @return mixed The environment variable value or the default value
 */
function env(string $key, mixed $default = null): mixed
{
    // Symfony Dotenv は $_ENV と $_SERVER に環境変数を設定するため、これらを優先
    if (isset($_ENV[$key])) {
        $value = $_ENV[$key];
    } elseif (isset($_SERVER[$key])) {
        $value = $_SERVER[$key];
    } else {
        // 古い環境との互換性のため getenv() もフォールバックとして使用
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
    }

    switch (strtolower((string) $value)) {
        case 'true':
            return true;
        case 'false':
            return false;
        case 'null':
            return null;
    }

    if ($value === '') {
        return $value;
    }

    $decoded = json_decode((string) $value, true);
    if ($decoded !== null) {
        return $decoded;
    }

    return $value;
}
