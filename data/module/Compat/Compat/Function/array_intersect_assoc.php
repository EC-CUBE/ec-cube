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
// $Id: array_intersect_assoc.php,v 1.4 2005/01/26 04:55:13 aidan Exp $


/**
 * Replace array_intersect_assoc()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.array_intersect_assoc
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.4 $
 * @since       PHP 4.3.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('array_intersect_assoc')) {
    function array_intersect_assoc()
    {
        // Sanity check
        $args = func_get_args();
        if (count($args) < 2) {
            user_error('wrong parameter count for array_intersect_assoc()', E_USER_WARNING);
            return;
        }

        // Check arrays
        $array_count = count($args);
        for ($i = 0; $i !== $array_count; $i++) {
            if (!is_array($args[$i])) {
                user_error('array_intersect_assoc() Argument #' .
                    ($i + 1) . ' is not an array', E_USER_WARNING);
                return;
            }
        }

        // Compare entries
        $intersect = array();
        foreach ($args[0] as $key => $value) {
            $intersect[$key] = $value;

            for ($i = 1; $i < $array_count; $i++) {
                if (!isset($args[$i][$key]) || $args[$i][$key] != $value) {
                    unset($intersect[$key]);
                    break;
                }
            }
        }

        return $intersect;
    }
}

?>