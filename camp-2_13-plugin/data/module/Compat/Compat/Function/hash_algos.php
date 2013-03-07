<?php

/**
 * Replace hash_algos()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
 * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
 * @link        http://php.net/function.hash_algos
 * @author      revulo <revulon@gmail.com>
 * @since       PHP 5.1.2
 * @require     PHP 4.0.0
 */
function php_compat_hash_algos()
{
    return array('md5', 'sha1', 'sha256');
}


// Define
if (!function_exists('hash_algos')) {
    function hash_algos()
    {
        return php_compat_hash_algos();
    }
}
