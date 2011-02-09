--TEST--
Function -- vprintf
--SKIPIF--
<?php if (function_exists('vprintf')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('vprintf');

$values = array (2, 'car');

$format = "There are %d monkeys in the %s";
vprintf($format, $values);
?>
--EXPECT--
There are 2 monkeys in the car