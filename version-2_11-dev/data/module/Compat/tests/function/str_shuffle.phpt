--TEST--
Function -- str_shuffle
--SKIPIF--
<?php if (function_exists('str_shuffle')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('str_shuffle');

$string = str_shuffle('ab');
if ($string == 'ab' ||
    $string == 'ba' ||
    $string == 'aa' ||
    $string == 'bb') {

    echo "true";
}
?>
--EXPECT--
true