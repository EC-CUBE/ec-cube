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
// $Id: floatval.php,v 1.2 2005/01/26 04:55:13 aidan Exp $


/**
 * Replace floatval()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.floatval
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.2 $
 * @since       PHP 4.2.0
 * @require     PHP 4.0.0 (user_error) (Type Casting)
 */
if (!function_exists('floatval')) {
    function floatval($var)
    {
        return (float) $var;
    }
}

?>
