--TEST--
Method -- PHP_Compat::loadVersion
--FILE--
<?php
require_once ('PHP/Compat.php');

// Rather useless tests
$res = PHP_Compat::loadVersion('3.0.0');
var_dump(is_array($res));

$res = PHP_Compat::loadVersion('9.0.0');
var_dump(is_array($res));

$res = PHP_Compat::loadVersion();
var_dump(is_array($res));

?>
--EXPECT--
bool(true)
bool(true)
bool(true)