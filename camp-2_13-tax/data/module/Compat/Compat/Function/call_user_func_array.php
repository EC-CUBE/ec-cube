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
// $Id: call_user_func_array.php,v 1.13 2005/01/26 04:55:13 aidan Exp $


/**
 * Replace call_user_func_array()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.call_user_func_array
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.13 $
 * @since       PHP 4.0.4
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('call_user_func_array')) {
    function call_user_func_array($function, $param_arr)
    {
        $param_arr = array_values((array) $param_arr);

        // Sanity check
        if (!is_callable($function)) {
            if (is_array($function) && count($function) > 2) {
                $function = $function[0] . '::' . $function[1];
            }
            $error = sprintf('call_user_func_array() First argument is expected ' .
                'to be a valid callback, \'%s\' was given', $function);
            user_error($error, E_USER_WARNING);
            return;
        }

        // Build argument string
        $arg_string = '';
        $comma = '';
        for ($i = 0, $x = count($param_arr); $i < $x; $i++) {
            $arg_string .= $comma . "\$param_arr[$i]";
            $comma = ', ';
        }

        // Determine method of calling function
        if (is_array($function)) {
            $object =& $function[0];
            $method = $function[1];

            // Static vs method call
            if (is_string($function[0])) {
                eval("\$retval = $object::\$method($arg_string);");
            } else {
                eval("\$retval = \$object->\$method($arg_string);");
            }
        } else {
            eval("\$retval = \$function($arg_string);");
        }

        return $retval;
    }
}

?>
