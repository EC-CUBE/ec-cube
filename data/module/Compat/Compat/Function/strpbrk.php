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
// | Authors: Stephan Schmidt <schst@php.net>                             |
// +----------------------------------------------------------------------+
//
// $Id: strpbrk.php,v 1.4 2005/01/26 04:55:13 aidan Exp $


/**
 * Replace strpbrk()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.strpbrk
 * @author      Stephan Schmidt <schst@php.net>
 * @version     $Revision: 1.4 $
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('strpbrk')) {
    function strpbrk($haystack, $char_list)
    {
        if (!is_scalar($haystack)) {
            user_error('strpbrk() expects parameter 1 to be string, ' .
                gettype($haystack) . ' given', E_USER_WARNING);
            return false;
        }

        if (!is_scalar($char_list)) {
            user_error('strpbrk() expects parameter 2 to be scalar, ' .
                gettype($needle) . ' given', E_USER_WARNING);
            return false;
        }

        $haystack  = (string) $haystack;
        $char_list = (string) $char_list;

        $len = strlen($haystack);
        for ($i = 0; $i < $len; $i++) {
            $char = substr($haystack, $i, 1);
            if (strpos($char_list, $char) === false) {
                continue;
            }
            return substr($haystack, $i);
        }

        return false;
    }
}

?>