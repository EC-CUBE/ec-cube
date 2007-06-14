<?php

require_once 'XML_Feed_Parser_TestCase.php';

class rdf_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_rdf_channel_description_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_channel_description.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->description);
    }

    function test_rdf_channel_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_channel_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/', $feed->link);
    }

    function test_rdf_channel_title_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_channel_title.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example feed', $feed->title);
    }

    function test_rdf_item_description_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_item_description.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->getEntryByOffset(0)->description);
    }

    function test_rdf_item_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_item_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/1', $feed->getEntryByOffset(0)->link);
    }

    function test_rdf_item_rdf_about_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_item_rdf_about.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.org/1', $feed->getEntryByOffset(0)->id);
    }

    function test_rdf_item_title_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_item_title.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example title', $feed->getEntryByOffset(0)->title);
    }

    function test_rss090_channel_title_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rss090_channel_title.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example title', $feed->title);
    }

    function test_rss090_item_title_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rss090_item_title.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Item title', $feed->getEntryByOffset(0)->title);
    }

    function test_rss_version_10_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rss_version_10.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('rss10', $feed->version());
    }

    function test_rss_version_10_not_default_ns_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rss_version_10_not_default_ns.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('rss10', $feed->version());
    }
}

$suite = new PHPUnit_TestSuite('rdf_TestCase');
$result = PHPUnit::run($suite, '123');
echo $result->toString();

?>
