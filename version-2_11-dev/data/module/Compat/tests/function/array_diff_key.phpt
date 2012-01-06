--TEST--
Function -- array_diff_key
--SKIPIF--
<?php if (function_exists('array_diff_key')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_diff_key');

$array1 = array('blue'  => 1, 'red'  => 2, 'green'  => 3, 'purple' => 4);
$array2 = array('green' => 5, 'blue' => 6, 'yellow' => 7, 'cyan'   => 8);

print_r(array_diff_key($array1, $array2));

?>
--EXPECT--
Array
(
    [red] => 2
    [purple] => 4
)