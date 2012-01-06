--TEST--
Function -- pg_affected_rows
--SKIPIF--
<?php if (function_exists('pg_affected_rows')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('pg_affected_rows');

echo 'test';
?>
--EXPECT--
test