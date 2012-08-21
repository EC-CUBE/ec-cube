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

     public $_translations = array();

     public $_plural_forms = array();

     /**
     * Return a string which corresponding with message alias.
     *
     * @param   string  $string             message alias
     * @param   string  $lang_code          language code
     * @param   integer $device_type_id     device type ID
     * @return  string  a string corresponding with message alias
     */
    function get_locale($string, $lang_code = LANG_CODE, $device_type_id = DEVICE_TYPE_PC) {
        // Get string list of specified language.
        $translations = $this->get_translations($lang_code, $device_type_id);
        // Whether a string which corresponding with alias is exist.
        if (isset($translations[$string])) {
            return $translations[$string];
        }
        else {
            return $string;
        }
    }

    /**
     * Get the strings of specified language from locale files.
     *
     * @param   string  $lang_code      language code
     * @param   integer $device_type_id device type ID
     * @return  array   strings
     */
    function get_translations($lang_code = LANG_CODE, $device_type_id = DEVICE_TYPE_PC) {
        // If the strings of specified language is not loaded
        if (empty($this->_translations[$lang_code][$device_type_id])) {
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

            $this->_translations[$lang_code][$device_type_id] = $translations;
        }

        return $this->_translations[$lang_code][$device_type_id];
    }

    /**
     * Get a list of locale files.
     *
     * @param   string  $lang_code      language code
     * @param   integer $device_type_id device type ID
     * @return  array   file list
     */
    function get_locale_file_list($lang_code = LANG_CODE, $device_type_id = DEVICE_TYPE_PC) {
        $file_list = array();

        // Path to the EC-CUBE Core locale file.
        $core_locale_path = DATA_REALDIR . "locales/{$lang_code}.mo";
        // If a locale file of specified language is exist, add to the file list.
        if (file_exists($core_locale_path)) {
            $file_list[] = $core_locale_path;
        }

        // Get a list of enabled plugins.
        $arrPluginDataList = SC_Plugin_Util_Ex::getEnablePlugin();
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

        // Path to the template locale file.
        $template_locale_path = HTML_REALDIR . SC_Helper_PageLayout_Ex::getUserDir($device_type_id, true) . "locales/{$lang_code}.mo";
        // If a locale file of specified language is exist, add to the file list.
        if (file_exists($template_locale_path)) {
            $file_list[] = $template_locale_path;
        }

        return $file_list;
    }

    /**
     * Determine appropriate plural form.
     *
     * @param integer   $count      counter
     * @param string    $lang_code  language code
     * @return integer  index
     */
    function get_plural_index($count, $lang_code = LANG_CODE) {
        // Get a formula
        $string = $this->get_plural_forms($lang_code);
        $string = str_replace('nplurals', "\$total", $string);
        $string = str_replace("n", $count, $string);
        $string = str_replace('plural', "\$plural", $string);

        $total = 0;
        $plural = 0;

        eval("$string");
        if ($plural >= $total) $plural = $total - 1;

        return $plural;
    }

    /**
     * Get a formula to determine appropriate plural form.
     *
     * @param   string  $lang_code  language code
     * @return  string  formula
     */
    function get_plural_forms($lang_code = LANG_CODE) {
        // If formula is empty, include the file.
        if(empty($this->_plural_forms)){
            $this->_plural_forms = @include_once DATA_REALDIR . "include/plural_forms.inc";
        }

        return $this->_plural_forms[$lang_code];
    }
}
