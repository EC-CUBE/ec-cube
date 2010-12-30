--TEST--
Function -- array_diff_uassoc
--SKIPIF--
<?php if (function_exists('array_diff_uassoc')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_diff_uassoc');

function key_compare_func($a, $b)
{
    if ($a === $b) {
        return 0;
    }

    return ($a > $b) ? 1 : -1;
}

$array1 = array('a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red');
$array2 = array('a' => 'green', 'yellow', 'red');
$result = array_diff_uassoc($array1, $array2, 'key_compare_func');
print_r($result);

?>
--EXPECT--
Array
(
    [b] => brown
    [c] => blue
    [0] => red
)