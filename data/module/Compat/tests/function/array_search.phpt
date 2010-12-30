--TEST--
Function -- array_search
--SKIPIF--
<?php if (function_exists('array_search')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_search');

$array = array(0 => 'blue', 1 => 'red', 2 => 'green', 3 => 'red');

var_dump(array_search('green', $array));
var_dump(array_search('red', $array));
?>
--EXPECT--
int(2)
int(1)