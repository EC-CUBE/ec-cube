--TEST--
Function -- set_include_path
--SKIPIF--
<?php if (function_exists('set_include_path')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('set_include_path');

set_include_path('foo');
echo ini_get('include_path');
?>
--EXPECT--
foo