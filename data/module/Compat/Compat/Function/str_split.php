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
// $Id: str_split.php,v 1.15 2005/06/18 12:15:32 aidan Exp $


/**
 * Replace str_split()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.str_split
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.15 $
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('str_split')) {
    function str_split($string, $split_length = 1)
    {
        if (!is_scalar($split_length)) {
            user_error('str_split() expects parameter 2 to be long, ' .
                gettype($split_length) . ' given', E_USER_WARNING);
            return false;
        }

        $split_length = (int) $split_length;
        if ($split_length < 1) {
            user_error('str_split() The length of each segment must be greater than zero', E_USER_WARNING);
            return false;
        }
        
        // Select split method
        if ($split_length < 65536) {
            // Faster, but only works for less than 2^16
            preg_match_all('/.{1,' . $split_length . '}/s', $string, $matches);
            return $matches[0];
        } else {
            // Required due to preg limitations
            $arr = array();
            $idx = 0;
            $pos = 0;
            $len = strlen($string);

            while ($len > 0) {
                $blk = ($len < $split_length) ? $len : $split_length;
                $arr[$idx++] = substr($string, $pos, $blk);
                $pos += $blk;
                $len -= $blk;
            }

            return $arr;
        }
    }
}

?>
