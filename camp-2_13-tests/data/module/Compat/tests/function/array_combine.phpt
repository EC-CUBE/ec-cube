--TEST--
Function -- array_combine
--SKIPIF--
<?php if (function_exists('array_combine')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_combine');

$a = array('green', 'red', 'yellow');
$b = array('avocado', 'apple', 'banana');
$c = array_combine($a, $b);

print_r($c);
?>
--EXPECT--
Array
(
    [green] => avocado
    [red] => apple
    [yellow] => banana
)