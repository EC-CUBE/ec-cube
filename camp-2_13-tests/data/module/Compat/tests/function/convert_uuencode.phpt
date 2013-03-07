--TEST--
Function -- convert_uuencode
--SKIPIF--
<?php if (function_exists('convert_uuencode')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('convert_uuencode');

// Simple test
echo md5(convert_uuencode('This is a simple test')), "\n";

// Really messy test
$string = '';
for ($i = 0; 127 > $i; $i++) {
    $string .= str_repeat(chr($i), 10);
}
echo md5(convert_uuencode($string));

?>
--EXPECT--
d7974131c8970783f70851c83fe17767
19acf7157a8345307ea5e5ea6878abb4