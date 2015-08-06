<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Util;

class Str
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

            return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
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
     * @return string
     */
    public static function convertLineFeed($value, $lf = "\n")
    {
        if (empty($value)) {
            return '';
        }
        return strtr($value, array_fill_keys(array("\r\n", "\r", "\n"), $lf));
    }

    /**
     * 文字コードの判定
     *
     * @param $value
     * @return string
     */
    public static function characterEncoding($value, $encoding = array('UTF-8', 'SJIS', 'EUC-JP', 'ASCII', 'JIS', 'sjis-win'))
    {
        foreach ($encoding as $encode) {
            if (mb_convert_encoding($value, $encode, $encode) == $value) {
                return $encode;
            }
        }

        return null;

    }

    /**
     * 指定した文字列以上ある場合、「...」を付加する
     *
     * @param string $value
     * @param int $length
     * @param string $end
     * @return string
     */
    public static function ellipsis($value, $length = 100, $end = '...')
    {
        if (mb_strlen($value) <= $length) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $length, 'UTF-8')) . $end;
    }


    /**
     *
     * @param $date
     * @return string
     */
    public static function timeAgo($date)
    {
        $now = new \DateTime();
        if (!($date instanceof \DateTime)) {
            $date = new \DateTime($date);
        }
        $diff = $date->diff($now, true);
        if ($diff->y > 0) {
            // return $date->format("Y/m/d H:i");
            return $date->format("Y/m/d");
        }
        if ($diff->m == 1 || $diff->days > 0) {
            if ($diff->days <= 31) {
                return $diff->days . '日前';
            }
            // return $date->format("Y/m/d H:i");
            return $date->format("Y/m/d");
        }
        if ($diff->h > 0) {
            return $diff->h . "時間前";
        }
        if ($diff->i > 0) {
            return $diff->i . "分前";
        }
        return $diff->s . "秒前";
    }


}