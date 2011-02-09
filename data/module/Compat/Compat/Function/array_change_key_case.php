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
// | Authors: Stephan Schmidt <schst@php.net>                             |
// |          Aidan Lister <aidan@php.net>                                |
// +----------------------------------------------------------------------+
//
// $Id: array_change_key_case.php,v 1.11 2005/12/07 21:08:57 aidan Exp $


if (!defined('CASE_LOWER')) {
    define('CASE_LOWER', 0);
}

if (!defined('CASE_UPPER')) {
    define('CASE_UPPER', 1);
}


/**
 * Replace array_change_key_case()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.array_change_key_case
 * @author      Stephan Schmidt <schst@php.net>
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.11 $
 * @since       PHP 4.2.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('array_change_key_case')) {
    function array_change_key_case($input, $case = CASE_LOWER)
    {
        if (!is_array($input)) {
            user_error('array_change_key_case(): The argument should be an array',
                E_USER_WARNING);
            return false;
        }

        $output   = array ();
        $keys     = array_keys($input);
        $casefunc = ($case == CASE_LOWER) ? 'strtolower' : 'strtoupper';

        foreach ($keys as $key) {
            $output[$casefunc($key)] = $input[$key];
        }

        return $output;
    }
}

?>