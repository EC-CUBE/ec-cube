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
// $Id: md5_file.php,v 1.3 2005/11/22 08:29:19 aidan Exp $


/**
 * Replace md5_file()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/md5_file
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.3 $
 * @since       PHP 4.2.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('md5_file')) {
    function md5_file($filename, $raw_output = false)
    {
        // Sanity check
        if (!is_scalar($filename)) {
            user_error('md5_file() expects parameter 1 to be string, ' .
                gettype($filename) . ' given', E_USER_WARNING);
            return;
        }

        if (!is_scalar($raw_output)) {
            user_error('md5_file() expects parameter 2 to be bool, ' .
                gettype($raw_output) . ' given', E_USER_WARNING);
            return;
        }

        if (!file_exists($filename)) {
            user_error('md5_file() Unable to open file', E_USER_WARNING);
            return false;
        }
        
        // Read the file
        if (false === $fh = fopen($filename, 'rb')) {
            user_error('md5_file() failed to open stream: No such file or directory',
                E_USER_WARNING);
            return false;
        }

        clearstatcache();
        if ($fsize = @filesize($filename)) {
            $data = fread($fh, $fsize);
        } else {
            $data = '';
            while (!feof($fh)) {
                $data .= fread($fh, 8192);
            }
        }

        fclose($fh);

        // Return
        $data = md5($data);
        if ($raw_output === true) {
            $data = pack('H*', $data);
        }

        return $data;
    }
}

?>