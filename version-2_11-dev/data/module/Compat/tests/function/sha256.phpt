--TEST--
Function -- sha256
--FILE--
<?php
require_once 'PHP/Compat/Function/_sha256.php';

echo php_compat_sha256('') . "\n";
echo php_compat_sha256('a') . "\n";
echo php_compat_sha256('012345678901234567890123456789012345678901234567890123456789') . "\n";

/* FIPS-180 Vectors */
echo php_compat_sha256('abc') . "\n";
echo php_compat_sha256('abcdbcdecdefdefgefghfghighijhijkijkljklmklmnlmnomnopnopq') . "\n";
?>
--EXPECT--
e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855
ca978112ca1bbdcafac231b39a23dc4da786eff8147c4e72b9807785afee48bb
5e43c8704ac81f33d701c1ace046ba9f257062b4d17e78f3254cbf243177e4f2
ba7816bf8f01cfea414140de5dae2223b00361a396177a9cb410ff61f20015ad
248d6a61d20638b8e5c026930c3e6039a33ce45964ff2167f6ecedd419db06c1
