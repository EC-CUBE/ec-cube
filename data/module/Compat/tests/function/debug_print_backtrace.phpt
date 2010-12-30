--TEST--
Function -- debug_print_backtrace
--SKIPIF--
<?php if (function_exists('debug_print_backtrace')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('debug_print_backtrace');

echo 'test';
?>
--EXPECT--
test