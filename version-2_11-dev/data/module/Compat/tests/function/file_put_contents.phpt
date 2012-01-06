--TEST--
Function -- file_put_contents
--SKIPIF--
<?php if (function_exists('file_put_contents')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('file_put_contents');

// Create a temp file
$tmpfname = tempnam('/tmp', 'phpcompat');

// With a string
$string = "abcd";

echo file_put_contents($tmpfname, $string), "\n";
echo implode('', file($tmpfname)), "\n";

// With an array
$string = array('foo', 'bar');

echo file_put_contents($tmpfname, $string), "\n";
echo implode('', file($tmpfname)), "\n";

// Test append
$string = 'foobar';
$string2 = 'testtest';
$tmpfname = tempnam('/tmp', 'php');

echo file_put_contents($tmpfname, $string), "\n";
echo file_put_contents($tmpfname, $string2, FILE_APPEND), "\n";
echo implode('', file($tmpfname)), "\n";
echo file_put_contents($tmpfname, $string2), "\n";
echo implode('', file($tmpfname));

unlink($tmpfname);
?>
--EXPECT--
4
abcd
6
foobar
6
8
foobartesttest
8
testtest