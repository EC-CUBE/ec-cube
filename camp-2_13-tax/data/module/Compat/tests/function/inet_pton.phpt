--TEST--
Function -- inet_pton
--SKIPIF--
<?php if (function_exists('inet_pton')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('inet_pton');

$adds = array(
    '127.0.0.1'                  => '7f000001',
    '192.232.131.222'            => 'c0e883de',
    '::1'                        => '00000000000000000000000000000001',
    '2001:260:0:10::1'           => '20010260000000100000000000000001',
    'fe80::200:4cff:fe43:172f'   => 'fe8000000000000002004cfffe43172f'
);

foreach ($adds as $k => $v) {
    echo "\ntesting: $k\n    ";
    echo bin2hex(inet_pton($k)), "\n";
}

?>
--EXPECT--
testing: 127.0.0.1
    7f000001

testing: 192.232.131.222
    c0e883de

testing: ::1
    00000000000000000000000000000001

testing: 2001:260:0:10::1
    20010260000000100000000000000001

testing: fe80::200:4cff:fe43:172f
    fe8000000000000002004cfffe43172f