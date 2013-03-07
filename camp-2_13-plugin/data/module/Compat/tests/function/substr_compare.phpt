--TEST--
Function -- substr_compare
--SKIPIF--
<?php if (function_exists('substr_compare')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('substr_compare');

echo substr_compare("abcde", "bc", 1, 2), "\n";
echo substr_compare("abcde", "bcg", 1, 2), "\n";
echo substr_compare("abcde", "BC", 1, 2, true), "\n"; 
echo substr_compare("abcde", "bc", 1, 3), "\n";
echo substr_compare("abcde", "cd", 1, 2);
?>
--EXPECT--
0
0
0
1
-1