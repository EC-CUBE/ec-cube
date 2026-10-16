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

namespace Eccube\Util;

/**
 * リクエスト由来の値を、整数型の ID として DB へ渡せるか検証する.
 *
 * 文字列や範囲外の値をそのまま問い合わせると、PostgreSQL では
 * invalid input syntax (22P02) や out of range (22003) で例外になる.
 */
final class IdUtil
{
    public const SMALLINT_MAX = 32767;

    public const INTEGER_MAX = 2147483647;

    /**
     * 0 以上 $max 以下の整数、またはその 10 進表記の文字列であれば int で返す. それ以外は null.
     */
    public static function toId(mixed $value, int $max = self::INTEGER_MAX): ?int
    {
        if (is_int($value)) {
            $id = $value;
        } elseif (is_string($value) && preg_match('/\A[0-9]{1,19}\z/', $value)) {
            $id = filter_var(ltrim($value, '0') ?: '0', FILTER_VALIDATE_INT);
            if (false === $id) {
                return null;
            }
        } else {
            return null;
        }

        return $id >= 0 && $id <= $max ? $id : null;
    }

    /**
     * Doctrine の型名に対応する ID の上限を返す. 整数型でなければ null.
     */
    public static function maxForType(?string $type): ?int
    {
        return match ($type) {
            'smallint' => self::SMALLINT_MAX,
            'integer' => self::INTEGER_MAX,
            'bigint' => PHP_INT_MAX,
            default => null,
        };
    }
}
