<?php

require_once 'XML_Feed_Parser_TestCase.php';

class cdf_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_channel_abstract_map_description_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/cdf/channel_abstract_map_description.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->description);
    }

    function test_channel_abstract_map_tagline_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/cdf/channel_abstract_map_tagline.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->tagline);
    }

    function test_channel_href_map_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/cdf/channel_href_map_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://www.example.org/', $feed->link);
    }

    function test_channel_href_map_links_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/cdf/channel_href_map_links.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://www.example.org/', $feed->links(0, 'href'));
    }

    function test_channel_title_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/cdf/channel_title.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example feed', $feed->title);
    }

    function test_item_abstract_map_description_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/cdf/item_abstract_map_description.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_abstract_map_summary_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/cdf/item_abstract_map_summary.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->getEntryByOffset(0)->summary);
    }

    function test_item_href_map_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/cdf/item_href_map_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://www.example.org/', $feed->getEntryByOffset(0)->link);
    }

    function test_item_href_map_links_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/cdf/item_href_map_links.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://www.example.org/', $feed->getEntryByOffset(0)->links(0, 'href'));
    }

    function test_item_title_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/cdf/item_title.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example item', $feed->getEntryByOffset(0)->title);
    }
}

$suite = new PHPUnit_TestSuite('cdf_TestCase');
$result = PHPUnit::run($suite, '123');
echo $result->toString();

?>
