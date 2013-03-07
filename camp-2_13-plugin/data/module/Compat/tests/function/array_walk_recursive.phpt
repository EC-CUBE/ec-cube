--TEST--
Function -- array_walk_recursive
--SKIPIF--
<?php if (function_exists('array_walk_recursive')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_walk_recursive');

$sweet = array('a' => 'apple', 'b' => 'banana');
$fruits = array('sweet' => $sweet, 'sour' => 'lemon');

function test_print($item, $key)
{
   echo "$key holds $item\n";
}

array_walk_recursive($fruits, 'test_print');
?>
--EXPECT--
a holds apple
b holds banana
sour holds lemon