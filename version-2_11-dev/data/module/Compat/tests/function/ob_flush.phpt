--TEST--
Function -- ob_flush
--SKIPIF--
<?php if (function_exists('ob_flush')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('ob_flush');

ob_start();
echo 'foo';
ob_flush();
ob_end_clean();
?>
--EXPECT--
foo