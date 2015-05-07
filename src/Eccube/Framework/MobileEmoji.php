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
