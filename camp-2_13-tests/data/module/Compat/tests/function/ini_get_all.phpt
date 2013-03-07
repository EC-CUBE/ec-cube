--TEST--
Function -- ini_get_all
--SKIPIF--
<?php if (function_exists('ini_get_all')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('ini_get_all');

if (is_array(ini_get_all())) {
    echo "true\n";
}

if (is_array(ini_get_all('mysql'))) {
    echo "true\n";
}
?>
--EXPECT--
true
true