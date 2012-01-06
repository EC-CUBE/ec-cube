--TEST--
Function -- ob_get_flush
--SKIPIF--
<?php if (function_exists('ob_get_flush')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('ob_get_flush');

ob_start();
echo 'foo';
$buffer = ob_get_flush();
echo $buffer;
?>
--EXPECT--
foofoo