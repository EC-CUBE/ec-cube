--TEST--
Function -- floatval
--SKIPIF--
<?php if (function_exists('floatval')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('floatval');

$var = '12312.123';
var_dump(floatval($var));
?>
--EXPECT--
float(12312.123)