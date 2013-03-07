--TEST--
Constant -- CLI Constants
--SKIPIF--
<?php if (defined('STDIN')) { echo 'skip'; } ?>
--FILE--
<?php
require_once ('PHP/Compat.php');
PHP_Compat::loadConstant('STD');

echo (is_resource(STDIN)) ? 'true' : 'false', "\n";
echo (is_resource(STDOUT)) ? 'true' : 'false', "\n";
echo (is_resource(STDERR)) ? 'true' : 'false';
?>
--EXPECT--
true
true
true