--TEST--
Function -- idate
--SKIPIF--
<?php if (function_exists('idate')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('idate');

$tests = array(
    'B',    // OK
    'd',    // ...
    'h',
    'H',
    'i',
    'I',
    'L',
    'm',
    's',
    't',
    'U',
    'w',
    'W',
    'y',
    'Y',
    'z',    // ...
    'Z',    // OK

    'foo',  // NOK
    '',     // NOK
    '!',    // NOK
    '\\'    // NOK
);

function ehandler($no, $str)
{
    echo '(Warning) ';
}
set_error_handler('ehandler');

foreach ($tests as $v) {
    echo 'testing: ';
    var_dump($v);
    echo "\nresult:  ";
    $res = idate($v);
    if (!$res) {
        var_dump($res);
    } else {
        echo "> 0\n";
    }
    echo "\n\n";
}

restore_error_handler();
?>
--EXPECT--
testing: string(1) "B"

result:  > 0


testing: string(1) "d"

result:  > 0


testing: string(1) "h"

result:  > 0


testing: string(1) "H"

result:  > 0


testing: string(1) "i"

result:  > 0


testing: string(1) "I"

result:  int(0)


testing: string(1) "L"

result:  int(0)


testing: string(1) "m"

result:  > 0


testing: string(1) "s"

result:  > 0


testing: string(1) "t"

result:  > 0


testing: string(1) "U"

result:  > 0


testing: string(1) "w"

result:  > 0


testing: string(1) "W"

result:  > 0


testing: string(1) "y"

result:  > 0


testing: string(1) "Y"

result:  > 0


testing: string(1) "z"

result:  > 0


testing: string(1) "Z"

result:  int(0)


testing: string(3) "foo"

result:  (Warning) bool(false)


testing: string(0) ""

result:  (Warning) bool(false)


testing: string(1) "!"

result:  int(0)


testing: string(1) "\"

result:  int(0)