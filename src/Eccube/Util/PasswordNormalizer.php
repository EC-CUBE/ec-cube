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
 * パスワードの Unicode 正規化を行うユーティリティ.
 *
 * NIST SP 800-63B-4 に従い, パスワード設定時とログイン照合時で表記ゆれを統一するため,
 * NFKC 正規化を適用する. ext-intl(\Normalizer) が利用できない環境では入力値をそのまま返す.
 */
class PasswordNormalizer
{
    /**
     * パスワードを NFKC 正規化する.
     *
     * @param string|null $password 正規化対象のパスワード
     *
     * @return string|null 正規化後のパスワード(\Normalizer 未導入時や正規化失敗時は入力値のまま)
     */
    public static function normalize(?string $password): ?string
    {
        if ($password === null || $password === '') {
            return $password;
        }

        if (!class_exists(\Normalizer::class)) {
            return $password;
        }

        $normalized = \Normalizer::normalize($password, \Normalizer::FORM_KC);

        return $normalized === false ? $password : $normalized;
    }
}
