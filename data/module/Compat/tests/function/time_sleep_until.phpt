--TEST--
Function -- time_sleep_until
--SKIPIF--
<?php if (function_exists('time_sleep_until')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('time_sleep_until');

function ehandler($no, $str)
{
    echo '(Warning) ';
}
set_error_handler('ehandler');

$time = time();
time_sleep_until($time + 3);
echo '3:', time() - $time;

echo PHP_EOL;

$time = time();
time_sleep_until($time - 1);
echo '-1:', time() - $time;

restore_error_handler();
?>
--EXPECT--
3:3
(Warning) -1:0