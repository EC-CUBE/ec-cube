--TEST--
Constant -- DIRECTORY_SEPARATOR
--SKIPIF--
<?php if (defined('DIRECTORY_SEPARATOR')) { echo 'skip'; } ?>
--FILE--
<?php
require_once ('PHP/Compat.php');
PHP_Compat::loadConstant('DIRECTORY_SEPARATOR');

echo (DIRECTORY_SEPARATOR == '\\' || DIRECTORY_SEPARATOR == '/') ?
        'true' :
        'false';
?>
--EXPECT--
true