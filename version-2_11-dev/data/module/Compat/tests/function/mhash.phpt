--TEST--
Function -- mhash
--SKIPIF--
<?php if (function_exists('mhash')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('mhash');

$input = "what do ya want for nothing?";

$hash = mhash(MHASH_MD5, $input);
echo bin2hex($hash) . "\n";

$hash = mhash(MHASH_MD5, $input, "Jefe");
echo bin2hex($hash) . "\n";
?>
--EXPECT--
ae2e4b39f3b5ee2c8b585994294201ea
750c783e6ab0b503eaa86e310a5db738