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
 * @param string $name
 * @param null|string $selected
 * @param string $extra
 * @param string $separator
 * @param boolean $labels
 * @param boolean $label_ids
 */
function smarty_function_html_radios_output_ex($name, $value, $output, $selected, $extra, $separator, $labels, $label_ids, $tags)
{
    $_output = '';

    $_output .= '<input type="radio" name="'
            . smarty_function_escape_special_chars($name) . '" value="'
            . smarty_function_escape_special_chars($value) . '"';

    if ($labels && $label_ids) {
        $_id = smarty_function_escape_special_chars(preg_replace('![^\w\-\.]!', '_', $name . '_' . $value));
        $_output .= ' id="' . $_id . '"';
    }
    if ((string) $value == $selected) {
        $_output .= ' checked="checked"';
    }

    $_output .= $extra . ' />';

    $_output .= $tags[0];

    if ($labels) {
        if ($label_ids) {
            $_id = smarty_function_escape_special_chars(preg_replace('![^\w\-\.]!', '_', $name . '_' . $value));
            $_output .= '<label for="' . $_id . '">';
        } else {
            $_output .= '<label>';
        }
    }

    // 値を挿入
    $_output .= $output;

    $_output .= $tags[1];

    if ($labels) {
        $_output .= '</label>';
    }
    $_output .= $separator;

    return $_output;
}
