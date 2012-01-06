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
// $Id: substr_compare.php,v 1.5 2005/01/26 04:55:13 aidan Exp $


/**
 * Replace substr_compare()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.substr_compare
 * @author      Tom Buskens <ortega@php.net>
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.5 $
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('substr_compare')) {
    function substr_compare($main_str, $str, $offset, $length = null, $case_insensitive = false)
    {
        if (!is_string($main_str)) {
            user_error('substr_compare() expects parameter 1 to be string, ' .
                gettype($main_str) . ' given', E_USER_WARNING);
            return;
        }

        if (!is_string($str)) {
            user_error('substr_compare() expects parameter 2 to be string, ' .
                gettype($str) . ' given', E_USER_WARNING);
            return;
        }
        
        if (!is_int($offset)) {
            user_error('substr_compare() expects parameter 3 to be long, ' .
                gettype($offset) . ' given', E_USER_WARNING);
            return;
        }
        
        if (is_null($length)) {
            $length = strlen($main_str) - $offset;
        } elseif ($offset >= strlen($main_str)) {
            user_error('substr_compare() The start position cannot exceed initial string length',
                E_USER_WARNING);
            return false;
        }

        $main_str = substr($main_str, $offset, $length);
        $str = substr($str, 0, strlen($main_str));

        if ($case_insensitive === false) {
            return strcmp($main_str, $str);
        } else {
            return strcasecmp($main_str, $str);
        }
    }
}

?>
