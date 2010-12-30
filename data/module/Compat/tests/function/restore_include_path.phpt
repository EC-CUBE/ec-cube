--TEST--
Function -- restore_include_path
--SKIPIF--
<?php if (function_exists('restore_include_path')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('restore_include_path');

$orig = ini_get('include_path');
ini_set('include_path', 'foo');
echo ini_get('include_path'), "\n";

restore_include_path();
$new = ini_get('include_path');

if ($orig == $new) {
    echo 'true';
}
?>
--EXPECT--
foo
true