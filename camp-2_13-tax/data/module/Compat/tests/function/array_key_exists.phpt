--TEST--
Function -- array_key_exists
--SKIPIF--
<?php if (function_exists('array_key_exists')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_key_exists');

$search_array = array("first" => 1, "second" => 4);
if (array_key_exists("first", $search_array)) {
   echo "The 'first' element is in the array";
}
?>
--EXPECT--
The 'first' element is in the array