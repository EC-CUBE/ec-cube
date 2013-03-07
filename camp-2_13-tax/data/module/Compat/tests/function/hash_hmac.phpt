--TEST--
Function -- hash_hmac
--FILE--
<?php
require_once 'PHP/Compat/Function/hash_hmac.php';

$content = "This is a sample string used to test the hash_hmac function with various hashing algorithms";
$key = 'secret';

echo "md5: " . php_compat_hash_hmac('md5', $content, $key) . "\n";
echo "sha1: " . php_compat_hash_hmac('sha1', $content, $key) . "\n";
echo "sha256: " . php_compat_hash_hmac('sha256', $content, $key) . "\n";
echo "md5(raw): " . bin2hex(php_compat_hash_hmac('md5', $content, $key, true)) . "\n";
echo "sha256(raw): " . bin2hex(php_compat_hash_hmac('sha256', $content, $key, true)) . "\n";

?>
--EXPECT--
md5: 2a632783e2812cf23de100d7d6a463ae
sha1: 5bfdb62b97e2c987405463e9f7c193139c0e1fd0
sha256: 49bde3496b9510a17d0edd8a4b0ac70148e32a1d51e881ec76faa96534125838
md5(raw): 2a632783e2812cf23de100d7d6a463ae
sha256(raw): 49bde3496b9510a17d0edd8a4b0ac70148e32a1d51e881ec76faa96534125838
