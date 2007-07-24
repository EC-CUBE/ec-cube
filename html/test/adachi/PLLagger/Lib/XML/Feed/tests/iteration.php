<?php

require_once 'XML_Feed_Parser_TestCase.php';

class XML_Feed_Parser_Iteration_TestCase extends XML_Feed_Parser_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $this->sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
    }
    
    function setUp() {
    }
    
    function tearDown() {
    }
    
    function test_Atom() {
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $feed = new XML_Feed_Parser(file_get_contents($this->sample_dir . "/grwifi-atom.xml"));
        $entries = array();
        foreach ($feed as $entry) {
            array_push($entries, $entry);
        }
        $this->assertNotSame($entries[0], $entries[1]);
    }

    function test_RSS1() {
        $feed = new XML_Feed_Parser(file_get_contents($this->sample_dir . "/delicious.feed"));
        $entries = array();
        foreach ($feed as $entry) {
            array_push($entries, $entry);
        }
        $this->assertNotSame($entries[0], $entries[1]);
    }
    
    function test_RSS2() {
        $feed = new XML_Feed_Parser(file_get_contents($this->sample_dir . "/rss2sample.xml"));
        $entries = array();
        foreach ($feed as $entry) {
            array_push($entries, $entry);
        }
        $this->assertNotSame($entries[0], $entries[1]);
    }
}

$suite = new PHPUnit_TestSuite;
$suite->addTestSuite("XML_Feed_Parser_Iteration_TestCase");
$result = PHPUnit::run($suite, "123");
echo $result->toString();

?>