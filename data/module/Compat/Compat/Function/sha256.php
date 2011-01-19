<?php

/**
 * PHP implementation of SHA-256 hash function
 *
 * @category    PHP
 * @package     PHP_Compat
 * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
 * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
 * @link        http://php.net/function.hash
 * @author      revulo <revulon@gmail.com>
 * @require     PHP 4.0.0
 */
function php_compat_sha256($str, $raw_output = false)
{
    $h0 = (int)0x6a09e667;
    $h1 = (int)0xbb67ae85;
    $h2 = (int)0x3c6ef372;
    $h3 = (int)0xa54ff53a;
    $h4 = (int)0x510e527f;
    $h5 = (int)0x9b05688c;
    $h6 = (int)0x1f83d9ab;
    $h7 = (int)0x5be0cd19;

    $k = array(
        (int)0x428a2f98, (int)0x71374491, (int)0xb5c0fbcf, (int)0xe9b5dba5,
        (int)0x3956c25b, (int)0x59f111f1, (int)0x923f82a4, (int)0xab1c5ed5,
        (int)0xd807aa98, (int)0x12835b01, (int)0x243185be, (int)0x550c7dc3,
        (int)0x72be5d74, (int)0x80deb1fe, (int)0x9bdc06a7, (int)0xc19bf174,
        (int)0xe49b69c1, (int)0xefbe4786, (int)0x0fc19dc6, (int)0x240ca1cc,
        (int)0x2de92c6f, (int)0x4a7484aa, (int)0x5cb0a9dc, (int)0x76f988da,
        (int)0x983e5152, (int)0xa831c66d, (int)0xb00327c8, (int)0xbf597fc7,
        (int)0xc6e00bf3, (int)0xd5a79147, (int)0x06ca6351, (int)0x14292967,
        (int)0x27b70a85, (int)0x2e1b2138, (int)0x4d2c6dfc, (int)0x53380d13,
        (int)0x650a7354, (int)0x766a0abb, (int)0x81c2c92e, (int)0x92722c85,
        (int)0xa2bfe8a1, (int)0xa81a664b, (int)0xc24b8b70, (int)0xc76c51a3,
        (int)0xd192e819, (int)0xd6990624, (int)0xf40e3585, (int)0x106aa070,
        (int)0x19a4c116, (int)0x1e376c08, (int)0x2748774c, (int)0x34b0bcb5,
        (int)0x391c0cb3, (int)0x4ed8aa4a, (int)0x5b9cca4f, (int)0x682e6ff3,
        (int)0x748f82ee, (int)0x78a5636f, (int)0x84c87814, (int)0x8cc70208,
        (int)0x90befffa, (int)0xa4506ceb, (int)0xbef9a3f7, (int)0xc67178f2
    );

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
        for ($j = 16; $j < 64; ++$j) {
            $s0 = php_compat_sha256_rotr_helper($w[$j - 15],  7)
                ^ php_compat_sha256_rotr_helper($w[$j - 15], 18)
                ^ php_compat_sha256_shr_helper ($w[$j - 15],  3);

            $s1 = php_compat_sha256_rotr_helper($w[$j - 2], 17)
                ^ php_compat_sha256_rotr_helper($w[$j - 2], 19)
                ^ php_compat_sha256_shr_helper ($w[$j - 2], 10);

            $w[$j] = php_compat_sha256_add32_helper(
                     php_compat_sha256_add32_helper(
                     php_compat_sha256_add32_helper($w[$j - 16], $s0), $w[$j - 7]), $s1);
        }

        $a = $h0;
        $b = $h1;
        $c = $h2;
        $d = $h3;
        $e = $h4;
        $f = $h5;
        $g = $h6;
        $h = $h7;

        for ($j = 0; $j < 64; ++$j) {
            $s1 = php_compat_sha256_rotr_helper($e,  6)
                ^ php_compat_sha256_rotr_helper($e, 11)
                ^ php_compat_sha256_rotr_helper($e, 25);

            $ch = ($e & $f) ^ (~$e & $g);

            $s0 = php_compat_sha256_rotr_helper($a,  2)
                ^ php_compat_sha256_rotr_helper($a, 13)
                ^ php_compat_sha256_rotr_helper($a, 22);

            $maj = ($a & $b) ^ ($a & $c) ^ ($b & $c);

            $t1 = php_compat_sha256_add32_helper(
                  php_compat_sha256_add32_helper(
                  php_compat_sha256_add32_helper(
                  php_compat_sha256_add32_helper($h, $s1), $ch), $k[$j]), $w[$j]);

            $t2 = php_compat_sha256_add32_helper($s0, $maj);

            $h = $g;
            $g = $f;
            $f = $e;
            $e = php_compat_sha256_add32_helper($d, $t1);
            $d = $c;
            $c = $b;
            $b = $a;
            $a = php_compat_sha256_add32_helper($t1, $t2);
        }

        $h0 = php_compat_sha256_add32_helper($h0, $a);
        $h1 = php_compat_sha256_add32_helper($h1, $b);
        $h2 = php_compat_sha256_add32_helper($h2, $c);
        $h3 = php_compat_sha256_add32_helper($h3, $d);
        $h4 = php_compat_sha256_add32_helper($h4, $e);
        $h5 = php_compat_sha256_add32_helper($h5, $f);
        $h6 = php_compat_sha256_add32_helper($h6, $g);
        $h7 = php_compat_sha256_add32_helper($h7, $h);
    }

    $h0 &= (int)0xffffffff;
    $h1 &= (int)0xffffffff;
    $h2 &= (int)0xffffffff;
    $h3 &= (int)0xffffffff;
    $h4 &= (int)0xffffffff;
    $h5 &= (int)0xffffffff;
    $h6 &= (int)0xffffffff;
    $h7 &= (int)0xffffffff;

    $hash = sprintf('%08x%08x%08x%08x%08x%08x%08x%08x', $h0, $h1, $h2, $h3, $h4, $h5, $h6, $h7);

    if ($raw_output) {
        return pack('H*', $hash);
    } else {
        return $hash;
    }
}

function php_compat_sha256_add32_helper($x, $y)
{
    $lsw = ($x & 0xffff) + ($y & 0xffff);
    $msw = ($x >> 16) + ($y >> 16) + ($lsw >> 16);
    return ($msw << 16) | ($lsw & 0xffff);
}

function php_compat_sha256_shr_helper($x, $n)
{
    return ($x >> $n) & (0x7fffffff >> ($n - 1));
}

function php_compat_sha256_rotr_helper($x, $n)
{
    return ($x << (32 - $n)) | ($x >> $n) & (0x7fffffff >> ($n - 1));
}
