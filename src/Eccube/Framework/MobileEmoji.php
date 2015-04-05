<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework;

use Eccube\Application;
use Eccube\Framework\MobileUserAgent;

/**
 * 表示できない絵文字を置き換える文字列 (Shift JIS)
 * デフォルトは空文字列。
 */
define('MOBILE_EMOJI_SUBSTITUTE', '');

/**
 * 携帯端末の絵文字を扱うクラス
 */
class MobileEmoji
{
    /**
     * 絵文字タグを各キャリア用の文字コードに変換する
     * output buffering 用コールバック関数
     *
     * @param string 入力
     * @return string 出力
     */
    public static function handler($buffer)
    {
        return preg_replace_callback('/\[emoji:(e?\d+)\]/', function ($matches) {
            $index = $matches[1];

            $carrier = MobileUserAgent::getCarrier();
            if ($carrier === false) {
                return MOBILE_EMOJI_SUBSTITUTE;
            }

            static $arrMap = array();
            if (empty($arrMap)) {
                $arrMap = @include_once dirname(__FILE__) . "/../../mobile_emoji/mobile_emoji_map_$carrier.inc";
            }

            return isset($arrMap[$index]) ? $arrMap[$index] : MOBILE_EMOJI_SUBSTITUTE;
        }, $buffer);
    }
}
