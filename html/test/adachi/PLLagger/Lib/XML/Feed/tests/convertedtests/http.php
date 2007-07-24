<?php

require_once 'XML_Feed_Parser_TestCase.php';

class http_TestCase extends XML_Feed_Parser_Converted_TestCase {
}

$suite = new PHPUnit_TestSuite('http_TestCase');
$result = PHPUnit::run($suite, '123');
echo $result->toString();

?>
