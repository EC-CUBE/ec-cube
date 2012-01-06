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
// $Id: FILE.php,v 1.8 2004/08/19 10:09:52 aidan Exp $


/**
 * Replace filesystem constants
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/ref.filesystem
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.8 $
 * @since       PHP 5
 */
if (!defined('FILE_USE_INCLUDE_PATH')) {
    define('FILE_USE_INCLUDE_PATH', 1);
}

if (!defined('FILE_IGNORE_NEW_LINES')) {
    define('FILE_IGNORE_NEW_LINES', 2);
}

if (!defined('FILE_SKIP_EMPTY_LINES')) {
    define('FILE_SKIP_EMPTY_LINES', 4);
}

if (!defined('FILE_APPEND')) {
    define('FILE_APPEND', 8);
}

if (!defined('FILE_NO_DEFAULT_CONTEXT')) {
    define('FILE_NO_DEFAULT_CONTEXT', 16);
}

?>
