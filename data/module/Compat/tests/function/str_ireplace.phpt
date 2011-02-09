--TEST--
Function -- str_ireplace
--SKIPIF--
<?php if (function_exists('str_ireplace')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('str_ireplace');

//
// Simple
//

$search = '{object}';
$replace = 'fence';
$subject = 'The dog jumped over the {object}';

echo str_ireplace($search, $replace, $subject), "\n";

//
// Test 1: With subject as array
//

// As a full array
$search = '{SUBJECT}';
$replace = 'Lady';
$subject = array('A {subject}', 'The {subject}', 'My {subject}');
print_r(str_ireplace($search, $replace, $subject));

// As a single array
$search = '{SUBJECT}';
$replace = 'Lady';
$subject = array('The dog jumped over the {object}');
print_r(str_ireplace($search, $replace, $subject));


//
// Test 2: Search as string, replace as array
//

$search = '{object}';
$replace = array('cat', 'dog', 'tiger');
$subject = 'The dog jumped over the {object}';
// Supress the error, no way of knowing how it'll turn out on the users machine
echo @str_ireplace($search, $replace, $subject), "\n";


//
// Test 3: Search as array, Replace as string
//

$search = array('{ANIMAL}', '{OBJECT}', '{THING}');
$replace = 'frog';
$subject = 'The {animal} jumped over the {object} and the {thing}...';
echo str_ireplace($search, $replace, $subject), "\n";


//
// Test 4: Search and Replace as arrays
//

// Simple
$search = array('{ANIMAL}', '{OBJECT}');
$replace = array('frog', 'gate');
$subject = 'The {animal} jumped over the {object}';
echo str_ireplace($search, $replace, $subject), "\n";

// More in search
$search = array('{ANIMAL}', '{OBJECT}', '{THING}');
$replace = array('frog', 'gate');
$subject = 'The {animal} jumped over the {object} and the {thing}...';
echo str_ireplace($search, $replace, $subject), "\n";

// More in replace
$search = array('{ANIMAL}', '{OBJECT}');
$replace = array('frog', 'gate', 'door');
$subject = 'The {animal} jumped over the {object} and the {thing}...';
echo str_ireplace($search, $replace, $subject), "\n";


//
// Test 5: All arrays
//

$search = array('{ANIMAL}', '{OBJECT}', '{THING}');
$replace = array('frog', 'gate', 'beer');
$subject = array('A {animal}', 'The {object}', 'My {thing}');
print_r(str_ireplace($search, $replace, $subject));

?>
--EXPECT--
The dog jumped over the fence
Array
(
    [0] => A Lady
    [1] => The Lady
    [2] => My Lady
)
Array
(
    [0] => The dog jumped over the {object}
)
The dog jumped over the Array
The frog jumped over the frog and the frog...
The frog jumped over the gate
The frog jumped over the gate and the ...
The frog jumped over the gate and the {thing}...
Array
(
    [0] => A frog
    [1] => The gate
    [2] => My beer
)