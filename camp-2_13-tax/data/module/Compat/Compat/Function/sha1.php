<?php

/**
 * Replace sha1()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
 * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
 * @link        http://php.net/function.sha1
 * @author      revulo <revulon@gmail.com>
 * @since       PHP 4.3.0
 * @require     PHP 4.0.0
 */
function php_compat_sha1($str, $raw_output = false)
{
    $h0 = (int)0x67452301;
    $h1 = (int)0xefcdab89;
    $h2 = (int)0x98badcfe;
    $h3 = (int)0x10325476;
    $h4 = (int)0xc3d2e1f0;

    $len = strlen($str);

    $str .= "\x80";
    $str .= str_repeat("\0", 63 - ($len + 8) % 64);
    $str .= pack('N2', $len >> 29, $len << 3);

    for ($i = 0; $i < strlen($str); $i += 64) {

        $w = array();
        for ($j = 0; $j < 16; ++$j) {
            $index = $i + $j * 4;
            $w[$j] = ord($str[$index])     << 24
                   | ord($str[$index + 1]) << 16
                   | ord($str[$index + 2]) << 8
                   | ord($str[$index + 3]);
        }
        for ($j = 16; $j < 80; ++$j) {
            $w[$j] = php_compat_sha1_rotl_helper($w[$j - 3] ^ $w[$j - 8] ^ $w[$j - 14] ^ $w[$j - 16], 1);
        }

        $a = $h0;
        $b = $h1;
        $c = $h2;
        $d = $h3;
        $e = $h4;

        for ($j = 0; $j < 80; ++$j) {
            if ($j < 20) {
                $f = ($b & $c) | (~$b & $d);
                $k = (int)0x5a827999;
            } else if ($j < 40) {
                $f = $b ^ $c ^ $d;
                $k = (int)0x6ed9eba1;
            } else if ($j < 60) {
                $f = ($b & $c) | ($b & $d) | ($c & $d);
                $k = (int)0x8f1bbcdc;
            } else {
                $f = $b ^ $c ^ $d;
                $k = (int)0xca62c1d6;
            }

            $t = php_compat_sha1_add32_helper(
                 php_compat_sha1_add32_helper(
                 php_compat_sha1_add32_helper(
                 php_compat_sha1_add32_helper(
                 php_compat_sha1_rotl_helper($a, 5), $f), $e), $k), $w[$j]);

            $e = $d;
            $d = $c;
            $c = php_compat_sha1_rotl_helper($b, 30);
            $b = $a;
            $a = $t;
        }

        $h0 = php_compat_sha1_add32_helper($h0, $a);
        $h1 = php_compat_sha1_add32_helper($h1, $b);
        $h2 = php_compat_sha1_add32_helper($h2, $c);
        $h3 = php_compat_sha1_add32_helper($h3, $d);
        $h4 = php_compat_sha1_add32_helper($h4, $e);
    }

    $h0 &= (int)0xffffffff;
    $h1 &= (int)0xffffffff;
    $h2 &= (int)0xffffffff;
    $h3 &= (int)0xffffffff;
    $h4 &= (int)0xffffffff;

    $hash = sprintf('%08x%08x%08x%08x%08x', $h0, $h1, $h2, $h3, $h4);

    if ($raw_output) {
        return pack('H*', $hash);
    } else {
        return $hash;
    }
}

function php_compat_sha1_add32_helper($x, $y)
{
    $lsw = ($x & 0xffff) + ($y & 0xffff);
    $msw = ($x >> 16) + ($y >> 16) + ($lsw >> 16);
    return ($msw << 16) | ($lsw & 0xffff);
}

function php_compat_sha1_rotl_helper($x, $n)
{
    return ($x << $n) | ($x >> (32 - $n)) & (0x7fffffff >> (31 - $n));
}

// Define
if (!function_exists('sha1')) {
    function sha1($str, $raw_output = false)
    {
        return php_compat_sha1($str, $raw_output);
    }
}
