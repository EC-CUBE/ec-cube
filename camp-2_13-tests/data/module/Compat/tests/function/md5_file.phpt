--TEST--
Function -- md5_file
--SKIPIF--
<?php if (function_exists('md5_file')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('md5_file');

echo md5_file(__FILE__);
?>
--EXPECT--
762a55bb01c6133a956599e6a51c49b0