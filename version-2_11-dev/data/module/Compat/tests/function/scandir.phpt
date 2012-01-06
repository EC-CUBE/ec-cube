--TEST--
Function -- scandir
--SKIPIF--
<?php if (function_exists('scandir')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('scandir');

// Create a folder and fill it with files
mkdir('tmp');
touch('tmp/test1');
touch('tmp/test2');

// Scan it
$dir    = 'tmp';
// Not sorted
$files = scandir($dir);
// Sorted
$files2 = scandir($dir, 1);

// List the results
print_r($files);
print_r($files2);

// Remove the files
unlink('tmp/test1');
unlink('tmp/test2');
rmdir('tmp');
?>
--EXPECT--
Array
(
    [0] => .
    [1] => ..
    [2] => test1
    [3] => test2
)
Array
(
    [0] => test2
    [1] => test1
    [2] => ..
    [3] => .
)