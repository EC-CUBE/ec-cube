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
// $Id: inet_ntop.php,v 1.3 2005/12/05 14:49:40 aidan Exp $


/**
 * Replace inet_ntop()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/inet_ntop
 * @author      Arpad Ray <arpad@php.net>
 * @version     $Revision: 1.3 $
 * @since       PHP 5.1.0
 * @require     PHP 4.0.0 (long2ip)
 */
if (!function_exists('inet_ntop')) {
    function inet_ntop($in_addr)
    {
        switch (strlen($in_addr)) {
            case 4:
                list(,$r) = unpack('N', $in_addr);
                return long2ip($r);

            case 16:
                $r = substr(chunk_split(bin2hex($in_addr), 4, ':'), 0, -1);
                $r = preg_replace(
                    array('/(?::?\b0+\b:?){2,}/', '/\b0+([^0])/e'),
                    array('::', '(int)"$1"?"$1":"0$1"'),
                    $r);
                return $r;
        }

        return false;
    }
}

?>
