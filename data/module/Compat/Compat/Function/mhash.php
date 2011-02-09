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
// $Id: mhash.php,v 1.1 2005/05/10 07:56:44 aidan Exp $


if (!defined('MHASH_CRC32')) {
    define('MHASH_CRC32', 0);
}

if (!defined('MHASH_MD5')) {
    define('MHASH_MD5', 1);
}

if (!defined('MHASH_SHA1')) {
    define('MHASH_SHA1', 2);
}

if (!defined('MHASH_HAVAL256')) {
    define('MHASH_HAVAL256', 3);
}

if (!defined('MHASH_RIPEMD160')) {
    define('MHASH_RIPEMD160', 5);
}

if (!defined('MHASH_TIGER')) {
    define('MHASH_TIGER', 7);
}

if (!defined('MHASH_GOST')) {
    define('MHASH_GOST', 8);
}

if (!defined('MHASH_CRC32B')) {
    define('MHASH_CRC32B', 9);
}

if (!defined('MHASH_HAVAL192')) {
    define('MHASH_HAVAL192', 11);
}

if (!defined('MHASH_HAVAL160')) {
    define('MHASH_HAVAL160', 12);
}

if (!defined('MHASH_HAVAL128')) {
    define('MHASH_HAVAL128', 13);
}

if (!defined('MHASH_TIGER128')) {
    define('MHASH_TIGER128', 14);
}

if (!defined('MHASH_TIGER160')) {
    define('MHASH_TIGER160', 15);
}

if (!defined('MHASH_MD4')) {
    define('MHASH_MD4', 16);
}

if (!defined('MHASH_SHA256')) {
    define('MHASH_SHA256', 17);
}

if (!defined('MHASH_ADLER32')) {
    define('MHASH_ADLER32', 18);
}


/**
 * Replace mhash()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.mhash
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.1 $
 * @since       PHP 4.1.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('mhash')) {
    function mhash($hashtype, $data, $key = '')    
    {
        switch ($hashtype) {
            case MHASH_MD5:
                $key = str_pad((strlen($key) > 64 ? pack("H*", md5($key)) : $key), 64, chr(0x00));
                $k_opad = $key ^ (str_pad('', 64, chr(0x5c)));
                $k_ipad = $key ^ (str_pad('', 64, chr(0x36)));
                return pack("H*", md5($k_opad . pack("H*", md5($k_ipad .  $data))));

            default:
                return false;

            break;
        }
    }
}

?>