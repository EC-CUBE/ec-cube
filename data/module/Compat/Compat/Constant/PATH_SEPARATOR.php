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
// $Id: PATH_SEPARATOR.php,v 1.13 2004/11/14 16:10:18 aidan Exp $


/**
 * Replace constant PATH_SEPARATOR
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/ref.dir
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.13 $
 * @since       PHP 4.3.0
 */
if (!defined('PATH_SEPARATOR')) {
    define('PATH_SEPARATOR',
        strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ? ';' : ':'
     );
}

?>