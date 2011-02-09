--TEST--
Function -- str_split
--SKIPIF--
<?php if (function_exists('str_split')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('str_split');

$str = "Hello Friend";

// Simple
$arr = str_split($str);
print_r($arr);

// With a chunk size specified
$arr = str_split($str, 3);
print_r($arr);

// With chunk size bigger than the string
$arr = str_split($str, 60);
print_r($arr);

// String that has a remainder less than the chunk size
$arr = str_split($str, 11);
print_r($arr);
?>
--EXPECT--
Array
(
    [0] => H
    [1] => e
    [2] => l
    [3] => l
    [4] => o
    [5] =>  
    [6] => F
    [7] => r
    [8] => i
    [9] => e
    [10] => n
    [11] => d
)
Array
(
    [0] => Hel
    [1] => lo 
    [2] => Fri
    [3] => end
)
Array
(
    [0] => Hello Friend
)
Array
(
    [0] => Hello Frien
    [1] => d
)