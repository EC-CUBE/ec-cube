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
// +----------------------------------------------------------------------+
//
// $Id: mime_content_type.php,v 1.3 2005/12/07 21:08:57 aidan Exp $


/**
* Replace mime_content_type()
*
* You will need the `file` command installed and present in your $PATH. If
* `file` is not available, the type 'application/octet-stream' is returned
* for all files.
*
* @category   PHP
* @package    PHP_Compat
* @link       http://php.net/function.mime_content_type
* @version    $Revision: 1.3 $
* @author     Ian Eure <ieure@php.net>
* @since      PHP 4.3.0
* @require    PHP 4.0.3 (escapeshellarg)
*/
if (!function_exists('mime_content_type')) {
    function mime_content_type($filename)
    {
        // Sanity check
        if (!file_exists($filename)) {
            return false;
        }

        $filename = escapeshellarg($filename);
        $out = `file -iL $filename 2>/dev/null`;
        if (empty($out)) {
            return 'application/octet-stream';
        }

        // Strip off filename
        $t = substr($out, strpos($out, ':') + 2);

        if (strpos($t, ';') !== false) {
            // Strip MIME parameters
            $t = substr($t, 0, strpos($t, ';'));
        }

        // Strip any remaining whitespace
        return trim($t);
    }
}

?> 
