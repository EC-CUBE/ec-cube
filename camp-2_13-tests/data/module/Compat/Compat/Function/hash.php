<?php

/**
 * Replace hash()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
 * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
 * @link        http://php.net/function.hash
 * @author      revulo <revulon@gmail.com>
 * @since       PHP 5.1.2
 * @require     PHP 4.0.0 (user_error)
 */
function php_compat_hash($algo, $data, $raw_output = false)
{
    $algo = strtolower($algo);
    switch ($algo) {
        case 'md5':
            $hash = md5($data);
            break;

        case 'sha1':
            if (!function_exists('sha1')) {
                require dirname(__FILE__) . '/sha1.php';
            }
            $hash = sha1($data);
            break;

        case 'sha256':
            if (!function_exists('php_compat_sha256')) {
                require dirname(__FILE__) . '/_sha256.php';
            }
            $hash = php_compat_sha256($data);
            break;

        default:
            user_error('hash(): Unknown hashing algorithm: ' . $algo, E_USER_WARNING);
            return false;
    }

    if ($raw_output) {
        return pack('H*', $hash);
    } else {
        return $hash;
    }
}


// Define
if (!function_exists('hash')) {
    function hash($algo, $data, $raw_output = false)
    {
        return php_compat_hash($algo, $data, $raw_output);
    }
}
