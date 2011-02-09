--TEST--
Function -- stripos
--SKIPIF--
<?php if (function_exists('stripos')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('stripos');

$haystack = 'Cat Dinner Dog Lion Mouse Sheep Wolf Cat Dog';
$needle  = 'DOG';

// Simple
var_dump(stripos($haystack, $needle));

// With offset
var_dump(stripos($haystack, $needle, 4));
var_dump(stripos($haystack, $needle, 10));
var_dump(stripos($haystack, $needle, 15));
var_dump(stripos($haystack, 'idontexist', 15));
?>
--EXPECT--
int(11)
int(11)
int(11)
int(41)
bool(false)