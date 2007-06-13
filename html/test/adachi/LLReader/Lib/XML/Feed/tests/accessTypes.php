<?php

require_once 'XML_Feed_Parser_TestCase.php';

/**
 * This test is to make sure that we get sane values back for all
 * elements specified by the atom specification. It is okay for a feed
 * to not have some of these, but if they're not present we should
 * get a null or false return rather than an error. This test begins
 * to ensure consistency of our API.
 */
class XML_Feed_Parser_AccessTypes1_TestCase extends XML_Feed_Parser_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'atom10-example1.xml');
        $this->feed = new XML_Feed_Parser($this->file);
        $this->entry = $this->feed->getEntryByOffset(0);
    }
    
    function setUp() {
    }
    
    function tearDown() {
    }

    function test_feedAuthor() {
        $this->assertEquals($this->feed->author, $this->feed->author());
    }
    
    function test_feedIcon() {
        $this->assertEquals($this->feed->icon, $this->feed->icon());
    }
    
    function test_feedId() {
        $this->assertEquals($this->feed->id, $this->feed->id());
    }
    
    function test_feedRights() {
        $this->assertEquals($this->feed->rights, $this->feed->rights());
    }
    
    function test_feedTitle() {
        $this->assertEquals($this->feed->title, $this->feed->title());
    }
    
    function test_feedSubtitle() {
        $this->assertEquals($this->feed->subtitle, $this->feed->subtitle());
    }
    
    function test_feedUpdated() {
        $this->assertEquals($this->feed->updated, $this->feed->updated());
    }
    
    function test_feedLink() {
        $this->assertEquals($this->feed->link, $this->feed->link());
    }
    
    function test_entryAuthor() {
        $this->assertEquals($this->entry->author, $this->entry->author());
    }
    
    function test_entryId() {
        $this->assertEquals($this->entry->id, $this->entry->id());
    }
    
    function test_entryPublished() {
        $this->assertEquals($this->entry->published, $this->entry->published());
    }
    
    function testEntryTitle() {
        $this->assertEquals($this->entry->title, $this->entry->title());
    }
    
    function testEntryRights() {
        $this->assertEquals($this->entry->rights, $this->entry->rights());
    }
    
    function testEntrySummary() {
        $this->assertEquals($this->entry->summary, $this->entry->summary());
    }
    
    function testEntryContent() {
        $this->assertEquals($this->entry->content, $this->entry->content());
    }
    
    function testEntryLink() {
        $this->assertEquals($this->entry->link, $this->entry->link());
    }
}

class XML_Feed_Parser_AccessTypes2_TestCase extends XML_Feed_Parser_AccessTypes1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'atom10-example2.xml');
        $this->feed = new XML_Feed_Parser($this->file);
        $this->entry = $this->feed->getEntryByOffset(0);
    }
}

class XML_Feed_Parser_AccessTypes3_TestCase extends XML_Feed_Parser_AccessTypes1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'rss10-example1.xml');
        $this->feed = new XML_Feed_Parser($this->file);
        $this->entry = $this->feed->getEntryByOffset(0);
    }
}

class XML_Feed_Parser_AccessTypes4_TestCase extends XML_Feed_Parser_AccessTypes1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'rss10-example2.xml');
        $this->feed = new XML_Feed_Parser($this->file);
        $this->entry = $this->feed->getEntryByOffset(0);
    }
}

class XML_Feed_Parser_AccessTypes5_TestCase extends XML_Feed_Parser_AccessTypes1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'rss2sample.xml');
        $this->feed = new XML_Feed_Parser($this->file);
        $this->entry = $this->feed->getEntryByOffset(0);
    }
}

class XML_Feed_Parser_AccessTypes6_TestCase extends XML_Feed_Parser_AccessTypes1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'delicious.feed');
        $this->feed = new XML_Feed_Parser($this->file);
        $this->entry = $this->feed->getEntryByOffset(0);
    }
}

class XML_Feed_Parser_AccessTypes7_TestCase extends XML_Feed_Parser_AccessTypes1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'technorati.feed');
        $this->feed = new XML_Feed_Parser($this->file);
        $this->entry = $this->feed->getEntryByOffset(0);
    }
}

class XML_Feed_Parser_AccessTypes8_TestCase extends XML_Feed_Parser_AccessTypes1_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'grwifi-atom.xml');
        $this->feed = new XML_Feed_Parser($this->file);
        $this->entry = $this->feed->getEntryByOffset(0);
    }
}

$suite = new PHPUnit_TestSuite;
$suite->addTestSuite('XML_Feed_Parser_AccessTypes1_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AccessTypes2_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AccessTypes3_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AccessTypes4_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AccessTypes5_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AccessTypes6_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AccessTypes7_TestCase');
$suite->addTestSuite('XML_Feed_Parser_AccessTypes8_TestCase');
$result = PHPUnit::run($suite, '123');
echo $result->toString();
?>