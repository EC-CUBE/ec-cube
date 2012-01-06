--TEST--
Function -- get_headers
--SKIPIF--
<?php if (function_exists('get_headers')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('get_headers');

echo 'test';
?>
--EXPECT--
test