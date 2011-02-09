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
// | Authors: Tom Buskens <ortega@php.net>                                |
// |          Aidan Lister <aidan@php.net>                                |
// +----------------------------------------------------------------------+
//
// $Id: array_uintersect.php,v 1.9 2005/05/10 12:05:48 aidan Exp $


/**
 * Replace array_uintersect()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.array_uintersect
 * @author      Tom Buskens <ortega@php.net>
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.9 $
 * @since       PHP 5
 * @require     PHP 4.0.6 (is_callable)
 */
if (!function_exists('array_uintersect')) {
    function array_uintersect()
    {
        $args = func_get_args();
        if (count($args) < 3) {
            user_error('wrong parameter count for array_uintersect()',
                E_USER_WARNING);
            return;
        }

        // Get compare function
        $user_func = array_pop($args);
        if (!is_callable($user_func)) {
            if (is_array($user_func)) {
                $user_func = $user_func[0] . '::' . $user_func[1];
            }
            user_error('array_uintersect() Not a valid callback ' .
                $user_func, E_USER_WARNING);
            return;
        }

        // Check arrays
        $array_count = count($args);
        for ($i = 0; $i < $array_count; $i++) {
            if (!is_array($args[$i])) {
                user_error('array_uintersect() Argument #' .
                    ($i + 1) . ' is not an array', E_USER_WARNING);
                return;
            }
        }

        // Compare entries
        $output = array();
        foreach ($args[0] as $key => $item) {
            for ($i = 1; $i !== $array_count; $i++) {
                $array = $args[$i];
                foreach($array as $key0 => $item0) {
                    if (!call_user_func($user_func, $item, $item0)) {
                        $output[$key] = $item;
                    }
                }
            }            
        }

        return $output;
    }
}

?>