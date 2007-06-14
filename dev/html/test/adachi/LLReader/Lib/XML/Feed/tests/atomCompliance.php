<?php

require_once 'XML_Feed_Parser_TestCase.php';

/**
 * This test is to make sure that we get sane values back for all
 * elements specified by the atom specification. It is okay for a feed
 * to not have some of these, but if they're not present we should
 * get a null or false return rather than an error. This test begins
 * to ensure consistency of our API.
 */
class XML_Feed_Parser_AtomCompat1_TestCase extends XML_Feed_Parser_TestCase
{
    
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'atom10-example1.xml');
        $this->parser = new XML_Feed_Parser($this->file);
        $this->entry = $this->parser->getEntryByOffset(0);
    }
    
    function setUp() {
    }
    
    function tearDown() {
    }
    
    function checkString($attribute, $entry = false) {
        if ($entry) {
            $author = $this->entry->$attribute;            
        } else {
            $author = $this->parser->$attribute;
        }
        if (is_string($author) or $author === false) {
            $test = true;
        }
        return $test;
    }

    function checkNumeric($attribute, $entry = false) {
        if ($entry) {
            $author = $this->entry->$attribute;            
        } else {
            $author = $this->parser->$attribute;
        }
        if (is_numeric($author)) {
            $test = true;
        } else if ($author === false) {
            $test = true;
        }
        return $test;
    }

    function test_feedAuthor() {
        $this->assertTrue($this->checkString('author'));
    }

    function test_feedContributor()
    {
        $this->assertTrue($this->checkString('contributor'));
    }

    function test_feedIcon() {
        $this->assertTrue($this->checkString('icon'));
    }
    
    function test_feedId() {
        $this->assertTrue($this->checkString('id'));
    }
    
    function test_feedRights() {
        $this->assertTrue($this->checkString('rights'));
    }
    
    function test_feedTitle() {
        $this->assertTrue($this->checkString('title'));
    }
    
    function test_feedSubtitle() {
        $this->assertTrue($this->checkString('subtitle'));
    }
    
    function test_feedUpdated() {
        $this->assertTrue($this->checkNumeric('updated'));
    }
    
    function test_feedLink() {
        $this->assertTrue($this->checkString('link'));
    }
    
    function test_entryAuthor() {
        $this->assertTrue($this->checkString('author', true));
    }

    function test_entryContributor()
    {
        $this->assertTrue($this->checkString('contributor', true));
    }

    function test_entryId() {
        $this->assertTrue($this->checkString('id', true));
    }
    
    function test_entryPublished() {
        $this->assertTrue($this->checkNumeric('published', true));
    }
    
    function testEntryTitle() {
        $this->assertTrue($this->checkString('title', true));
    }
    
    function testEntryRights() {
        $this->assertTrue($this->checkString('rights', true));
    }
    
    function testEntrySummary() {
        $this->assertTrue($this->checkString('summary', true));
    }
    
    function testEntryContent() {
        $this->assertTrue($this->checkString('content', true));
    }
    
    function testEntryLink() {
        $this->assertTrue($this->checkString('link', true));
    }
}

class XML_Feed_Parser_AtomCompat2_TestCase extends XML_Feed_Parser_AtomCompat1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'atom10-example2.xml');
        $this->parser = new XML_Feed_Parser($this->file);
        $this->entry = $this->parser->getEntryByOffset(0);
    }
}

class XML_Feed_Parser_AtomCompat3_TestCase extends XML_Feed_Parser_AtomCompat1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'rss10-example1.xml');
        $this->parser = new XML_Feed_Parser($this->file);
        $this->entry = $this->parser->getEntryByOffset(0);
    }
}

class XML_Feed_Parser_AtomCompat4_TestCase extends XML_Feed_Parser_AtomCompat1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'rss10-example2.xml');
        $this->parser = new XML_Feed_Parser($this->file);
        $this->entry = $this->parser->getEntryByOffset(0);
    }
}

class XML_Feed_Parser_AtomCompat5_TestCase extends XML_Feed_Parser_AtomCompat1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'rss2sample.xml');
        $this->parser = new XML_Feed_Parser($this->file);
        $this->entry = $this->parser->getEntryByOffset(0);
    }
}

class XML_Feed_Parser_AtomCompat6_TestCase extends XML_Feed_Parser_AtomCompat1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'delicious.feed');
        $this->parser = new XML_Feed_Parser($this->file);
        $this->entry = $this->parser->getEntryByOffset(0);
    }
}

class XML_Feed_Parser_AtomCompat7_TestCase extends XML_Feed_Parser_AtomCompat1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'technorati.feed');
        $this->parser = new XML_Feed_Parser($this->file);
        $this->entry = $this->parser->getEntryByOffset(0);
    }
}

class XML_Feed_Parser_AtomCompat8_TestCase extends XML_Feed_Parser_AtomCompat1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'grwifi-atom.xml');
        $this->parser = new XML_Feed_Parser($this->file);
        $this->entry = $this->parser->getEntryByOffset(0);
    }
}

$suite = new PHPUnit_TestSuite;
$suite->addTestSuite('XML_Feed_Parser_AtomCompat1_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AtomCompat2_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AtomCompat3_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AtomCompat4_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AtomCompat5_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AtomCompat6_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AtomCompat7_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AtomCompat8_TestCase');
$result = PHPUnit::run($suite, '123');
echo $result->toString();
?>