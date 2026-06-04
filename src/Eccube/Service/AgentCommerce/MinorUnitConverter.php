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

namespace Eccube\Service\AgentCommerce;

use Symfony\Component\Intl\Currencies;

/**
 * 通貨の major-unit (人間が扱う表記) と minor-unit (整数表現) を相互変換するサービス.
 *
 * ACP / UCP の金額はいずれも minor-unit (最小通貨単位) の整数で表現される。
 * minor-unit は major-unit を「通貨の小数桁数 (ISO 4217) だけ 10 倍した整数」で、
 * 同じ "1000" でも通貨ごとに桁数が変わる (JPY は ×1、USD は ×100)。一律 ×100 ではない点に注意。
 * 桁数は symfony/intl の権威データから取得するため、ゼロデシマル通貨 (JPY/KRW/TWD) や
 * 3 桁通貨 (BHD 等) も自動で正しく扱える (EC-CUBE 本体の金額カラムは 2 桁まで)。
 * 割引・返金などの負数にも対応する。
 */
class MinorUnitConverter
{
    /**
     * major-unit の金額文字列を minor-unit の整数へ変換する.
     *
     * minor-unit は「通貨の小数桁数 (ISO 4217) だけ 0 が増えた整数」。
     * 同じ金額でも通貨によって桁数が変わる (一律 ×100 ではない):
     *   ("1000",    "JPY") => 1000     // 0 桁通貨: ×10^0 → 桁は増えない
     *   ("1000.00", "USD") => 100000   // 2 桁通貨: ×10^2 → 0 が 2 つ増える
     *   ("12.34",   "USD") => 1234
     *   ("-15.00",  "USD") => -1500    // 割引・返金などの負数も可
     *
     * @param string $amount   major-unit の金額 (decimal 文字列). 負数可
     * @param string $currency ISO 4217 通貨コード (例: JPY, USD)
     */
    public function toMinorUnits(string $amount, string $currency): int
    {
        $amount = trim($amount);
        $digits = $this->getFractionDigits($currency);

        // 符号を分離して非負の値として丸め、最後に符号を戻す (round-half-up を絶対値で行うため)。
        $negative = false;
        if (str_starts_with($amount, '-')) {
            $negative = true;
            $amount = substr($amount, 1);
        } elseif (str_starts_with($amount, '+')) {
            $amount = substr($amount, 1);
        }

        // 空や不正な表記は 0 とみなす。
        if ($amount === '' || $amount === '.') {
            return 0;
        }

        // scale を桁数 + 1 にして、丸め用の補正を加える。
        $scale = $digits + 1;
        $factor = bcpow('10', (string) $digits, 0);

        // amount * 10^digits を計算 (まだ小数を含みうる)。
        $scaled = bcmul($amount, $factor, $scale);

        // round-half-up: 正の値に 0.5 を足してから切り捨て (bcmath は truncate)。
        $rounded = bcadd($scaled, '0.5', 0);

        $result = (int) $rounded;

        return $negative ? -$result : $result;
    }

    /**
     * minor-unit の整数を major-unit の decimal 文字列へ変換する (toMinorUnits の逆変換).
     *
     *   (1000,   "JPY") => "1000"     // 0 桁通貨: そのまま
     *   (100000, "USD") => "1000.00"  // 2 桁通貨: 下 2 桁が小数部
     *   (1234,   "USD") => "12.34"
     *   (-1500,  "USD") => "-15.00"
     *
     * @param int    $minorUnits minor-unit の整数. 負数可
     * @param string $currency   ISO 4217 通貨コード
     */
    public function toAmountString(int $minorUnits, string $currency): string
    {
        $digits = $this->getFractionDigits($currency);

        if ($digits === 0) {
            return (string) $minorUnits;
        }

        $factor = bcpow('10', (string) $digits, 0);

        // bcdiv は scale 桁で丸めずに切り捨てるが、minor->major は割り切れるため scale=digits で正確。
        return bcdiv((string) $minorUnits, $factor, $digits);
    }

    /**
     * 通貨の小数桁数を ISO 4217 権威データから取得する.
     *
     * 未知の通貨コードの場合は安全側に倒して 2 桁を返す。
     */
    private function getFractionDigits(string $currency): int
    {
        $currency = strtoupper(trim($currency));

        if ($currency !== '' && Currencies::exists($currency)) {
            return Currencies::getFractionDigits($currency);
        }

        return 2;
    }
}
