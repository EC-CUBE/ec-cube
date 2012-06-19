<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

/**
 * 表示できない絵文字を置き換える文字列 (Shift JIS)
 * デフォルトは空文字列。
 */
define('MOBILE_EMOJI_SUBSTITUTE', '');

/**
 * 携帯端末の絵文字を扱うクラス
 */
class SC_MobileEmoji {
    /**
     * 絵文字タグを各キャリア用の文字コードに変換する
     * output buffering 用コールバック関数
     *
     * @param string 入力
     * @return string 出力
     */
    static function handler($buffer) {
        $replace_callback = create_function('$matches', 'return SC_MobileEmoji_Ex::indexToCode($matches[1]);');
        return preg_replace_callback('/\[emoji:(e?\d+)\]/', $replace_callback, $buffer);
    }

    /**
     * 絵文字番号を絵文字を表す Shift JIS の文字列に変換する。
     *
     * @param string $index 絵文字番号
     * @return string 絵文字を表す Shift JIS の文字列を返す。
     */
    function indexToCode($index) {
        $carrier = SC_MobileUserAgent_Ex::getCarrier();
        if ($carrier === false) {
            return MOBILE_EMOJI_SUBSTITUTE;
        }

        static $arrMap = array();
        if (empty($arrMap)) {
            $arrMap = @include_once dirname(__FILE__) . "/../include/mobile_emoji_map_$carrier.inc";
        }

        return isset($arrMap[$index]) ? $arrMap[$index] : MOBILE_EMOJI_SUBSTITUTE;
    }
}
