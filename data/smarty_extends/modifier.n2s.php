<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty n2s modifier plugin
 *
 * Type:     modifier<br>
 * Name:     n2s<br>
 * Purpose:  formatting number to string.
 * @author   pineray 松田光貴 <matsudaterutaka at gmail dot com>
 * @param string
 * @return string
 */
function smarty_modifier_n2s($number)
{
    $decimals = 0;
    $dec_point = ".";
    $thousands_sep = ",";

    // 引数を取得
    $args = func_get_args();

    // パラメーターの引数があればセットする
    if (count($args) > 1) {
        array_shift($args); // $number
        $decimals = $args[0];
        if (isset($args[1])) {
            $dec_point = $args[1];
        }
        if (isset($args[2])) {
            $thousands_sep = $args[2];
        }
    }

    return number_format(floatval($number), $decimals, $dec_point, $thousands_sep);
}