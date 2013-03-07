--TEST--
Function -- array_uintersect
--SKIPIF--
<?php if (function_exists('array_uintersect')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_uintersect');

$array1 = array('a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red');
$array2 = array('a' => 'GREEN', 'B' => 'brown', 'yellow', 'red');

print_r(array_uintersect($array1, $array2, 'strcasecmp'));
?>
--EXPECT--
Array
(
    [a] => green
    [b] => brown
    [0] => red
)