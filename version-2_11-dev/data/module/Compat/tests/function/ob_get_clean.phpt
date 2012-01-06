--TEST--
Function -- ob_get_clean
--SKIPIF--
<?php if (function_exists('ob_get_clean')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('ob_get_clean');

ob_start();
echo 'foo';
$buffer = ob_get_clean();
echo $buffer;
?>
--EXPECT--
foo