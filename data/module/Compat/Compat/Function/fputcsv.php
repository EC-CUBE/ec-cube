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
// $Id: fputcsv.php,v 1.2 2005/11/22 08:28:16 aidan Exp $


/**
 * Replace fprintf()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.fprintf
 * @author      Twebb <twebb@boisecenter.com>
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.2 $
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('fputcsv')) {
    function fputcsv($handle, $fields, $delimiter = ',', $enclosure = '"')
    {
        // Sanity Check
        if (!is_resource($handle)) {
            user_error('fputcsv() expects parameter 1 to be resource, ' .
                gettype($handle) . ' given', E_USER_WARNING);
            return false;
        }

        
        $str = '';
        foreach ($fields as $cell) {
            $cell = str_replace($enclosure, $enclosure . $enclosure, $cell);

            if (strchr($cell, $delimiter) !== false ||
                strchr($cell, $enclosure) !== false ||
                strchr($cell, "\n") !== false) {
                
                $str .= $enclosure . $cell . $enclosure . $delimiter;
            } else {
                $str .= $cell . $delimiter;
            }
        }

        fputs($handle, substr($str, 0, -1) . "\n");

        return strlen($str);
    }
}

?>