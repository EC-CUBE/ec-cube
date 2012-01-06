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
// $Id: scandir.php,v 1.18 2005/01/26 04:55:13 aidan Exp $


/**
 * Replace scandir()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.scandir
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.18 $
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('scandir')) {
    function scandir($directory, $sorting_order = 0)
    {
        if (!is_string($directory)) {
            user_error('scandir() expects parameter 1 to be string, ' .
                gettype($directory) . ' given', E_USER_WARNING);
            return;
        }

        if (!is_int($sorting_order) && !is_bool($sorting_order)) {
            user_error('scandir() expects parameter 2 to be long, ' .
                gettype($sorting_order) . ' given', E_USER_WARNING);
            return;
        }

        if (!is_dir($directory) || (false === $fh = @opendir($directory))) {
            user_error('scandir() failed to open dir: Invalid argument', E_USER_WARNING);
            return false;
        }

        $files = array ();
        while (false !== ($filename = readdir($fh))) {
            $files[] = $filename;
        }

        closedir($fh);

        if ($sorting_order == 1) {
            rsort($files);
        } else {
            sort($files);
        }

        return $files;
    }
}

?>
