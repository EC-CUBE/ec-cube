--TEST--
Function -- strpbrk
--SKIPIF--
<?php if (function_exists('strpbrk')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('strpbrk');

$haystack = 'To be or not to be';
$char_list  = 'jhdn';

var_dump(strpbrk($haystack, $char_list));
?>
--EXPECT--
string(9) "not to be"