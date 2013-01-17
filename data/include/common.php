<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
 * @file
 * Common functions that many EC-CUBE classes will need to reference.
 */

/**
 * Translate a message alias.
 *
 * @param   string  $string     message alias
 * @param   array   $tokens     parameters for translation
 * @param   array   $options    options
 * @return  string  message to display
 */
function t($string, $tokens = array(), $options = array()) {
    if (method_exists('SC_Helper_Locale_Ex', 'get_locale')) {
        // Get a string of specified language which corresponds to the message alias.
        $translated = SC_Helper_Locale_Ex::get_locale($string, $options);
    } else {
        $translated = $string;
    }

    // If parameters are set, translate a message.
    if (empty($tokens)) {
      return $translated;
    }
    else {
      return strtr($translated, $tokens);
    }
}

/**
 * Translate a message alias (plural).
 *
 * @param   integer $count      number for detecting format
 * @param   string  $single     message alias (single)
 * @param   string  $plural     message alias (plural)
 * @param   array   $tokens     parameters for translation
 * @param   array   $options    options
 * @return  string  message to display
 */
function t_plural($count, $single, $plural, $tokens = array(), $options = array()) {
    if (method_exists('SC_Helper_Locale_Ex', 'get_locale_plural')) {
        list($translated_single, $translated_plural) = SC_Helper_Locale_Ex::get_locale_plural($single, $plural, $options);
    } else {
        $translated_single = $single;
        $translated_plural = $plural;
        $options['lang_code'] = 'en-US';
    }

    if ($count == 1) {
        $return = $translated_single;
    } else {
        // Determine appropriate plural form.
        $index = get_plural_index($count, $options['lang_code']);

        if ($index < 0) {
            $return = $translated_plural;
        } else {
            switch ($index) {
                case "0":
                    $return = $translated_single;
                case "1":
                default:
                    $return = $translated_plural;
            }
        }
    }

    // Add a counter to translation parameters.
    $tokens['T_COUNT'] = number_format($count);

    return strtr($return, $tokens);
}

/**
 * Determine appropriate plural form.
 *
 * @param integer   $count      counter
 * @param string    $lang_code  language code
 * @return integer  index
 */
function get_plural_index($count, $lang_code = 'en-US') {
    static $plural_indexes = array();

    if (!isset($plural_indexes[$lang_code][$count])) {
        // Get a formula
        $formula = get_plural_formula($lang_code);

        // If there is a plural formula for the language, evaluate it
        if (!empty($formula)) {
            $string = str_replace('nplurals', "\$total", $formula);
            $string = str_replace("n", $count, $string);
            $string = str_replace('plural', "\$plural", $string);

            $total = 0;
            $plural = 0;

            eval("$string");
            if ($plural >= $total) $plural = $total - 1;

            $plural_indexes[$lang_code][$count] = $plural;
        // If there is no plural formula for English
        } elseif ($lang_code == 'en-US') {
            $plural_indexes[$lang_code][$count] = (int) ($count != 1);
        // Otherwise, return -1 (unknown).
        } else {
            $plural_indexes[$lang_code][$count] = -1;
        }
    }

    return $plural_indexes[$lang_code][$count];
}

/**
 * Get a formula to determine appropriate plural form.
 *
 * @param   string  $lang_code  language code
 * @return  string  formula
 */
function get_plural_formula($lang_code) {
    static $plural_formulas = array();

    // If formula is empty, include the file.
    if(empty($plural_formulas)){
        $plural_formulas = @include_once DATA_REALDIR . "include/plural_forms.inc";
    }

    return isset($plural_formulas[$lang_code]) ? $plural_formulas[$lang_code] : NULL;
}
