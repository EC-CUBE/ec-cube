--TEST--
Function -- array_intersect_ukey
--SKIPIF--
<?php if (function_exists('array_intersect_ukey')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_intersect_ukey');

function key_compare_func($key1, $key2)
{
    if ($key1 == $key2) {
        return 0;
    } elseif ($key1 > $key2) {
        return 1;
    } else {
        return -1;
    }
}

$array1 = array('blue'  => 1, 'red'  => 2, 'green'  => 3, 'purple' => 4);
$array2 = array('green' => 5, 'blue' => 6, 'yellow' => 7, 'cyan'   => 8);

print_r(array_intersect_ukey($array1, $array2, 'key_compare_func'));

?>
--EXPECT--
Array
(
    [blue] => 1
    [green] => 3
)