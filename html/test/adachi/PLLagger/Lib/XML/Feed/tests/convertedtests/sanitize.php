<?php

require_once 'XML_Feed_Parser_TestCase.php';

class sanitize_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_entry_content_applet_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_applet.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe <b>description</b>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_blink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_blink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_crazy_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_crazy.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Crazy HTML -' + '- Can Your Regex Parse This?\n\n\n\n<!-' + '- <script> -' + '->\n\n<!-' + '- \n\t<script> \n-' + '->\n\n\n\nfunction executeMe()\n{\n\n\n\n\n/* \n<h1>Did The Javascript Execute?</h1>\n<div>\nI will execute here, too, if you mouse over me\n</div>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_embed_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_embed.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe <b>description</b>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_frame_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_frame.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe <b>description</b>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_iframe_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_iframe.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe <b>description</b>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe <b>description</b>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_meta_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_meta.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe <b>description</b>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_object_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_object.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe <b>description</b>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onabort_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onabort.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onblur_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onblur.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onchange_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onchange.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_ondblclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_ondblclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onerror_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onerror.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onfocus_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onfocus.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onkeydown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onkeydown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onkeypress_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onkeypress.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onkeyup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onkeyup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onmousedown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onmousedown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onmouseout_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onmouseout.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onmouseover_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onmouseover.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onmouseup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onmouseup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onreset_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onreset.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onresize_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onresize.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onsubmit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onsubmit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_onunload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_onunload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_script_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_script.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_script_base64_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_script_base64.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_script_cdata_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_script_cdata.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_script_inline_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_script_inline.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div>safe description</div>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_style_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_content_style.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<a href="http://www.ragingplatypus.com/">never trust your upstream platypus</a>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_summary_applet_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_applet.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_blink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_blink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_crazy_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_crazy.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Crazy HTML -' + '- Can Your Regex Parse This?\n\n\n\n<!-' + '- <script> -' + '->\n\n<!-' + '- \n\t<script> \n-' + '->\n\n\n\nfunction executeMe()\n{\n\n\n\n\n/* \n<h1>Did The Javascript Execute?</h1>\n<div>\nI will execute here, too, if you mouse over me\n</div>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_embed_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_embed.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_frame_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_frame.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_iframe_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_iframe.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_meta_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_meta.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_object_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_object.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onabort_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onabort.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onblur_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onblur.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onchange_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onchange.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_ondblclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_ondblclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onerror_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onerror.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onfocus_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onfocus.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onkeydown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onkeydown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onkeypress_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onkeypress.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onkeyup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onkeyup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onmousedown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onmousedown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onmouseout_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onmouseout.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onmouseover_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onmouseover.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onmouseup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onmouseup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onreset_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onreset.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onresize_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onresize.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onsubmit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onsubmit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_onunload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_onunload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_script_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_script.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_script_base64_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_script_base64.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_script_cdata_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_script_cdata.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_script_inline_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_script_inline.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div>safe description</div>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_script_map_description_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_script_map_description.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_entry_summary_style_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_summary_style.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<a href="http://www.ragingplatypus.com/">never trust your upstream platypus</a>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_title_applet_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_applet.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_blink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_blink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_crazy_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_crazy.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Crazy HTML -' + '- Can Your Regex Parse This?\n\n\n\n<!-' + '- <script> -' + '->\n\n<!-' + '- \n\t<script> \n-' + '->\n\n\n\nfunction executeMe()\n{\n\n\n\n\n/* \n<h1>Did The Javascript Execute?</h1>\n<div>\nI will execute here, too, if you mouse over me\n</div>', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_embed_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_embed.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_frame_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_frame.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_iframe_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_iframe.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_meta_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_meta.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_object_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_object.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onabort_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onabort.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onblur_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onblur.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onchange_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onchange.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_ondblclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_ondblclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onerror_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onerror.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onfocus_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onfocus.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onkeydown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onkeydown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onkeypress_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onkeypress.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onkeyup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onkeyup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onmousedown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onmousedown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onmouseout_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onmouseout.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onmouseover_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onmouseover.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onmouseup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onmouseup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onreset_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onreset.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onresize_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onresize.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onsubmit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onsubmit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_onunload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_onunload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_script_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_script.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_script_cdata_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_script_cdata.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_script_inline_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_script_inline.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div>safe description</div>', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_style_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/entry_title_style.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<a href="http://www.ragingplatypus.com/">never trust your upstream platypus</a>', $feed->getEntryByOffset(0)->title);
    }

    function test_feed_copyright_applet_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_applet.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->copyright);
    }

    function test_feed_copyright_blink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_blink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->copyright);
    }

    function test_feed_copyright_crazy_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_crazy.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Crazy HTML -' + '- Can Your Regex Parse This?\n\n\n\n<!-' + '- <script> -' + '->\n\n<!-' + '- \n\t<script> \n-' + '->\n\n\n\nfunction executeMe()\n{\n\n\n\n\n/* \n<h1>Did The Javascript Execute?</h1>\n<div>\nI will execute here, too, if you mouse over me\n</div>', $feed->copyright);
    }

    function test_feed_copyright_embed_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_embed.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->copyright);
    }

    function test_feed_copyright_frame_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_frame.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->copyright);
    }

    function test_feed_copyright_iframe_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_iframe.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->copyright);
    }

    function test_feed_copyright_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->copyright);
    }

    function test_feed_copyright_meta_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_meta.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->copyright);
    }

    function test_feed_copyright_object_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_object.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->copyright);
    }

    function test_feed_copyright_onabort_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onabort.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onblur_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onblur.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onchange_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onchange.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_ondblclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_ondblclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onerror_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onerror.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onfocus_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onfocus.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onkeydown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onkeydown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onkeypress_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onkeypress.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onkeyup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onkeyup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onmousedown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onmousedown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onmouseout_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onmouseout.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onmouseover_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onmouseover.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onmouseup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onmouseup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onreset_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onreset.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onresize_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onresize.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onsubmit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onsubmit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_onunload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_onunload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->copyright);
    }

    function test_feed_copyright_script_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_script.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->copyright);
    }

    function test_feed_copyright_script_cdata_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_script_cdata.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->copyright);
    }

    function test_feed_copyright_script_inline_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_script_inline.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div>safe description</div>', $feed->copyright);
    }

    function test_feed_copyright_style_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_copyright_style.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<a href="http://www.ragingplatypus.com/">never trust your upstream platypus</a>', $feed->copyright);
    }

    function test_feed_info_applet_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_applet.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->info);
    }

    function test_feed_info_blink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_blink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->info);
    }

    function test_feed_info_crazy_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_crazy.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Crazy HTML -' + '- Can Your Regex Parse This?\n\n\n\n<!-' + '- <script> -' + '->\n\n<!-' + '- \n\t<script> \n-' + '->\n\n\n\nfunction executeMe()\n{\n\n\n\n\n/* \n<h1>Did The Javascript Execute?</h1>\n<div>\nI will execute here, too, if you mouse over me\n</div>', $feed->info);
    }

    function test_feed_info_embed_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_embed.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->info);
    }

    function test_feed_info_frame_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_frame.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->info);
    }

    function test_feed_info_iframe_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_iframe.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->info);
    }

    function test_feed_info_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->info);
    }

    function test_feed_info_meta_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_meta.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->info);
    }

    function test_feed_info_object_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_object.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->info);
    }

    function test_feed_info_onabort_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onabort.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onblur_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onblur.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onchange_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onchange.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_ondblclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_ondblclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onerror_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onerror.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onfocus_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onfocus.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onkeydown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onkeydown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onkeypress_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onkeypress.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onkeyup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onkeyup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onmousedown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onmousedown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onmouseout_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onmouseout.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onmouseover_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onmouseover.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onmouseup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onmouseup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onreset_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onreset.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onresize_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onresize.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onsubmit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onsubmit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_onunload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_onunload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->info);
    }

    function test_feed_info_script_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_script.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->info);
    }

    function test_feed_info_script_cdata_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_script_cdata.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->info);
    }

    function test_feed_info_script_inline_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_script_inline.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div>safe description</div>', $feed->info);
    }

    function test_feed_info_style_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_info_style.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<a href="http://www.ragingplatypus.com/">never trust your upstream platypus</a>', $feed->info);
    }

    function test_feed_subtitle_applet_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_applet.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_subtitle_blink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_blink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_subtitle_crazy_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_crazy.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Crazy HTML -' + '- Can Your Regex Parse This?\n\n\n\n<!-' + '- <script> -' + '->\n\n<!-' + '- \n\t<script> \n-' + '->\n\n\n\nfunction executeMe()\n{\n\n\n\n\n/* \n<h1>Did The Javascript Execute?</h1>\n<div>\nI will execute here, too, if you mouse over me\n</div>', $feed->tagline);
    }

    function test_feed_subtitle_embed_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_embed.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_subtitle_frame_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_frame.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_subtitle_iframe_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_iframe.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_subtitle_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_subtitle_meta_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_meta.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_subtitle_object_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_object.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_subtitle_onabort_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onabort.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onblur_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onblur.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onchange_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onchange.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_ondblclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_ondblclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onerror_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onerror.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onfocus_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onfocus.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onkeydown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onkeydown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onkeypress_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onkeypress.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onkeyup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onkeyup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onmousedown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onmousedown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onmouseout_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onmouseout.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onmouseover_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onmouseover.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onmouseup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onmouseup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onreset_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onreset.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onresize_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onresize.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onsubmit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onsubmit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_onunload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_onunload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_subtitle_script_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_script.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_subtitle_script_cdata_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_script_cdata.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_subtitle_script_inline_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_script_inline.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div>safe description</div>', $feed->tagline);
    }

    function test_feed_subtitle_style_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_subtitle_style.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<a href="http://www.ragingplatypus.com/">never trust your upstream platypus</a>', $feed->tagline);
    }

    function test_feed_tagline_applet_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_applet.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_tagline_blink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_blink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_tagline_crazy_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_crazy.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Crazy HTML -' + '- Can Your Regex Parse This?\n\n\n\n<!-' + '- <script> -' + '->\n\n<!-' + '- \n\t<script> \n-' + '->\n\n\n\nfunction executeMe()\n{\n\n\n\n\n/* \n<h1>Did The Javascript Execute?</h1>\n<div>\nI will execute here, too, if you mouse over me\n</div>', $feed->tagline);
    }

    function test_feed_tagline_embed_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_embed.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_tagline_frame_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_frame.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_tagline_iframe_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_iframe.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_tagline_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_tagline_meta_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_meta.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_tagline_object_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_object.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_tagline_onabort_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onabort.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onblur_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onblur.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onchange_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onchange.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_ondblclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_ondblclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onerror_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onerror.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onfocus_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onfocus.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onkeydown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onkeydown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onkeypress_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onkeypress.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onkeyup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onkeyup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onmousedown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onmousedown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onmouseout_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onmouseout.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onmouseover_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onmouseover.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onmouseup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onmouseup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onreset_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onreset.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onresize_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onresize.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onsubmit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onsubmit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_onunload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_onunload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->tagline);
    }

    function test_feed_tagline_script_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_script.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_tagline_script_cdata_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_script_cdata.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->tagline);
    }

    function test_feed_tagline_script_inline_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_script_inline.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div>safe description</div>', $feed->tagline);
    }

    function test_feed_tagline_script_map_description_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_script_map_description.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->description);
    }

    function test_feed_tagline_style_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_tagline_style.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<a href="http://www.ragingplatypus.com/">never trust your upstream platypus</a>', $feed->tagline);
    }

    function test_feed_title_applet_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_applet.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->title);
    }

    function test_feed_title_blink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_blink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->title);
    }

    function test_feed_title_crazy_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_crazy.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Crazy HTML -' + '- Can Your Regex Parse This?\n\n\n\n<!-' + '- <script> -' + '->\n\n<!-' + '- \n\t<script> \n-' + '->\n\n\n\nfunction executeMe()\n{\n\n\n\n\n/* \n<h1>Did The Javascript Execute?</h1>\n<div>\nI will execute here, too, if you mouse over me\n</div>', $feed->title);
    }

    function test_feed_title_embed_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_embed.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->title);
    }

    function test_feed_title_frame_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_frame.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->title);
    }

    function test_feed_title_iframe_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_iframe.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->title);
    }

    function test_feed_title_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->title);
    }

    function test_feed_title_meta_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_meta.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->title);
    }

    function test_feed_title_object_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_object.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->title);
    }

    function test_feed_title_onabort_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onabort.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onblur_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onblur.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onchange_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onchange.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_ondblclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_ondblclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onerror_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onerror.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onfocus_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onfocus.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onkeydown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onkeydown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onkeypress_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onkeypress.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onkeyup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onkeyup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onmousedown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onmousedown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onmouseout_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onmouseout.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onmouseover_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onmouseover.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onmouseup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onmouseup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onreset_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onreset.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onresize_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onresize.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onsubmit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onsubmit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_onunload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_onunload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->title);
    }

    function test_feed_title_script_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_script.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->title);
    }

    function test_feed_title_script_cdata_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_script_cdata.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->title);
    }

    function test_feed_title_script_inline_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_script_inline.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div>safe description</div>', $feed->title);
    }

    function test_feed_title_style_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/feed_title_style.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<a href="http://www.ragingplatypus.com/">never trust your upstream platypus</a>', $feed->title);
    }

    function test_item_body_applet_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_applet.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_blink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_blink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_embed_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_embed.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_frame_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_frame.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_iframe_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_iframe.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_meta_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_meta.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_object_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_object.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onabort_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onabort.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onblur_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onblur.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onchange_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onchange.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_ondblclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_ondblclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onerror_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onerror.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onfocus_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onfocus.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onkeydown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onkeydown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onkeypress_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onkeypress.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onkeyup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onkeyup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onmousedown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onmousedown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onmouseout_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onmouseout.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onmouseover_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onmouseover.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onmouseup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onmouseup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onreset_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onreset.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onresize_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onresize.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onsubmit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onsubmit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_onunload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_onunload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_script_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_script.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_body_script_map_content_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_script_map_content.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_body_style_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_body_style.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<a href="http://www.ragingplatypus.com/">never trust your upstream platypus</a>', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_applet_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_applet.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_blink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_blink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_crazy_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_crazy.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Crazy HTML -' + '- Can Your Regex Parse This?\n\n\n\n<!-' + '- <script> -' + '->\n\n<!-' + '- \n\t<script> \n-' + '->\n\n\n\nfunction executeMe()\n{\n\n\n\n\n/* \n<h1>Did The Javascript Execute?</h1>\n<div>\nI will execute here, too, if you mouse over me\n</div>', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_embed_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_embed.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_frame_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_frame.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_iframe_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_iframe.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_map_content_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_map_content.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_content_encoded_meta_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_meta.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_object_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_object.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onabort_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onabort.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onblur_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onblur.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onchange_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onchange.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_ondblclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_ondblclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onerror_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onerror.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onfocus_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onfocus.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onkeydown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onkeydown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onkeypress_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onkeypress.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onkeyup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onkeyup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onmousedown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onmousedown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onmouseout_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onmouseout.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onmouseover_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onmouseover.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onmouseup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onmouseup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onreset_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onreset.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onresize_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onresize.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onsubmit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onsubmit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_onunload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_onunload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_script_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_script.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_script_cdata_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_script_cdata.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_content_encoded_script_map_content_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_script_map_content.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_content_encoded_style_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_content_encoded_style.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<a href="http://www.ragingplatypus.com/">never trust your upstream platypus</a>', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_applet_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_applet.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_blink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_blink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_crazy_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_crazy.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Crazy HTML -' + '- Can Your Regex Parse This?\n\n\n\n<!-' + '- <script> -' + '->\n\n<!-' + '- \n\t<script> \n-' + '->\n\n\n\nfunction executeMe()\n{\n\n\n\n\n/* \n<h1>Did The Javascript Execute?</h1>\n<div>\nI will execute here, too, if you mouse over me\n</div>', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_embed_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_embed.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_frame_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_frame.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_iframe_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_iframe.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_meta_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_meta.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_object_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_object.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onabort_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onabort.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onblur_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onblur.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onchange_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onchange.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_ondblclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_ondblclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onerror_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onerror.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onfocus_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onfocus.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onkeydown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onkeydown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onkeypress_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onkeypress.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onkeyup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onkeyup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onmousedown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onmousedown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onmouseout_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onmouseout.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onmouseover_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onmouseover.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onmouseup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onmouseup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onreset_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onreset.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onresize_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onresize.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onsubmit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onsubmit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_onunload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_onunload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_script_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_script.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_script_cdata_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_script_cdata.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_script_map_summary_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_script_map_summary.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->summary);
    }

    function test_item_description_style_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_description_style.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<a href="http://www.ragingplatypus.com/">never trust your upstream platypus</a>', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_applet_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_applet.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_blink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_blink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_crazy_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_crazy.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Crazy HTML -' + '- Can Your Regex Parse This?\n\n\n\n<!-' + '- <script> -' + '->\n\n<!-' + '- \n\t<script> \n-' + '->\n\n\n\nfunction executeMe()\n{\n\n\n\n\n/* \n<h1>Did The Javascript Execute?</h1>\n<div>\nI will execute here, too, if you mouse over me\n</div>', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_embed_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_embed.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_frame_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_frame.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_iframe_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_iframe.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_meta_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_meta.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_object_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_object.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onabort_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onabort.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onblur_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onblur.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onchange_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onchange.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_ondblclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_ondblclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onerror_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onerror.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onfocus_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onfocus.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onkeydown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onkeydown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onkeypress_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onkeypress.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onkeyup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onkeyup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onmousedown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onmousedown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onmouseout_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onmouseout.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onmouseover_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onmouseover.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onmouseup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onmouseup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onreset_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onreset.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onresize_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onresize.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onsubmit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onsubmit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_onunload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_onunload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_script_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_script.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_script_cdata_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_script_cdata.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_fullitem_script_map_summary_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_script_map_summary.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_fullitem_style_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_fullitem_style.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<a href="http://www.ragingplatypus.com/">never trust your upstream platypus</a>', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_applet_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_applet.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_blink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_blink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_embed_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_embed.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_frame_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_frame.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_iframe_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_iframe.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_meta_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_meta.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_object_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_object.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onabort_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onabort.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onblur_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onblur.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onchange_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onchange.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_ondblclick_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_ondblclick.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onerror_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onerror.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onfocus_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onfocus.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onkeydown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onkeydown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onkeypress_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onkeypress.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onkeyup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onkeyup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onmousedown_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onmousedown.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onmouseout_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onmouseout.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onmouseover_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onmouseover.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onmouseup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onmouseup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onreset_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onreset.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onresize_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onresize.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onsubmit_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onsubmit.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_onunload_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_onunload.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<img src="http://www.ragingplatypus.com/i/cam-full.jpg" />', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_script_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_script.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_xhtml_body_script_map_content_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_script_map_content.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('safe description', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_xhtml_body_style_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/sanitize/item_xhtml_body_style.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<a href="http://www.ragingplatypus.com/">never trust your upstream platypus</a>', $feed->getEntryByOffset(0)->description);
    }
}

$suite = new PHPUnit_TestSuite('sanitize_TestCase');
$result = PHPUnit::run($suite, '123');
echo $result->toString();

?>
