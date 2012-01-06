--TEST--
Method -- PHP_Compat::loadConstant
--FILE--
<?php
require_once ('PHP/Compat.php');

// Singular
echo (PHP_Compat::loadConstant('invalid') === false) ? 'false' : 'true', "\n";

// Multiple
$comp = array('an-invalid', 'also-invalid', 'more-invalid', 'E_STRICT');
$results = PHP_Compat::loadConstant($comp);

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
E_STRICT: true