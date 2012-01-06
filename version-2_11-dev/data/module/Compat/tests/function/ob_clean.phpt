--TEST--
Function -- ob_clean
--SKIPIF--
<?php if (function_exists('ob_clean')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('ob_clean');

ob_start();
echo 'foo';
ob_clean();
echo 'foo';
ob_end_flush();
?>
--EXPECT--
foo