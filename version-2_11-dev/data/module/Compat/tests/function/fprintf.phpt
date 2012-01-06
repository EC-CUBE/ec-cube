--TEST--
Function -- fprintf
--SKIPIF--
<?php if (function_exists('fprintf')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('fprintf');

$tmpfname = tempnam('/tmp', 'php');
$handle = fopen($tmpfname, 'w');
fprintf($handle, 'The %s went to the %s for %d days', 'dog', 'park', 2);
fclose($handle);
$data = implode('', file($tmpfname));
unlink($tmpfname);

echo $data;
?>
--EXPECT--
The dog went to the park for 2 days