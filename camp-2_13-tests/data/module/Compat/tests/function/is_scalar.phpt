--TEST--
Function -- is_scalar
--SKIPIF--
<?php if (function_exists('is_scalar')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('is_scalar');

echo 'test';
?>
--EXPECT--
test