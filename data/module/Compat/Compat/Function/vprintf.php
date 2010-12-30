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
// $Id: vprintf.php,v 1.14 2005/01/26 04:55:13 aidan Exp $


/**
 * Replace vprintf()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.vprintf
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.14 $
 * @since       PHP 4.1.0
 * @require     PHP 4.0.4 (call_user_func_array)
 */
if (!function_exists('vprintf')) {
    function vprintf ($format, $args)
    {
        if (count($args) < 2) {
            user_error('vprintf() Too few arguments', E_USER_WARNING);
            return;
        }

        array_unshift($args, $format);
        return call_user_func_array('printf', $args);
    }
}

?>