<?php

require_once 'XML_Feed_Parser_TestCase.php';

class itunes_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_itunes_channel_block_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_block.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(1, $feed->itunes_block);
    }

    function test_itunes_channel_block_false_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_block_false.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->itunes_block);
    }

    function test_itunes_channel_block_no_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_block_no.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->itunes_block);
    }

    function test_itunes_channel_block_true_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_block_true.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->itunes_block);
    }

    function test_itunes_channel_block_uppercase_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_block_uppercase.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->itunes_block);
    }

    function test_itunes_channel_block_whitespace_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_block_whitespace.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->itunes_block);
    }

    function test_itunes_channel_category_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_category.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Technology', $feed->tags(0, 'term'));
    }

    function test_itunes_channel_category_nested_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_category_nested.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Gadgets', $feed->tags(0, 'term'));
    }

    function test_itunes_channel_category_scheme_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_category_scheme.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://www.itunes.com/', $feed->tags(0, 'scheme'));
    }

    function test_itunes_channel_explicit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_explicit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(1, $feed->itunes_explicit);
    }

    function test_itunes_channel_explicit_false_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_explicit_false.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->itunes_explicit);
    }

    function test_itunes_channel_explicit_no_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_explicit_no.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->itunes_explicit);
    }

    function test_itunes_channel_explicit_true_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_explicit_true.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->itunes_explicit);
    }

    function test_itunes_channel_explicit_uppercase_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_explicit_uppercase.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->itunes_explicit);
    }

    function test_itunes_channel_explicit_whitespace_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_explicit_whitespace.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->itunes_explicit);
    }

    function test_itunes_channel_image_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_image.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/logo.jpg', $feed->image(0, 'href'));
    }

    function test_itunes_channel_keywords_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_keywords.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Technology', $feed->tags(0, 'term'));
    }

    function test_itunes_channel_keywords_duplicate_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_keywords_duplicate.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(1, len(feed->tags));
    }

    function test_itunes_channel_keywords_duplicate_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_keywords_duplicate_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(1, len(feed->tags));
    }

    function test_itunes_channel_keywords_multiple_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_keywords_multiple.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Gadgets', $feed->tags(0, 'term'));
    }

    function test_itunes_channel_link_image_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_link_image.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/logo.jpg', $feed->image(0, 'href'));
    }

    function test_itunes_channel_owner_email_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_owner_email.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('mark@example.com', $feed->publisher(0, 'email'));
    }

    function test_itunes_channel_owner_name_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_owner_name.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Mark Pilgrim', $feed->publisher(0, 'name'));
    }

    function test_itunes_channel_subtitle_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_subtitle.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example subtitle', $feed->subtitle);
    }

    function test_itunes_channel_summary_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_summary.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example summary', $feed->description);
    }

    function test_itunes_core_element_uppercase_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_core_element_uppercase.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example title', $feed->title);
    }

    function test_itunes_enclosure_url_maps_id_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_enclosure_url_maps_id.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/movie.mp4', $feed->getEntryByOffset(0)->id);
    }

    function test_itunes_enclosure_url_maps_id_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_enclosure_url_maps_id_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/id', $feed->getEntryByOffset(0)->id);
    }

    function test_itunes_item_author_map_author_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_author_map_author.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Mark Pilgrim', $feed->getEntryByOffset(0)->author);
    }

    function test_itunes_item_block_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_block.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(1, $feed->getEntryByOffset(0)->itunes_block);
    }

    function test_itunes_item_block_false_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_block_false.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_block);
    }

    function test_itunes_item_block_no_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_block_no.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_block);
    }

    function test_itunes_item_block_true_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_block_true.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_block);
    }

    function test_itunes_item_block_uppercase_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_block_uppercase.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_block);
    }

    function test_itunes_item_block_whitespace_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_block_whitespace.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_block);
    }

    function test_itunes_item_category_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_category.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Technology', $feed->getEntryByOffset(0)->tags(0, 'term'));
    }

    function test_itunes_item_category_nested_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_category_nested.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Gadgets', $feed->getEntryByOffset(0)->tags(0, 'term'));
    }

    function test_itunes_item_category_scheme_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_category_scheme.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://www.itunes.com/', $feed->getEntryByOffset(0)->tags(0, 'scheme'));
    }

    function test_itunes_item_duration_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_duration.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('3:00', $feed->getEntryByOffset(0)->itunes_duration);
    }

    function test_itunes_item_explicit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_explicit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(1, $feed->getEntryByOffset(0)->itunes_explicit);
    }

    function test_itunes_item_explicit_false_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_explicit_false.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_explicit);
    }

    function test_itunes_item_explicit_no_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_explicit_no.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_explicit);
    }

    function test_itunes_item_explicit_true_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_explicit_true.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_explicit);
    }

    function test_itunes_item_explicit_uppercase_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_explicit_uppercase.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_explicit);
    }

    function test_itunes_item_explicit_whitespace_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_explicit_whitespace.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_explicit);
    }

    function test_itunes_item_image_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_image.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/logo.jpg', $feed->getEntryByOffset(0)->image(0, 'href'));
    }

    function test_itunes_item_link_image_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_link_image.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/logo.jpg', $feed->getEntryByOffset(0)->image(0, 'href'));
    }

    function test_itunes_item_subtitle_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_subtitle.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example subtitle', $feed->getEntryByOffset(0)->subtitle);
    }

    function test_itunes_item_summary_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_summary.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example summary', $feed->getEntryByOffset(0)->summary);
    }

    function test_itunes_link_enclosure_maps_id_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_link_enclosure_maps_id.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/movie.mp4', $feed->getEntryByOffset(0)->id);
    }

    function test_itunes_link_enclosure_maps_id_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_link_enclosure_maps_id_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/id', $feed->getEntryByOffset(0)->id);
    }

    function test_itunes_namespace_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_namespace.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(1, $feed->itunes_block);
    }

    function test_itunes_namespace_example_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_namespace_example.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(1, $feed->itunes_block);
    }

    function test_itunes_namespace_lowercase_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_namespace_lowercase.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(1, $feed->itunes_block);
    }

    function test_itunes_namespace_uppercase_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_namespace_uppercase.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(1, $feed->itunes_block);
    }
}

$suite = new PHPUnit_TestSuite('itunes_TestCase');
$result = PHPUnit::run($suite, '123');
echo $result->toString();

?>
