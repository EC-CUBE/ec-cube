--TEST--
Function -- array_uintersect_uassoc
--SKIPIF--
<?php if (function_exists('array_uintersect_uassoc')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_uintersect_uassoc');

$array1 = array('a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red');
$array2 = array('a' => 'GREEN', 'B' => 'brown', 'yellow', 'red');

print_r(array_uintersect_uassoc($array1, $array2, 'strcasecmp', 'strcasecmp'));
?>
--EXPECT--
Array
(
    [a] => green
    [b] => brown
)