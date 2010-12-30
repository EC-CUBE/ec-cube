--TEST--
Method -- PHP_Compat::loadFunction
--FILE--
<?php
require_once ('PHP/Compat.php');

// Singular
echo (PHP_Compat::loadFunction('invalid') === false) ? 'false' : 'true', "\n";

// Multiple
$comp = array('an-invalid', 'also-invalid', 'more-invalid');
$results = PHP_Compat::loadFunction($comp);

foreach ($results as $comp => $result) {
    echo $comp . ': ';
	echo ($result === false) ? 'false' : 'true', "\n";
}

?>
--EXPECT--
false
an-invalid: false
also-invalid: false
more-invalid: false