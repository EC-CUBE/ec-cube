<?php

require_once 'XML_Feed_Parser_TestCase.php';

class XML_Feed_Parser_RSS2_valueValidity_TestCase extends XML_Feed_Parser_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . "rss2sample.xml");
        $this->feed = new XML_Feed_Parser($this->file);
        $this->entry = $this->feed->getEntryByOffset(2);
    }

    function test_feedNumberItems()
    {
        $value = 4;
        $this->assertEquals($value, $this->feed->numberEntries);
    }
    function test_feedTitle()
    {
        $value = "Liftoff News";
        $this->assertEquals($value, $this->feed->title);
    }
    
    function test_feedLink()
    {
        $value = "http://liftoff.msfc.nasa.gov/";
        $this->assertEquals($value, $this->feed->link);
    }
    
    function test_feedDescription()
    {
        $value = "Liftoff to Space Exploration.";
        $this->assertEquals($value, $this->feed->description);
    }
    
    function test_feedSubtitleEquivalence()
    {
        $value = "Liftoff to Space Exploration.";
        $this->assertEquals($value, $this->feed->subtitle);
    }
    
    function test_feedDate()
    {
        $value = strtotime("Tue, 10 Jun 2003 04:00:00 GMT");
        $this->assertEquals($value, $this->feed->date);
    }
    
    function test_feedLastBuildDate()
    {
        $value = strtotime("Tue, 10 Jun 2003 09:41:01 GMT");
        $this->assertEquals($value, $this->feed->lastBuildDate);
    }
    
    function test_feedUpdatedEquivalence()
    {
        $value = strtotime("Tue, 10 Jun 2003 09:41:01 GMT");
        $this->assertEquals($value, $this->feed->updated);
    }
    
    function test_feedGenerator()
    {
        $value = 'Weblog Editor 2.0';
        $this->assertEquals($value, $this->feed->generator);
    }
    
    function test_feedLanguage()
    {
        $value = "en-us";
        $this->assertEquals($value, $this->feed->language);
    }
    
    function test_feedDocs()
    {
        $value = "http://blogs.law.harvard.edu/tech/rss";
        $this->assertEquals($value, $this->feed->docs);
    }
    
    function test_feedManagingEditor()
    {
        $value = "editor@example.com";
        $this->assertEquals($value, $this->feed->managingEditor);
    }
    
    function test_feedAuthorEquivalence()
    {
        $value = "editor@example.com";
        $this->assertEquals($value, $this->feed->author);
    }
    
    function test_feedWebmaster()
    {
        $value = "webmaster@example.com";
        $this->assertEquals($value, $this->feed->webMaster);
    }
    
    function test_entryTitle()
    {
        $value = "The Engine That Does More";
        $this->assertEquals($value, $this->entry->title);
    }
    
    function test_entryLink()
    {
        $value = "http://liftoff.msfc.nasa.gov/news/2003/news-VASIMR.asp";
        $this->assertEquals($value, $this->entry->link);
    }
    
    function test_entryDescription()
    {
        $value = "Before man travels to Mars, NASA hopes to design new engines that will let us fly through the Solar System more quickly.  The proposed VASIMR engine would do that.";
        $this->assertEquals($value, $this->entry->description);
    }
    
    function test_entryPubDate()
    {
        $value = strtotime("Tue, 27 May 2003 08:37:32 GMT");
        $this->assertEquals($value, $this->entry->pubDate);
    }
    
    function test_entryGuid()
    {
        $value = "http://liftoff.msfc.nasa.gov/2003/05/27.html#item571";
        $this->assertEquals($value, $this->entry->guid);
    }
    
    function test_entryIdEquivalence()
    {
        $value = "http://liftoff.msfc.nasa.gov/2003/05/27.html#item571";
        $this->assertEquals($value, $this->entry->id);   
    }

	function test_entryContent()
	{
		$value = "<p>Test content</p>";
		$this->assertEquals($value, $this->entry->content);
	}
}

$suite = new PHPUnit_TestSuite;
$suite->addTestSuite("XML_Feed_Parser_RSS2_valueValidity_TestCase");
$result = PHPUnit::run($suite, "123");
echo $result->toString();

?>