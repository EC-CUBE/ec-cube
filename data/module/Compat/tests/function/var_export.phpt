--TEST--
Function -- var_export
--SKIPIF--
<?php if (function_exists('var_export')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('var_export');

// Simple
var_export(true);
echo "\n";

var_export(false);
echo "\n";

var_export(null); 
echo "\n";

$fp = fopen(__FILE__, 'r');
var_export($fp);
fclose($fp);
echo "\n";

var_export(array(1, array(2, array(3, 4), array(5, array(6, array(7))))));
echo "\n";

$a = array (1, 2, array ("a", "b", "c"));
var_export($a);
echo "\n\n";

// With return
echo var_export($a, true);
echo "\n\n";

// More complex
$a = array(
    null => null,
    'O\'neil',
    'He said "bar" ...' => 'He said "bar" ...',
    'Yes \ No'          =>'Yes \ No O\'neil',
    'foo'               => null,
    );
var_export($a);
echo "\n\n";

// Classes
$var = new stdClass;
$var->foo = 'foo';
$var->bar = 'bar';
$var = array(array($var));
var_export($var);

?>
--EXPECT--
true
false
NULL
NULL
array (
  0 => 1,
  1 => 
  array (
    0 => 2,
    1 => 
    array (
      0 => 3,
      1 => 4,
    ),
    2 => 
    array (
      0 => 5,
      1 => 
      array (
        0 => 6,
        1 => 
        array (
          0 => 7,
        ),
      ),
    ),
  ),
)
array (
  0 => 1,
  1 => 2,
  2 => 
  array (
    0 => 'a',
    1 => 'b',
    2 => 'c',
  ),
)

array (
  0 => 1,
  1 => 2,
  2 => 
  array (
    0 => 'a',
    1 => 'b',
    2 => 'c',
  ),
)

array (
  '' => NULL,
  0 => 'O\'neil',
  'He said "bar" ...' => 'He said "bar" ...',
  'Yes \\ No' => 'Yes \\ No O\'neil',
  'foo' => NULL,
)

array (
  0 => 
  array (
    0 => 
    class stdClass {
      var $foo = 'foo';
      var $bar = 'bar';
    },
  ),
)