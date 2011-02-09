--TEST--
Function -- vsprintf
--SKIPIF--
<?php if (function_exists('vsprintf')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('vsprintf');

$values = array (2, 'car');

$format = "There are %d monkeys in the %s";
vprintf($format, $values);
?>
--EXPECT--
There are 2 monkeys in the car