--TEST--
Function -- file_get_contents
--SKIPIF--
<?php if (function_exists('file_get_contents')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('file_get_contents');

$tmpfname = tempnam('/tmp', 'php');
$handle = fopen($tmpfname, 'w');
fwrite($handle, "test test");
fclose($handle);

echo file_get_contents($tmpfname);

unlink($tmpfname);
?>
--EXPECT--
test test