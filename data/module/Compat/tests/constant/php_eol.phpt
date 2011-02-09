--TEST--
Constant -- PHP_EOL
--SKIPIF--
<?php if (defined('PHP_EOL')) { echo 'skip'; } ?>
--FILE--
<?php
require_once ('PHP/Compat.php');
PHP_Compat::loadConstant('PHP_EOL');

if (PHP_EOL == "\n" || PHP_EOL == "\r\n" || PHP_EOL == "\r") {
    echo 'true';
} else {
    echo 'false';
}
?>
--EXPECT--
true