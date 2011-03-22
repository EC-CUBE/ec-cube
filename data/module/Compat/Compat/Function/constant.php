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
// $Id: constant.php,v 1.7 2005/01/26 04:55:13 aidan Exp $


/**
 * Replace constant()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.constant
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.7 $
 * @since       PHP 4.0.4
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('constant')) {
    function constant($constant)
    {
        if (!defined($constant)) {
            $error = sprintf('constant() Couldn\'t find constant %s', $constant);
            user_error($error, E_USER_WARNING);
            return false;
        }

        eval("\$value=$constant;");

        return $value;
    }
}

?>
