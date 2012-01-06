--TEST--
Function -- http_build_query
--SKIPIF--
<?php if (function_exists('http_build_query')) { echo 'skip'; } ?>
--INI--
arg_separator.output=QQQ
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('http_build_query');

// Simple
$data = array('foo'=>'bar',
             'baz'=>'boom',
             'cow'=>'milk',
             'php'=>'hypertext processor');

echo http_build_query($data), "\n";


// With an object
class myClass {
    var $foo;
    var $baz;

    function myClass()
    {
        $this->foo = 'bar';
        $this->baz = 'boom';
    }
}

$data = new myClass();
echo http_build_query($data), "\n";


// With numerically indexed elements
$data = array('foo', 'bar', 'baz', 'boom', 'cow' => 'milk', 'php' =>'hypertext processor');
echo http_build_query($data), "\n";
echo http_build_query($data, 'myvar_'), "\n";


// With a complex array
$data = array('user' => array(
                    'name' => 'Bob Smith',
                    'age' => 47,
                    'sex' => 'M',
                    'dob' => '5/12/1956'),
             'pastimes' => array(
                    'golf',
                    'opera',
                    'poker',
                    'rap'),
             'children' => array(
                    'bobby' => array(
                        'age' => 12,
                        'sex' => 'M'),
                     'sally' => array(
                        'age' => 8,
                        'sex'=>'F')),
             'CEO');

echo http_build_query($data, 'flags_');
?>
--EXPECT--
foo=barQQQbaz=boomQQQcow=milkQQQphp=hypertext+processor
foo=barQQQbaz=boom
0=fooQQQ1=barQQQ2=bazQQQ3=boomQQQcow=milkQQQphp=hypertext+processor
myvar_0=fooQQQmyvar_1=barQQQmyvar_2=bazQQQmyvar_3=boomQQQcow=milkQQQphp=hypertext+processor
user[name]=Bob+SmithQQQuser[age]=47QQQuser[sex]=MQQQuser[dob]=5%2F12%2F1956QQQpastimes[0]=golfQQQpastimes[1]=operaQQQpastimes[2]=pokerQQQpastimes[3]=rapQQQchildren[bobby][age]=12QQQchildren[bobby][sex]=MQQQchildren[sally][age]=8QQQchildren[sally][sex]=FQQQflags_0=CEO