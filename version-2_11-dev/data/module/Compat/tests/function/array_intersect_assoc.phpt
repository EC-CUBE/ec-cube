--TEST--
Function -- array_intersect_assoc
--SKIPIF--
<?php if (function_exists('array_intersect_assoc')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_intersect_assoc');

$array1 = array("a" => "green", "b" => "brown", "c" => "blue", "red");
$array2 = array("a" => "green", "yellow", "red");
$result = array_intersect_assoc($array1, $array2);
print_r($result);

?>
--EXPECT--
Array
(
    [a] => green
)