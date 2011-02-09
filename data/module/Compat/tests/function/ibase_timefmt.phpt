--TEST--
Function -- ibase_timefmt
--SKIPIF--
<?php if (function_exists('ibase_timefmt')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('ibase_timefmt');

echo 'test';
?>
--EXPECT--
test