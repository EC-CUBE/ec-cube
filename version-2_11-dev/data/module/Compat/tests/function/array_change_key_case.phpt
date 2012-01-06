--TEST--
Function -- array_change_key_case
--SKIPIF--
<?php if (function_exists('array_change_key_case')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_change_key_case');

$in = array('FirSt' => 1, 'SecOnd' => 4);
print_r(array_change_key_case($in));
print_r(array_change_key_case($in, CASE_LOWER));
print_r(array_change_key_case($in, CASE_UPPER));
$in = array('FIRST' => 1, 'SECOND' => 4);
print_r(array_change_key_case($in));
print_r(array_change_key_case($in, CASE_LOWER));
print_r(array_change_key_case($in, CASE_UPPER));
$in = array('first' => 1, 'second' => 4);
print_r(array_change_key_case($in));
print_r(array_change_key_case($in, CASE_LOWER));
print_r(array_change_key_case($in, CASE_UPPER));
$in = array('foo', 'bar');
print_r(array_change_key_case($in));
print_r(array_change_key_case($in, CASE_LOWER));
print_r(array_change_key_case($in, CASE_UPPER));
$in = array();
print_r(array_change_key_case($in));
print_r(array_change_key_case($in, CASE_LOWER));
print_r(array_change_key_case($in, CASE_UPPER));
?>
--EXPECT--
Array
(
    [first] => 1
    [second] => 4
)
Array
(
    [first] => 1
    [second] => 4
)
Array
(
    [FIRST] => 1
    [SECOND] => 4
)
Array
(
    [first] => 1
    [second] => 4
)
Array
(
    [first] => 1
    [second] => 4
)
Array
(
    [FIRST] => 1
    [SECOND] => 4
)
Array
(
    [first] => 1
    [second] => 4
)
Array
(
    [first] => 1
    [second] => 4
)
Array
(
    [FIRST] => 1
    [SECOND] => 4
)
Array
(
    [0] => foo
    [1] => bar
)
Array
(
    [0] => foo
    [1] => bar
)
Array
(
    [0] => foo
    [1] => bar
)
Array
(
)
Array
(
)
Array
(
)