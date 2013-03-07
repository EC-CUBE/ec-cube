--TEST--
Function -- bcpowmod
--SKIPIF--
<?php if (function_exists('bcpowmod')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('bcpowmod');

echo 'test';
?>
--EXPECT--
test