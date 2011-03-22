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
// $Id: image_type_to_mime_type.php,v 1.8 2005/01/26 04:55:13 aidan Exp $


if (!defined('IMAGETYPE_GIF')) {
    define('IMAGETYPE_GIF', 1);
}

if (!defined('IMAGETYPE_JPEG')) {
    define('IMAGETYPE_JPEG', 2);
}

if (!defined('IMAGETYPE_PNG')) {
    define('IMAGETYPE_PNG', 3);
}

if (!defined('IMAGETYPE_SWF')) {
    define('IMAGETYPE_SWF', 4);
}

if (!defined('IMAGETYPE_PSD')) {
    define('IMAGETYPE_PSD', 5);
}

if (!defined('IMAGETYPE_BMP')) {
    define('IMAGETYPE_BMP', 6);
}

if (!defined('IMAGETYPE_TIFF_II')) {
    define('IMAGETYPE_TIFF_II', 7);
}

if (!defined('IMAGETYPE_TIFF_MM')) {
    define('IMAGETYPE_TIFF_MM', 8);
}

if (!defined('IMAGETYPE_JPC')) {
    define('IMAGETYPE_JPC', 9);
}

if (!defined('IMAGETYPE_JP2')) {
    define('IMAGETYPE_JP2', 10);
}

if (!defined('IMAGETYPE_JPX')) {
    define('IMAGETYPE_JPX', 11);
}

if (!defined('IMAGETYPE_JB2')) {
    define('IMAGETYPE_JB2', 12);
}

if (!defined('IMAGETYPE_SWC')) {
    define('IMAGETYPE_SWC', 13);
}

if (!defined('IMAGETYPE_IFF')) {
    define('IMAGETYPE_IFF', 14);
}

if (!defined('IMAGETYPE_WBMP')) {
    define('IMAGETYPE_WBMP', 15);
}

if (!defined('IMAGETYPE_XBM')) {
    define('IMAGETYPE_XBM', 16);
}


/**
 * Replace image_type_to_mime_type()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.image_type_to_mime_type
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.8 $
 * @since       PHP 4.3.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('image_type_to_mime_type')) {
    function image_type_to_mime_type($imagetype)
    {
        switch ($imagetype):
            case IMAGETYPE_GIF:
                return 'image/gif';
                break;
            case IMAGETYPE_JPEG:
                return 'image/jpeg';
                break;
            case IMAGETYPE_PNG:
                return 'image/png';
                break;
            case IMAGETYPE_SWF:
            case IMAGETYPE_SWC:
                return 'application/x-shockwave-flash';
                break;
            case IMAGETYPE_PSD:
                return 'image/psd';
                break;
            case IMAGETYPE_BMP:
                return 'image/bmp';
                break;
            case IMAGETYPE_TIFF_MM:
            case IMAGETYPE_TIFF_II:
                return 'image/tiff';
                break;
            case IMAGETYPE_JP2:
                return 'image/jp2';
                break;
            case IMAGETYPE_IFF:
                return 'image/iff';
                break;
            case IMAGETYPE_WBMP:
                return 'image/vnd.wap.wbmp';
                break;
            case IMAGETYPE_XBM:
                return 'image/xbm';
                break;
            case IMAGETYPE_JPX:
            case IMAGETYPE_JB2:
            case IMAGETYPE_JPC:
            default:
                return 'application/octet-stream';
                break;

        endswitch;
    }
}

?>
