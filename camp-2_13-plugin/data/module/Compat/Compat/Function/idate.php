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
// | Authors: Arpad Ray <arpad@php.net>                                   |
// +----------------------------------------------------------------------+
//
// $Id: idate.php,v 1.2 2005/12/07 21:08:57 aidan Exp $


/**
 * Replace idate()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/idate
 * @author      Arpad Ray <arpad@php.net>
 * @version     $Revision: 1.2 $
 * @since       PHP 5.0.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('idate')) {
    function idate($format, $timestamp = false)
    {
        if (strlen($format) !== 1) {
            user_error('idate format is one char', E_USER_WARNING);
            return false;
        }

        if (strpos('BdhHiILmstUwWyYzZ', $format) === false) {
            return 0;
        }

        if ($timestamp === false) {
            $timestamp = time();
        }

        return intval(date($format, $timestamp));
    }
}

?>
