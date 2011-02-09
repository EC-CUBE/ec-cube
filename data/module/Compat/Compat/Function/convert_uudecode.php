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
// | Authors: Michael Wallner <mike@php.net>                              |
// |          Aidan Lister <aidan@php.net>                                |
// +----------------------------------------------------------------------+
//
// $Id: convert_uudecode.php,v 1.8 2005/01/26 04:55:13 aidan Exp $


/**
 * Replace convert_uudecode()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.convert_uudecode
 * @author      Michael Wallner <mike@php.net>
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.8 $
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('convert_uudecode')) {
    function convert_uudecode($string)
    {
        // Sanity check
        if (!is_scalar($string)) {
            user_error('convert_uuencode() expects parameter 1 to be string, ' .
                gettype($string) . ' given', E_USER_WARNING);
            return false;
        }

        if (strlen($string) < 8) {
            user_error('convert_uuencode() The given parameter is not a valid uuencoded string', E_USER_WARNING);
            return false;
        }

        $decoded = '';
        foreach (explode("\n", $string) as $line) {

            $c = count($bytes = unpack('c*', substr(trim($line), 1)));

            while ($c % 4) {
                $bytes[++$c] = 0;
            }

            foreach (array_chunk($bytes, 4) as $b) {
                $b0 = $b[0] == 0x60 ? 0 : $b[0] - 0x20;
                $b1 = $b[1] == 0x60 ? 0 : $b[1] - 0x20;
                $b2 = $b[2] == 0x60 ? 0 : $b[2] - 0x20;
                $b3 = $b[3] == 0x60 ? 0 : $b[3] - 0x20;
                
                $b0 <<= 2;
                $b0 |= ($b1 >> 4) & 0x03;
                $b1 <<= 4;
                $b1 |= ($b2 >> 2) & 0x0F;
                $b2 <<= 6;
                $b2 |= $b3 & 0x3F;
                
                $decoded .= pack('c*', $b0, $b1, $b2);
            }
        }

        return rtrim($decoded, "\0");
    }
}

?>