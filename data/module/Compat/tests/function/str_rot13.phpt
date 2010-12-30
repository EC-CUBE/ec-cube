--TEST--
Function -- str_rot13
--SKIPIF--
<?php if (function_exists('str_rot13')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('str_rot13');

$str = "The quick brown fox jumped over the lazy dog.";
echo str_rot13($str);
?>
--EXPECT--
Gur dhvpx oebja sbk whzcrq bire gur ynml qbt.