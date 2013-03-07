--TEST--
Function -- inet_pton
--SKIPIF--
<?php if (function_exists('inet_pton')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('inet_pton');

echo 'test';
?>
--EXPECT--
test