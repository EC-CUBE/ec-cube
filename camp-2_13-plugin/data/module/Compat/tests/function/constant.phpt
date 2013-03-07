--TEST--
Function -- constant
--SKIPIF--
<?php if (function_exists('constant')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('constant');

$constant = 'BAR';
define($constant, 'foo');
echo constant($constant);
?>
--EXPECT--
foo