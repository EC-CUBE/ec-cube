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
 * Smarty {from_to} function plugin
 *
 * Type:       function<br>
 * Name:       from_to<br>
 * Date:       2008/09/07<br>
 * Input:
 * <pre>
 *           - from       (required) - string
 *           - to         (required) - string
 *           - separator  (optional) - string default " ～ ". ie "-", "～<br />". 常にエスケープせずに出力するので注意.
 *           - escape     (optional) - string default true. other false. エスケープするか否か。
 * </pre>
 * Examples:
 * <pre>
 * {html_radios from="-1" to="2"} → -1 ～ 2
 * {html_radios from="B" to="a" separator="～<br />"}  → B～<br />a
 * </pre>
 * @author     Seasoft 塚田将久
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_from_to($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared', 'escape_special_chars');

    $from = null;
    $to = null;
    $separator = ' ～ ';
    $escape = true;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'from':
            case 'to':
            case 'separator':
            case 'escape':
                $$_key = (string) $_val;
                break;

            default:
                $smarty->trigger_error("from_to: extra attribute '$_key' is unknown.", E_USER_NOTICE);
                break;
        }
    }

    if ($escape) {
        $from = smarty_function_escape_special_chars($from);
        $to = smarty_function_escape_special_chars($to);
    }

    if ($from === $to) {
        return $from;
    } else {
        return $from . $separator . $to;
    }
}
