--TEST--
Function -- hash
--FILE--
<?php
require_once 'PHP/Compat/Function/hash.php';

$content = "This is a sample string used to test the hash function with various hashing algorithms";

echo "md5: " . php_compat_hash('md5', $content). "\n";
echo "sha1: " . php_compat_hash('sha1', $content). "\n";
echo "sha256: " . php_compat_hash('sha256', $content). "\n";
echo "md5(raw): " . bin2hex(php_compat_hash('md5', $content, true)). "\n";
echo "sha256(raw): " . bin2hex(php_compat_hash('sha256', $content, true)). "\n";

?>
--EXPECT--
md5: bf33deeefaf5a9413160935be950cc07
sha1: f0dc0e88cc1008e46762f40a1b4a4c0b6baedfa0
sha256: a78149615dd1ef8aeb22a8254c36edd87713f2e79a052a89ff32ed94e827d47b
md5(raw): bf33deeefaf5a9413160935be950cc07
sha256(raw): a78149615dd1ef8aeb22a8254c36edd87713f2e79a052a89ff32ed94e827d47b
