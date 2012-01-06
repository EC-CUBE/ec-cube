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
// |          Stephan Schmidt <schst@php.net>                             |
// +----------------------------------------------------------------------+
//
// $Id: strripos.php,v 1.24 2005/08/10 10:19:59 aidan Exp $


/**
 * Replace strripos()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.strripos
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.24 $
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('strripos')) {
    function strripos($haystack, $needle, $offset = null)
    {
        // Sanity check
        if (!is_scalar($haystack)) {
            user_error('strripos() expects parameter 1 to be scalar, ' .
                gettype($haystack) . ' given', E_USER_WARNING);
            return false;
        }

        if (!is_scalar($needle)) {
            user_error('strripos() expects parameter 2 to be scalar, ' .
                gettype($needle) . ' given', E_USER_WARNING);
            return false;
        }

        if (!is_int($offset) && !is_bool($offset) && !is_null($offset)) {
            user_error('strripos() expects parameter 3 to be long, ' .
                gettype($offset) . ' given', E_USER_WARNING);
            return false;
        }

        // Initialise variables
        $needle         = strtolower($needle);
        $haystack       = strtolower($haystack);
        $needle_fc      = $needle{0};
        $needle_len     = strlen($needle);
        $haystack_len   = strlen($haystack);
        $offset         = (int) $offset;
        $leftlimit      = ($offset >= 0) ? $offset : 0;
        $p              = ($offset >= 0) ?
                                $haystack_len :
                                $haystack_len + $offset + 1;

        // Reverse iterate haystack
        while (--$p >= $leftlimit) {
            if ($needle_fc === $haystack{$p} &&
                substr($haystack, $p, $needle_len) === $needle) {
                return $p;
            }
        }

        return false;
    }
}

?>
