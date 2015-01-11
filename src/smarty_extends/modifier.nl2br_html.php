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

/**
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     nl2br_html<br>
 * Date:     Sep 20, 2008
 * Purpose:  convert \r\n, \r or \n to <<br />>. However, the HTML tag is considered.
 * Example:  {$text|nl2br_html}
 * @author   Seasoft 塚田将久
 * @param string
 * @return string
 */
function smarty_modifier_nl2br_html($string)
{
    $lines = preg_split('/(\\r\\n|\\r|\\n)/', $string);
    $keys = array_keys($lines);
    $last_key = end($keys); // 最終行のキー
    foreach ($keys as $key) {
        if ($key == $last_key) {
            continue; // 最終行はスキップ
        }
        $line = & $lines[$key];
        if (
            !preg_match('/<\/(address|blockquote|caption|center|col|colgroup|dd|del|dir|div|dl|dt|fieldset|form|frame|frameset|h[1-6]|hr|ins|isindex|legend|li|menu|noframes|noscript|ol|optgroup|option|p|pre|table|tbody|td|tfoot|th|thead|tr|ul)>$/i', $line) && !preg_match('/<[a-z0-9]+\s*[^<]*\/?>$/i', $line)
        ) {
            $line .= '<br />';
        }
        $line .= "\n";
    }
    unset($line);

    return implode('', $lines);
}
