--TEST--
Function -- fputcsv
--SKIPIF--
<?php if (function_exists('fputcsv')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('fputcsv');

echo 'test';
?>
--EXPECT--
test