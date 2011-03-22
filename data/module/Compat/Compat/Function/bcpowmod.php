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
// $Id: bcpowmod.php,v 1.2 2005/11/22 20:24:45 aidan Exp $


/**
 * Replace bcpowmod()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.bcpowmod
 * @author      Sara Golemon <pollita@php.net>
 * @version     $Revision: 1.2 $
 * @since       PHP 5.0.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('bcpowmod')) {
    function bcpowmod($x, $y, $modulus, $scale)
    {
        // Sanity check
        if (!is_scalar($x)) {
            user_error('bcpowmod() expects parameter 1 to be string, ' .
                gettype($x) . ' given', E_USER_WARNING);
            return false;
        }

        if (!is_scalar($y)) {
            user_error('bcpowmod() expects parameter 2 to be string, ' .
                gettype($y) . ' given', E_USER_WARNING);
            return false;
        }

        if (!is_scalar($modulus)) {
            user_error('bcpowmod() expects parameter 3 to be string, ' .
                gettype($modulus) . ' given', E_USER_WARNING);
            return false;
        }

        if (!is_scalar($scale)) {
            user_error('bcpowmod() expects parameter 4 to be integer, ' .
                gettype($scale) . ' given', E_USER_WARNING);
            return false;
        }

        $t = '1';
        while (bccomp($y, '0')) {
            if (bccomp(bcmod($y, '2'), '0')) {
                $t = bcmod(bcmul($t, $x), $modulus);
                $y = bcsub($y, '1');
            }

            $x = bcmod(bcmul($x, $x), $modulus);
            $y = bcdiv($y, '2');
        }

        return $t;
    }
}

?>
