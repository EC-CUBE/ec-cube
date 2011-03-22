<?php
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Aidan Lister <aidan@php.net>                                |
// +----------------------------------------------------------------------+
//
// $Id: ini_get_all.php,v 1.3 2005/01/26 04:55:13 aidan Exp $


/**
 * Replace ini_get_all()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.ini_get_all
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.3 $
 * @since       PHP 4.2.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('ini_get_all')) {
    function ini_get_all($extension = null)
    {
        // Sanity check
        if (!is_scalar($extension)) {
            user_error('ini_get_all() expects parameter 1 to be string, ' .
                gettype($extension) . ' given', E_USER_WARNING);
            return false;
        }
        
        // Get the location of php.ini
        ob_start();
        phpinfo(INFO_GENERAL);
        $info = ob_get_contents();
        ob_clean();
        $info = explode("\n", $info);
        $line = array_values(preg_grep('#php.ini#', $info));
        list (, $value) = explode('<td class="v">', $line[0]);
        $inifile = trim(strip_tags($value));

        // Parse
        if ($extension !== null) {
            $ini_all = parse_ini_file($inifile, true);

            // Lowercase extension keys
            foreach ($ini_all as $key => $value) {
                $ini_arr[strtolower($key)] = $value;
            }

            $ini = $ini_arr[$extension];
        } else {
            $ini = parse_ini_file($inifile);
        }

        // Order
        $ini_lc = array_map('strtolower', array_keys($ini));
        array_multisort($ini_lc, SORT_ASC, SORT_STRING, $ini);

        // Format
        $info = array();
        foreach ($ini as $key => $value) {
            $info[$key] = array(
                'global_value'  => $value,
                'local_value'   => ini_get($key),
                // No way to know this
                'access'        => -1
            );
        }

        return $info;
    }
}

?>
