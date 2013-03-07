--TEST--
Function -- array_diff_assoc
--SKIPIF--
<?php if (function_exists('array_diff_assoc')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_diff_assoc');

$array1 = array("a" => "green", "b" => "brown", "c" => "blue", "red");
$array2 = array("a" => "green", "yellow", "red");
$result = array_diff_assoc($array1, $array2);
print_r($result);
?>
--EXPECT--
Array
(
    [b] => brown
    [c] => blue
    [0] => red
)