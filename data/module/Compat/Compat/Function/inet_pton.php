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
// $Id: inet_pton.php,v 1.2 2005/12/05 14:49:40 aidan Exp $


/**
 * Replace inet_pton()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/inet_pton
 * @author      Arpad Ray <arpad@php.net>
 * @version     $Revision: 1.2 $
 * @since       PHP 5.1.0
 * @require     PHP 4.2.0 (array_fill)
 */
if (!function_exists('inet_pton')) {  
    function inet_pton($address)
    {
        $r = ip2long($address);
        if ($r !== false && $r != -1) {
            return pack('N', $r);
        }

        $delim_count = substr_count($address, ':');
        if ($delim_count < 1 || $delim_count > 7) {
            return false;
        }

        $r = explode(':', $address);
        $rcount = count($r);
        if (($doub = array_search('', $r, 1)) !== false) {
            $length = (!$doub || $doub == $rcount - 1 ? 2 : 1);
            array_splice($r, $doub, $length, array_fill(0, 8 + $length - $rcount, 0));
        }

        $r = array_map('hexdec', $r);
        array_unshift($r, 'n*');
        $r = call_user_func_array('pack', $r);

        return $r;
    }
}

?>