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

use Eccube\Framework\Display;

/**
 * 数値を数字絵文字に変換する。
 *
 * 入力が0～9ではない場合、または、携帯端末からのアクセスではない場合は、
 * 入力を [ と ] で囲んだ文字列を返す。
 *
 * @param string $value 入力
 * @return string 出力
 */
function smarty_modifier_numeric_emoji($value)
{
    // 数字絵文字 (0～9) の絵文字番号
    static $numeric_emoji_index = array('134', '125', '126', '127', '128', '129', '130', '131', '132', '133');

    if ((Display::detectDevice() == DEVICE_TYPE_MOBILE) && isset($numeric_emoji_index[$value])
    ) {
        return '[emoji:' . $numeric_emoji_index[$value] . ']';
    } else {
        return '[' . $value . ']';
    }
}
