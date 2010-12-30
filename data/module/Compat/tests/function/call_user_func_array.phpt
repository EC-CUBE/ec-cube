--TEST--
Function -- call_user_func_array
--SKIPIF--
<?php if (function_exists('call_user_func_array')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('call_user_func_array');

function somefunc ($param1, $param2, $param3) {
	echo $param1, "\n", $param2, "\n", $param3;
}

$args = array ('foo', 'bar', 'meta');
call_user_func_array('somefunc', $args);
?>
--EXPECT--
foo
bar
meta