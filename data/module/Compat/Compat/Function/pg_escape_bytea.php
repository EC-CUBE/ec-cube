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
// | Authors: Ian Eure <ieure@php.net>                                    |
// |          Mocha (http://us4.php.net/pg_escape_bytea)                  |
// +----------------------------------------------------------------------+
//
// $Id: pg_escape_bytea.php,v 1.1 2005/05/10 07:56:51 aidan Exp $


/**
 * Replace pg_escape_bytea()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.pg_escape_bytea
 * @author      Ian Eure <ieure@php.net>
 * @version     $Revision@
 * @since       PHP 4.2.0
 * @require     PHP 4.0.0
 */
if (!function_exists('pg_escape_bytea')) {
    function pg_escape_bytea($data)
    {
        return str_replace(
            array(chr(92),  chr(0),   chr(39)),
            array('\\\134', '\\\000', '\\\047'),
            $data);
    }
}

?>