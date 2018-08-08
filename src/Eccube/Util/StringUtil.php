<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Util;

use Doctrine\Common\Collections\ArrayCollection;

class StringUtil
{
    /**
     * The MIT License (MIT)
     *
     * Copyright (c) <Taylor Otwell>
     *
     * Permission is hereby granted, free of charge, to any person obtaining a copy
     * of this software and associated documentation files (the "Software"), to deal
     * in the Software without restriction, including without limitation the rights
     * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
     * copies of the Software, and to permit persons to whom the Software is
     * furnished to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included in
     * all copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
     * THE SOFTWARE
     *
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int $length
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public static function random($length = 16)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length * 2);

            if ($bytes === false) {
                throw new \RuntimeException('Unable to generate random string.');
            }

            return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
        }

        return static::quickRandom($length);
    }

    /**
     * The MIT License (MIT)
     *
     * Copyright (c) <Taylor Otwell>
     *
     * Permission is hereby granted, free of charge, to any person obtaining a copy
     * of this software and associated documentation files (the "Software"), to deal
     * in the Software without restriction, including without limitation the rights
     * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
     * copies of the Software, and to permit persons to whom the Software is
     * furnished to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included in
     * all copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
     * THE SOFTWARE
     *
     * Generate a "random" alpha-numeric string.
     *
     * Should not be considered sufficient for cryptography, etc.
     *
     * @param  int $length
     *
     * @return string
     */
    public static function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * 改行コードの変換
     *
     * @param $value
     * @param string $lf
     *
     * @return string
     */
    public static function convertLineFeed($value, $lf = "\n")
    {
        if (empty($value)) {
            return '';
        }

        return strtr($value, array_fill_keys(["\r\n", "\r", "\n"], $lf));
    }

    /**
     * 文字コードの判定
     *
     * @param string $value
     *
     * @return string
     */
    public static function characterEncoding($value, $encoding = ['UTF-8', 'SJIS', 'EUC-JP', 'ASCII', 'JIS', 'sjis-win'])
    {
        foreach ($encoding as $encode) {
            if (mb_check_encoding($value, $encode)) {
                return $encode;
            }
        }

        return null;
    }

    /**
     * 指定した文字列以上ある場合、「...」を付加する
     * lengthに7を指定すると、「1234567890」は「1234567...」と「...」を付与して出力される
     *
     * @param string $value
     * @param int $length
     * @param string $end
     *
     * @return string
     */
    public static function ellipsis($value, $length = 100, $end = '...')
    {
        if (mb_strlen($value) <= $length) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $length, 'UTF-8')).$end;
    }

    /**
     * 現在からの経過時間を書式化する.
     *
     * @param $date
     *
     * @return string
     */
    public static function timeAgo($date)
    {
        if (empty($date)) {
            return '';
        }

        $now = new \DateTime();
        if (!($date instanceof \DateTime)) {
            $date = new \DateTime($date);
        }
        $diff = $date->diff($now, true);
        if ($diff->y > 0) {
            // return $date->format("Y/m/d H:i");
            return $date->format('Y/m/d');
        }
        if ($diff->m == 1 || $diff->days > 0) {
            if ($diff->days <= 31) {
                return $diff->days.'日前';
            }
            // return $date->format("Y/m/d H:i");
            return $date->format('Y/m/d');
        }
        if ($diff->h > 0) {
            return $diff->h.'時間前';
        }
        if ($diff->i > 0) {
            return $diff->i.'分前';
        }

        return $diff->s.'秒前';
    }

    /**
     * 変数が空白かどうかをチェックする.
     *
     * 引数 $value が空白かどうかをチェックする. 空白の場合は true.
     * 以下の文字は空白と判断する.
     * - ' ' (ASCII 32 (0x20)), 通常の空白
     * - "\t" (ASCII 9 (0x09)), タブ
     * - "\n" (ASCII 10 (0x0A)), リターン
     * - "\r" (ASCII 13 (0x0D)), 改行
     * - "\0" (ASCII 0 (0x00)), NULバイト
     * - "\x0B" (ASCII 11 (0x0B)), 垂直タブ
     *
     * 引数 $value がオブジェクト型、配列の場合は非推奨とし、 E_USER_DEPRECATED をスローする.
     * EC-CUBE2系からの互換性、ビギナー層を配慮し、以下のような実装とする.
     * 引数 $value が配列の場合は, 空の配列の場合 true を返す.
     * 引数 $value が ArrayCollection::isEmpty() == true の場合 true を返す.
     * 引数 $value が上記以外のオブジェクト型の場合は false を返す.
     *
     * 引数 $greedy が true の場合は, 全角スペース, ネストした空の配列も
     * 空白と判断する.
     *
     * @param string $value チェック対象の変数. 文字型以外も使用できるが、非推奨.
     * @param boolean $greedy '貧欲'にチェックを行う場合 true, デフォルト false
     *
     * @return boolean $value が空白と判断された場合 true
     */
    public static function isBlank($value, $greedy = false)
    {
        $deprecated = '\Eccube\Util\StringUtil::isBlank() の第一引数は文字型、数値を使用してください';
        // テストカバレッジを上げるために return の前で trigger_error をスローしている
        if (is_object($value)) {
            if ($value instanceof ArrayCollection) {
                if ($value->isEmpty()) {
                    @trigger_error($deprecated, E_USER_DEPRECATED);

                    return true;
                } else {
                    @trigger_error($deprecated, E_USER_DEPRECATED);

                    return false;
                }
            }
            @trigger_error($deprecated, E_USER_DEPRECATED);

            return false;
        }
        if (is_array($value)) {
            if ($greedy) {
                if (empty($value)) {
                    @trigger_error($deprecated, E_USER_DEPRECATED);

                    return true;
                }
                $array_result = true;
                foreach ($value as $in) {
                    $array_result = self::isBlank($in, $greedy);
                    if (!$array_result) {
                        @trigger_error($deprecated, E_USER_DEPRECATED);

                        return false;
                    }
                }
                @trigger_error($deprecated, E_USER_DEPRECATED);

                return $array_result;
            } else {
                @trigger_error($deprecated, E_USER_DEPRECATED);

                return empty($value);
            }
        }

        if ($greedy) {
            $value = preg_replace('/　/', '', $value);
        }

        $value = trim($value);
        if (strlen($value) > 0) {
            return false;
        }

        return true;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public static function isNotBlank($value, $greedy = false)
    {
        return !self::isBlank($value, $greedy);
    }

    /**
     * 両端にある全角スペース、半角スペースを取り除く
     *
     * @param $value
     *
     * @return string
     */
    public static function trimAll($value)
    {
        if ($value === '') {
            return '';
        }
        if ($value === 0) {
            return 0;
        }
        if ($value == null) {
            return null;
        }

        return preg_replace('/(^\s+)|(\s+$)/u', '', $value);
    }

    /**
     * envファイルのコンテンツを更新または追加する.
     *
     * @param string $env
     * @param array $replacement
     *
     * @return string
     */
    public static function replaceOrAddEnv($env, array $replacement)
    {
        foreach ($replacement as $key => $value) {
            $pattern = '/^('.$key.')=(.*)/m';
            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, '$1='.$value, $env);
                if ('\\' === DIRECTORY_SEPARATOR) {
                    // The m modifier of the preg functions converts the end-of-line to '\n'
                    $env = self::convertLineFeed($env, "\r\n");
                }
            } else {
                $env .= PHP_EOL."${key}=${value}";
            }
        }

        return $env;
    }
}
