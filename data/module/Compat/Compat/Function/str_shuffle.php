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
// $Id: str_shuffle.php,v 1.6 2005/08/14 03:24:16 aidan Exp $


/**
 * Replace str_shuffle()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.str_shuffle
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.6 $
 * @since       PHP 4.3.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('str_shuffle')) {
    function str_shuffle($str)
    {
        // Init
        $str = (string) $str;

        // Seed
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        mt_srand($seed);

        // Shuffle
        for ($new = '', $len = strlen($str); $len > 0; $str{$p} = $str{$len}) { 
            $new .= $str{$p = mt_rand(0, --$len)};
        }

        return $new;
    }
}

?>
