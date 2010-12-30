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
// $Id: array_walk_recursive.php,v 1.7 2005/01/26 04:55:13 aidan Exp $


/**
 * Replace array_walk_recursive()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.array_walk_recursive
 * @author      Tom Buskens <ortega@php.net>
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.7 $
 * @since       PHP 5
 * @require     PHP 4.0.6 (is_callable)
 */
if (!function_exists('array_walk_recursive')) {
    function array_walk_recursive(&$input, $funcname)
    {
        if (!is_callable($funcname)) {
            if (is_array($funcname)) {
                $funcname = $funcname[0] . '::' . $funcname[1];
            }
            user_error('array_walk_recursive() Not a valid callback ' . $user_func,
                E_USER_WARNING);
            return;
        }

        if (!is_array($input)) {
            user_error('array_walk_recursive() The argument should be an array',
                E_USER_WARNING);
            return;
        }

        $args = func_get_args();

        foreach ($input as $key => $item) {
            if (is_array($item)) {
                array_walk_recursive($item, $funcname, $args);
                $input[$key] = $item;
            } else {
                $args[0] = &$item;
                $args[1] = &$key;
                call_user_func_array($funcname, $args);
                $input[$key] = $item;
            }
        }
    }
}

?>