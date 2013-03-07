--TEST--
Function -- convert_uudecode
--SKIPIF--
<?php if (function_exists('convert_uudecode')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('convert_uudecode');

$string = base64_decode('NTUmQUk8UiFJPFIhQSgnLUk7NyFMOTIhVDk3LVQKYAo=');
echo convert_uudecode($string);
?>
--EXPECT--
This is a simple test