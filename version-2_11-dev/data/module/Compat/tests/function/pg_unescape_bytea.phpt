--TEST--
Function -- pg_unescape_bytea
--SKIPIF--
<?php if (function_exists('pg_unescape_bytea')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('pg_unescape_bytea');

echo 'test';
?>
--EXPECT--
test