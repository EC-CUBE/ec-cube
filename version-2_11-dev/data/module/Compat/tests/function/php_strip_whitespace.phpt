--TEST--
Function -- php_strip_whitespace
--SKIPIF--
<?php
if (function_exists('php_strip_whitespace') ||
    !extension_loaded('tokenizer')) {
        
    echo 'skip';
}
?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('php_strip_whitespace');

// Here is some sample PHP code to write to the file
$string = '<?php
// PHP comment here

/*
 * Another PHP comment
 */

echo        php_strip_whitespace($_SERVER[\'PHP_SELF\']);
// Newlines are considered whitespace, and are removed too:
do_nothing();
?>';

// Create a temp file
$tmpfname = tempnam('/tmp', 'phpcompat');
$fh = fopen($tmpfname, 'w');
fwrite($fh, $string);

// Test
echo php_strip_whitespace($tmpfname);

// Close
fclose($fh);
?>
--EXPECT--
<?php
 echo php_strip_whitespace($_SERVER['PHP_SELF']); do_nothing(); ?>