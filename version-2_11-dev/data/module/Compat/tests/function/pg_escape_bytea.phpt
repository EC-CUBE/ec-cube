--TEST--
Function -- pg_escape_bytea
--SKIPIF--
<?php if (function_exists('pg_escape_bytea')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('pg_escape_bytea');

echo 'test';
?>
--EXPECT--
test