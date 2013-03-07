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
// $Id: time_sleep_until.php,v 1.2 2005/12/07 21:08:57 aidan Exp $


/**
 * Replace time_sleep_until()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/time_sleep_until
 * @author      Arpad Ray <arpad@php.net>
 * @version     $Revision: 1.2 $
 * @since       PHP 5.1.0
 * @require     PHP 4.0.1 (trigger_error)
 */
if (!function_exists('time_sleep_until')) {
    function time_sleep_until($timestamp)
    {
	    list($usec, $sec) = explode(' ', microtime());
        $now = $sec + $usec;
        if ($timestamp <= $now) {
            user_error('Specified timestamp is in the past', E_USER_WARNING);
            return false;
        }

        $diff = $timestamp - $now;
        usleep($diff * 1000000);
        return true;
    }
}
	
?>
