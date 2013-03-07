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
// $Id: array_diff_assoc.php,v 1.12 2005/12/07 21:08:57 aidan Exp $


/**
 * Replace array_diff_assoc()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.array_diff_assoc
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.12 $
 * @since       PHP 4.3.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('array_diff_assoc')) {
    function array_diff_assoc()
    {
        // Check we have enough arguments
        $args = func_get_args();
        $count = count($args);
        if (count($args) < 2) {
            user_error('Wrong parameter count for array_diff_assoc()', E_USER_WARNING);
            return;
        }

        // Check arrays
        for ($i = 0; $i < $count; $i++) {
            if (!is_array($args[$i])) {
                user_error('array_diff_assoc() Argument #' .
                    ($i + 1) . ' is not an array', E_USER_WARNING);
                return;
            }
        }

        // Get the comparison array
        $array_comp = array_shift($args);
        --$count;

        // Traverse values of the first array
        foreach ($array_comp as $key => $value) {
            // Loop through the other arrays
            for ($i = 0; $i < $count; $i++) {
                // Loop through this arrays key/value pairs and compare
                foreach ($args[$i] as $comp_key => $comp_value) {
                    if ((string)$key === (string)$comp_key &&
                        (string)$value === (string)$comp_value)
                    {

                        unset($array_comp[$key]);
                    }
                }
            }
        }

        return $array_comp;
    }
}

?>
