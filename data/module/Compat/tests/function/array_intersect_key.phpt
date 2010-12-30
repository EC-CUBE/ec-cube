--TEST--
Function -- array_intersect_key
--SKIPIF--
<?php if (function_exists('array_intersect_key')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_intersect_key');

$array1 = array('blue'  => 1, 'red'  => 2, 'green'  => 3, 'purple' => 4);
$array2 = array('green' => 5, 'blue' => 6, 'yellow' => 7, 'cyan'   => 8);

print_r(array_intersect_key($array1, $array2));

?>
--EXPECT--
Array
(
    [blue] => 1
    [green] => 3
)