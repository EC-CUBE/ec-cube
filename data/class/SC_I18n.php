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

class SC_I18n {

    /**
     * Store the instance of SC_Helper_Locale_Ex.
     * @var SC_Helper_Locale
     */
    static $_instance = NULL;

    /**
     * Translate a message alias.
     *
     * @param   string  $string     message alias
     * @param   array   $tokens     parameters for translation
     * @param   array   $options    options
     * @return  string  message to display
     */
    public static function t($string, $tokens = array(), $options = array()) {
        is_null(SC_I18n_Ex::$_instance) and SC_I18n_Ex::$_instance = new SC_Helper_Locale_Ex();
        $helper = SC_I18n_Ex::$_instance;

        // If language code is not specified, use site default.
        if (empty($options['lang_code'])) {
            if (defined(LANG_CODE)) {
                $options['lang_code'] = LANG_CODE;
            } else {
                $options['lang_code'] = 'en';
            }
        }
        // If device type ID is not specified, detect the viewing device.
        if (!isset($options['device_type_id']) || ($options['device_type_id'] !== FALSE && !strlen($options['device_type_id']))) {
            $options['device_type_id'] = SC_Display_Ex::detectDevice();
        }

        // Get a string of specified language which corresponds to the message alias.
        $translated = $helper->get_locale($string, $options['lang_code'], $options['device_type_id']);
        
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
    public static function t_plural($count, $single, $plural, $tokens = array(), $options = array()) {
        is_null(SC_I18n_Ex::$_instance) and SC_I18n_Ex::$_instance = new SC_Helper_Locale_Ex();
        $helper = SC_I18n_Ex::$_instance;

        // Add a counter to translation parameters.
        $tokens['T_COUNT'] = number_format($count);

        // If language code is not specified, use site default.
        if (empty($options['lang_code'])) {
            if (defined(LANG_CODE)) {
                $options['lang_code'] = LANG_CODE;
            } else {
                $options['lang_code'] = 'en';
            }
        }
        // If device type ID is not specified, detect the viewing device.
        if (!isset($options['device_type_id']) || ($options['device_type_id'] !== FALSE && !strlen($options['device_type_id']))) {
            $options['device_type_id'] = SC_Display_Ex::detectDevice();
        }

        // Determine appropriate plural form.
        $index = $helper->get_plural_index($count, $options['lang_code']);

        // Plural strings are coupled with a null character.
        $key = $single . chr(0) . $plural;
        // Get a string of specified language which corresponds to the message alias.
        $translated = $helper->get_locale($key, $options['lang_code'], $options['device_type_id']);
        // Divide with a null character.
        $list = explode(chr(0), $translated);

        return strtr($list[$index], $tokens);
    }
}
