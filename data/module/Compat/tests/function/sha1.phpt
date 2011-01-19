--TEST--
Function -- sha1
--FILE--
<?php
require_once 'PHP/Compat/Function/sha1.php';

$tests = array(
    'abc',
    'abcdbcdecdefdefgefghfghighijhijkijkljklmklmnlmnomnopnopq',
    'a',
    '0123456701234567012345670123456701234567012345670123456701234567',
    ''
);

foreach ($tests as $test) {
    echo php_compat_sha1($test), "\n";
}
?>
--EXPECT--
a9993e364706816aba3e25717850c26c9cd0d89d
84983e441c3bd26ebaae4aa1f95129e5e54670f1
86f7e437faa5a7fce15d1ddcb9eaeaea377667b8
e0c094e867ef46c350ef54a7f59dd60bed92ae83
da39a3ee5e6b4b0d3255bfef95601890afd80709
