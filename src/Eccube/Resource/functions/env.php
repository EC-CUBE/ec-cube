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
function env($key, $default = null)
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

    switch (strtolower($value)) {
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

    $decoded = json_decode($value, true);
    if ($decoded !== null) {
        return $decoded;
    }

    return $value;
}
