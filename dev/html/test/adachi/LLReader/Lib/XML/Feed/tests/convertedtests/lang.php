<?php

require_once 'XML_Feed_Parser_TestCase.php';

class lang_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_channel_dc_language_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/channel_dc_language.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->language);
    }

    function test_channel_language_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/channel_language.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en-us', $feed->language);
    }

    function test_entry_content_xml_lang_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_content_xml_lang_blank_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_blank.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(None, $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_content_xml_lang_blank_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_blank_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(None, $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_content_xml_lang_blank_3_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_blank_3.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(1)->content->language);
    }

    function test_entry_content_xml_lang_inherit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_inherit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_content_xml_lang_inherit_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_inherit_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_content_xml_lang_inherit_3_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_inherit_3.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_content_xml_lang_inherit_4_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_inherit_4.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_summary_xml_lang_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_summary_xml_lang.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->summary(0, 'language'));
    }

    function test_entry_summary_xml_lang_blank_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_summary_xml_lang_blank.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(None, $feed->getEntryByOffset(0)->summary(0, 'language'));
    }

    function test_entry_summary_xml_lang_inherit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_summary_xml_lang_inherit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->summary(0, 'language'));
    }

    function test_entry_summary_xml_lang_inherit_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_summary_xml_lang_inherit_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->summary(0, 'language'));
    }

    function test_entry_summary_xml_lang_inherit_3_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_summary_xml_lang_inherit_3.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->summary(0, 'language'));
    }

    function test_entry_summary_xml_lang_inherit_4_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_summary_xml_lang_inherit_4.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->summary(0, 'language'));
    }

    function test_entry_title_xml_lang_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_title_xml_lang.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_entry_title_xml_lang_blank_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_title_xml_lang_blank.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(None, $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_entry_title_xml_lang_inherit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_title_xml_lang_inherit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_entry_title_xml_lang_inherit_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_title_xml_lang_inherit_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_entry_title_xml_lang_inherit_3_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_title_xml_lang_inherit_3.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_entry_title_xml_lang_inherit_4_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_title_xml_lang_inherit_4.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_feed_copyright_xml_lang_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_copyright_xml_lang.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->copyright(0, 'language'));
    }

    function test_feed_copyright_xml_lang_blank_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_copyright_xml_lang_blank.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(None, $feed->copyright(0, 'language'));
    }

    function test_feed_copyright_xml_lang_inherit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_copyright_xml_lang_inherit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->copyright(0, 'language'));
    }

    function test_feed_copyright_xml_lang_inherit_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_copyright_xml_lang_inherit_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->copyright(0, 'language'));
    }

    function test_feed_copyright_xml_lang_inherit_3_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_copyright_xml_lang_inherit_3.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->copyright(0, 'language'));
    }

    function test_feed_copyright_xml_lang_inherit_4_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_copyright_xml_lang_inherit_4.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->copyright(0, 'language'));
    }

    function test_feed_info_xml_lang_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_info_xml_lang.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->info(0, 'language'));
    }

    function test_feed_info_xml_lang_blank_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_info_xml_lang_blank.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(None, $feed->info(0, 'language'));
    }

    function test_feed_info_xml_lang_inherit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_info_xml_lang_inherit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->info(0, 'language'));
    }

    function test_feed_info_xml_lang_inherit_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_info_xml_lang_inherit_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->info(0, 'language'));
    }

    function test_feed_info_xml_lang_inherit_3_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_info_xml_lang_inherit_3.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->info(0, 'language'));
    }

    function test_feed_info_xml_lang_inherit_4_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_info_xml_lang_inherit_4.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->info(0, 'language'));
    }

    function test_feed_language_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_language.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->language);
    }

    function test_feed_language_override_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_language_override.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->language);
    }

    function test_feed_not_xml_lang_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_not_xml_lang.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->language);
    }

    function test_feed_not_xml_lang_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_not_xml_lang_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(, ! $feed.has_key(->));
    }

    function test_feed_tagline_xml_lang_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_tagline_xml_lang.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->tagline(0, 'language'));
    }

    function test_feed_tagline_xml_lang_blank_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_tagline_xml_lang_blank.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(None, $feed->tagline(0, 'language'));
    }

    function test_feed_tagline_xml_lang_inherit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_tagline_xml_lang_inherit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->tagline(0, 'language'));
    }

    function test_feed_tagline_xml_lang_inherit_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_tagline_xml_lang_inherit_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('fr', $feed->tagline(0, 'language'));
    }

    function test_feed_tagline_xml_lang_inherit_3_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_tagline_xml_lang_inherit_3.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('de', $feed->tagline(0, 'language'));
    }

    function test_feed_tagline_xml_lang_inherit_4_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_tagline_xml_lang_inherit_4.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->tagline(0, 'language'));
    }

    function test_feed_title_xml_lang_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_title_xml_lang.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->title(0, 'language'));
    }

    function test_feed_title_xml_lang_blank_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_title_xml_lang_blank.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(None, $feed->title(0, 'language'));
    }

    function test_feed_title_xml_lang_inherit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_title_xml_lang_inherit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('de', $feed->title(0, 'language'));
    }

    function test_feed_title_xml_lang_inherit_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_title_xml_lang_inherit_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('de', $feed->title(0, 'language'));
    }

    function test_feed_title_xml_lang_inherit_3_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_title_xml_lang_inherit_3.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('de', $feed->title(0, 'language'));
    }

    function test_feed_title_xml_lang_inherit_4_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_title_xml_lang_inherit_4.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->title(0, 'language'));
    }

    function test_feed_xml_lang_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_xml_lang.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->language);
    }

    function test_http_content_language_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/http_content_language.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->language);
    }

    function test_http_content_language_entry_title_inherit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/http_content_language_entry_title_inherit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_http_content_language_entry_title_inherit_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/http_content_language_entry_title_inherit_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_http_content_language_feed_language_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/http_content_language_feed_language.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('fr', $feed->language);
    }

    function test_http_content_language_feed_xml_lang_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/http_content_language_feed_xml_lang.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('fr', $feed->language);
    }

    function test_item_content_encoded_xml_lang_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_content_encoded_xml_lang.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_item_content_encoded_xml_lang_inherit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_content_encoded_xml_lang_inherit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_item_dc_language_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_dc_language.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->language);
    }

    function test_item_fullitem_xml_lang_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_fullitem_xml_lang.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_item_fullitem_xml_lang_inherit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_fullitem_xml_lang_inherit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_item_xhtml_body_xml_lang_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_xhtml_body_xml_lang.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_item_xhtml_body_xml_lang_inherit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_xhtml_body_xml_lang_inherit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }
}

$suite = new PHPUnit_TestSuite('lang_TestCase');
$result = PHPUnit::run($suite, '123');
echo $result->toString();

?>
