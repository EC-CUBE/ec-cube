--TEST--
Constant -- E_STRICT
--SKIPIF--
<?php if (defined('E_STRICT')) { echo 'skip'; } ?>
--FILE--
<?php
require_once ('PHP/Compat.php');
PHP_Compat::loadConstant('E_STRICT');

echo E_STRICT;
?>
--EXPECT--
2048