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
 * CSV インジェクション（表計算ソフトでの数式評価）対策のための無害化・復元処理.
 *
 * ダウンロードした CSV を Excel 等で開く際, 先頭が次の 6 文字
 * (= + - @ ・ タブ(0x09) ・ 復帰(0x0D)) のいずれかの文字列は
 * 数式として評価され, マクロ不要で外部アクセス等を発生させ得る.
 * これを防ぐため出力時に先頭へ ' を付与し, 取込時に剥がす.
 *
 * 単一の先頭 ' は「guard が付与したもの」と「利用者が元から入力したもの」を区別できないため,
 * escape では数式トリガに加えて既に ' で始まる値にも ' を付与する(エスケープ文字自身をエスケープする).
 * これにより unescape は「先頭の ' を 1 つ剥がす」だけで元値を一意に復元でき,
 * 出力→取込の往復が完全な逆写像になる(利用者由来の先頭 ' も保持される).
 *
 * @see https://owasp.org/www-community/attacks/CSV_Injection
 * @see https://symfony.com/blog/cve-2021-41270-prevent-csv-injection-via-formulas
 */
final class CsvFormulaGuard
{
    /**
     * 表計算ソフトが数式の開始とみなし得る先頭文字.
     */
    private const FORMULA_TRIGGER = '/^[=+\-@\t\r]/';

    /**
     * 出力値を無害化する. 数式評価され得る先頭文字, または既に ' で始まる文字列に ' を付与する.
     */
    public static function escape(int|string|null $value): int|string|null
    {
        if (!is_string($value) || $value === '') {
            return $value;
        }

        if (str_starts_with($value, "'") || preg_match(self::FORMULA_TRIGGER, $value)) {
            return "'".$value;
        }

        return $value;
    }

    /**
     * escape() が付与した先頭の ' を 1 つだけ除去し, 元の値を復元する.
     */
    public static function unescape(string $value): string
    {
        if (str_starts_with($value, "'")) {
            return substr($value, 1);
        }

        return $value;
    }
}
