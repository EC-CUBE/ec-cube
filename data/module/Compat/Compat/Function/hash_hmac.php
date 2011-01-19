<?php

require_once dirname(__FILE__) . '/hash.php';

/**
 * Replace hash_hmac()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
 * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
 * @link        http://php.net/function.hash_hmac
 * @author      revulo <revulon@gmail.com>
 * @since       PHP 5.1.2
 * @require     PHP 4.0.1 (str_pad)
 */
function php_compat_hash_hmac($algo, $data, $key, $raw_output = false)
{
    // Block size (byte) for MD5, SHA-1 and SHA-256.
    $blocksize = 64;

    $ipad = str_repeat("\x36", $blocksize);
    $opad = str_repeat("\x5c", $blocksize);

    if (strlen($key) > $blocksize) {
        $key = hash($algo, $key, true);
    } else {
        $key = str_pad($key, $blocksize, "\x00");
    }

    $ipad ^= $key;
    $opad ^= $key;

    return hash($algo, $opad . hash($algo, $ipad . $data, true), $raw_output);
}


// Define
if (!function_exists('hash_hmac')) {
    function hash_hmac($algo, $data, $key, $raw_output = false)
    {
        return php_compat_hash_hmac($algo, $data, $key, $raw_output);
    }
}
