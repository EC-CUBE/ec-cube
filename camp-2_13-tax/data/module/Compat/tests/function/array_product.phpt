--TEST--
Function -- array_product
--SKIPIF--
<?php if (function_exists('array_product')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_product');

function ehandler($no, $str)
{
    echo '(Warning) ';
}
set_error_handler('ehandler');

$tests = array(
    'foo',
    array(),
    array(0),
    array(3),
    array(3, 3),
    array(0.5, 2, 3)
);

foreach ($tests as $v) {
    echo "testing: (", (is_array($v) ? implode(' * ', $v) : $v), ")\n    result: ";
    var_dump(array_product($v));
    echo "\n\n"; 
}

restore_error_handler();
?>
--EXPECT--
testing: (foo)
    result: (Warning) NULL


testing: ()
    result: int(0)


testing: (0)
    result: int(0)


testing: (3)
    result: int(3)


testing: (3 * 3)
    result: int(9)


testing: (0.5 * 2 * 3)
    result: float(3)