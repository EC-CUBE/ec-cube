--TEST--
Function -- bcinvert
--SKIPIF--
<?php if (function_exists('bcinvert')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('bcinvert');

echo 'test';
?>
--EXPECT--
test