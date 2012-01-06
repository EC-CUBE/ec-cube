--TEST--
Constant -- PATH_SEPARATOR
--SKIPIF--
<?php if (defined('PATH_SEPARATOR')) { echo 'skip'; } ?>
--FILE--
<?php
require_once ('PHP/Compat.php');
PHP_Compat::loadConstant('PATH_SEPARATOR');

echo (PATH_SEPARATOR == ';' || PATH_SEPARATOR == ':') ?
        'true' :
        'false';
?>
--EXPECT--
true