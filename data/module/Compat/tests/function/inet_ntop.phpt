--TEST--
Function -- inet_ntop
--SKIPIF--
<?php if (function_exists('inet_ntop')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('inet_ntop');

$adds = array(
    '127.0.0.1'                  => '7f000001',
    '192.232.131.222'            => 'c0e883de',
    '::1'                        => '00000000000000000000000000000001',
    '2001:260:0:10::1'           => '20010260000000100000000000000001',
    'fe80::200:4cff:fe43:172f'   => 'fe8000000000000002004cfffe43172f'
);

foreach ($adds as $k => $v) {
    echo "\ntesting: $k\n    ";
    var_dump(inet_ntop(pack('H*', $v)));
}

?>
--EXPECT--
testing: 127.0.0.1
    string(9) "127.0.0.1"

testing: 192.232.131.222
    string(15) "192.232.131.222"

testing: ::1
    string(3) "::1"

testing: 2001:260:0:10::1
    string(16) "2001:260:0:10::1"

testing: fe80::200:4cff:fe43:172f
    string(24) "fe80::200:4cff:fe43:172f"