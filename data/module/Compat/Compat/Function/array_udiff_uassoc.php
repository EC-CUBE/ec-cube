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
// $Id: array_udiff_uassoc.php,v 1.8 2005/01/26 04:55:13 aidan Exp $


/**
 * Replace array_udiff_uassoc()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.array_udiff_uassoc
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.8 $
 * @since       PHP 5
 * @require     PHP 4.0.6 (is_callable)
 */
if (!function_exists('array_udiff_uassoc')) {
    function array_udiff_uassoc()
    {
        $args = func_get_args();
        if (count($args) < 3) {
            user_error('Wrong parameter count for array_udiff_uassoc()', E_USER_WARNING);
            return;
        }

        // Get compare function
        $compare_func = array_pop($args);
        if (!is_callable($compare_func)) {
            if (is_array($compare_func)) {
                $compare_func = $compare_func[0] . '::' . $compare_func[1];
            }
            user_error('array_udiff_uassoc() Not a valid callback ' . $compare_func, E_USER_WARNING);
            return;
        }

        // Check arrays
        $count = count($args);
        for ($i = 0; $i < $count; $i++) {
            if (!is_array($args[$i])) {
                user_error('array_udiff_uassoc() Argument #' .
                    ($i + 1) . ' is not an array', E_USER_WARNING);
                return;
            }
        }

        // Traverse values of the first array
        $diff = array ();
        foreach ($args[0] as $key => $value) {
            // Check all arrays
            for ($i = 1; $i < $count; $i++) {
                if (!array_key_exists($key, $args[$i])) {
                    continue;
                }
                $result = call_user_func($compare_func, $value, $args[$i][$key]);
                if ($result === 0) {
                    continue 2;
                }
            }

            $diff[$key] = $value;
        }

        return $diff;
    }
}

?>