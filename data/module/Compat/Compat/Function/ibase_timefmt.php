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
// $Id: ibase_timefmt.php,v 1.1 2005/05/10 07:51:07 aidan Exp $


/**
 * Replace function ibase_timefmt()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.ibase_timefmt
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.1 $
 * @since       PHP 5.0.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('ibase_timefmt')) {
    function ibase_timefmt($format, $columntype = IBASE_TIMESTAMP)
    {
        switch ($columntype) {
            case IBASE_TIMESTAMP:
                ini_set('ibase.dateformat', $format);
                break;

            case IBASE_DATE:
                ini_set('ibase.dateformat', $format);
                break;

            case IBASE_TIME:
                ini_set('ibase.timeformat', $format);
                break;

            default:
                return false;
        }

        return true;
    }
}

?>