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
use Eccube\Framework\MobileUserAgent;

/**
 * marqueeタグで囲む。
 *
 * DoCoMoの携帯端末の場合はmarqueeを使用しない。
 *
 * @return string 出力
 */
function smarty_block_marquee($params, $content, &$smarty, &$repeat)
{
    // {/marquee}の場合のみ出力する。
    if ($repeat || !isset($content)) {
        return null;
    }

    // 末尾の改行などを取り除く。
    $content = rtrim($content);

    // marqueeタグを使用しない場合
    if (Display::detectDevice() == DEVICE_TYPE_MOBILE && MobileUserAgent::getCarrier() == 'docomo') {
        return "<div>\n$content\n</div>\n";
    }

    return "<marquee>\n$content\n</marquee>\n";
}
