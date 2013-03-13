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

require DATA_REALDIR . 'module/Locale/streams.php';
require DATA_REALDIR . 'module/Locale/gettext.php';

/**
 * Helper class for localization.
 * Library of static method.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Helper_Locale {

    /**
     * Store the instance of SC_Helper_Locale_Ex.
     * @var SC_Helper_Locale
     */
    static $_instance = NULL;

    public $_translations = array();

     /**
     * Return a string which corresponding with message alias.
     *
     * @param   string  $string     message alias
     * @param   array   $options    options
     * @return  string  a string corresponding with message alias
     */
    public static function get_locale($string, &$options) {
        is_null(SC_Helper_Locale_Ex::$_instance) and SC_Helper_Locale_Ex::$_instance = new SC_Helper_Locale_Ex();

        // If language code is not specified, use site default.
        if (empty($options['lang_code'])) {
            $lang_code = $options['lang_code'] = defined('LANG_CODE') ? LANG_CODE : 'en-US';
        } else {
            $lang_code = $options['lang_code'];
        }
        // If device type ID is not specified, detect the viewing device.
        if (!isset($options['device_type_id']) || ($options['device_type_id'] !== FALSE && !strlen($options['device_type_id']))) {
            if (method_exists('SC_Display_Ex', 'detectDevice')) {
                $device_type_id = SC_Display_Ex::detectDevice();
            } else {
                $device_type_id = FALSE;
            }
        } else {
            $device_type_id = $options['device_type_id'];
        }

        $return = $string;

        // Get string list of specified language.
        $translations = SC_Helper_Locale_Ex::$_instance->get_translations($lang_code, $device_type_id);
        // Whether a string which corresponding with alias is exist.
        if (isset($translations[$return])) {
            $return = $translations[$return];
        }

        if (is_array($options['escape'])) {
            foreach ($options['escape'] as $esc_type) {
                $return = SC_Helper_Locale_Ex::escape($return, $esc_type);
            }
        }

        return $return;
    }

    /**
     * Return a string which corresponding with message alias.
     *
     * @param   string  $single     message alias (single)
     * @param   string  $plural     message alias (plural)
     * @param   array   $options    options
     * @return  array
     */
    public static function get_locale_plural($single, $plural, &$options) {
        // Plural strings are coupled with a null character.
        $key = $single . chr(0) . $plural;
        // Get a string of specified language which corresponds to the message alias.
        $translated = SC_Helper_Locale_Ex::get_locale($key, $options);
        // Divide with a null character.
        return explode(chr(0), $translated);
    }

    /**
     * Get the strings of specified language from locale files.
     *
     * @param   string  $lang_code      language code
     * @param   integer $device_type_id device type ID
     * @return  array   strings
     */
    function get_translations($lang_code, $device_type_id = FALSE) {
        $translations_key = "translations_" . $lang_code . "_" . $device_type_id;
        // If the strings of specified language is not loaded
        if (empty($this->_translations[$translations_key])) {
            $translations = array();

            // Get a list of files to load.
            $file_list = $this->get_locale_file_list($lang_code, $device_type_id);

            // Get the strings from each locale file using php_gettext.
            foreach ($file_list as $locale_file) {
                $stream = new FileReader($locale_file);
                $gettext = new gettext_reader($stream);

                $gettext->load_tables();
                $translations = array_merge($translations, $gettext->cache_translations);
            }

            $this->_translations[$translations_key] = $translations;
        }

        return $this->_translations[$translations_key];
    }

    /**
     * Get a list of locale files.
     *
     * @param   string  $lang_code      language code
     * @param   integer $device_type_id device type ID
     * @return  array   file list
     */
    function get_locale_file_list($lang_code, $device_type_id = FALSE) {
        $file_list = array();

        // Path to the EC-CUBE Core locale file.
        $core_locale_path = DATA_REALDIR . "locales/{$lang_code}.mo";
        // If a locale file of specified language is exist, add to the file list.
        if (file_exists($core_locale_path)) {
            $file_list[] = $core_locale_path;
        }

        // Get a list of enabled plugins.
        if (defined('ECCUBE_INSTALL')) {
            $arrPluginDataList = SC_Plugin_Util_Ex::getAllPlugin();
            // Get the plugins directory.
            $arrPluginDirectory = SC_Plugin_Util_Ex::getPluginDirectory();
            foreach ($arrPluginDataList as $arrPluginData) {
                // Check that the plugin filename is contained in the list of plugins directory.
                if (array_search($arrPluginData['plugin_code'], $arrPluginDirectory) !== false) {
                    // Path to the plugin locale file.
                    $plugin_locale_path = PLUGIN_UPLOAD_REALDIR . $arrPluginData['plugin_code'] . "/locales/{$lang_code}.mo";
                    // If a locale file of specified language is exist, add to the file list.
                    if (file_exists($plugin_locale_path)) {
                        $file_list[] = $plugin_locale_path;
                    }
                }
            }
        }

        // Path to the template locale file.
        if ($device_type_id !== FALSE) {
            $template_locale_path = HTML_REALDIR . SC_Helper_PageLayout_Ex::getUserDir($device_type_id, true) . "locales/{$lang_code}.mo";
            // If a locale file of specified language is exist, add to the file list.
            if (file_exists($template_locale_path)) {
                $file_list[] = $template_locale_path;
            }
        }

        return $file_list;
    }

    /**
     * 文字列のエスケープを行う
     *
     * @param   string  $string     エスケープを行う文字列
     * @param   string  $esc_type   エスケープの種類
     * @return  string  エスケープを行った文字列
     */
    static function escape($string, $esc_type) {
        $return = $string;

        switch ($esc_type) {
            case 'h':
            case 'html':
                $return = htmlspecialchars($return, ENT_QUOTES);
                break;

            case 'j':
            case 'javascript':
                // escape quotes and backslashes, newlines, etc.
                $return = strtr($return, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
                break;

            case 'nl2br':
                $return = nl2br($return);
                break;

            case '':
            case 'none':
                break;

            case 'htmlall':
                $return = htmlentities($return, ENT_QUOTES);
                break;

            case 'u':
            case 'url':
                $return = rawurlencode($return);
                break;

            case 'urlpathinfo':
                $return = str_replace('%2F','/',rawurlencode($return));
                break;

            case 'quotes':
                // escape unescaped single quotes
                $return = preg_replace("%(?<!\\\\)'%", "\\'", $return);
                break;

            case 'hex':
                // escape every character into hex
                $text = '';
                for ($x=0; $x < strlen($return); $x++) {
                    $text .= '%' . bin2hex($return[$x]);
                }
                $return = $text;
                break;

            case 'hexentity':
                $text = '';
                for ($x=0; $x < strlen($return); $x++) {
                    $text .= '&#x' . bin2hex($return[$x]) . ';';
                }
                $return = $text;
                break;

            case 'decentity':
                $text = '';
                for ($x=0; $x < strlen($return); $x++) {
                    $text .= '&#' . ord($return[$x]) . ';';
                }
                $return = $text;
                break;

            case 'mail':
                // safe way to display e-mail address on a web page
                $return = str_replace(array('@', '.'),array(' [AT] ', ' [DOT] '), $return);
                break;

            case 'nonstd':
                // escape non-standard chars, such as ms document quotes
                $_res = '';
                for($_i = 0, $_len = strlen($return); $_i < $_len; $_i++) {
                    $_ord = ord(substr($return, $_i, 1));
                    // non-standard char, escape it
                    if($_ord >= 126){
                        $_res .= '&#' . $_ord . ';';
                    }
                    else {
                        $_res .= substr($return, $_i, 1);
                    }
                }
                $return = $_res;
                break;

            default:
                trigger_error('unknown escape type. ' . var_export(func_get_args(), true), E_USER_WARNING);
                break;
        }

        return $return;
    }
}
