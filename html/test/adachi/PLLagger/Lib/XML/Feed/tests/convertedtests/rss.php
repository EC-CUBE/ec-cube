<?php

require_once 'XML_Feed_Parser_TestCase.php';

class rss_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_channel_author_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_author.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor (me@example.com)', $feed->author);
    }

    function test_channel_author_map_author_detail_email_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_author_map_author_detail_email.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('me@example.com', $feed->author(0, 'email'));
    }

    function test_channel_author_map_author_detail_email_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_author_map_author_detail_email_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('me+spam@example.com', $feed->author(0, 'email'));
    }

    function test_channel_author_map_author_detail_email_3_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_author_map_author_detail_email_3.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('me@example.com', $feed->author(0, 'email'));
    }

    function test_channel_author_map_author_detail_name_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_author_map_author_detail_name.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->author(0, 'name'));
    }

    function test_channel_author_map_author_detail_name_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_author_map_author_detail_name_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->author(0, 'name'));
    }

    function test_channel_category_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_category.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example category', $feed->category);
    }

    function test_channel_category_domain_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_category_domain.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://www.example.com/', $feed->categories[0][0]);
    }

    function test_channel_category_multiple_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_category_multiple.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://www.example.com/2', $feed->categories[1][0]);
    }

    function test_channel_category_multiple_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_category_multiple_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example category 2', $feed->categories[1][1]);
    }

    function test_channel_cloud_domain_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_cloud_domain.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('rpc.sys.com', $feed->cloud(0, 'domain'));
    }

    function test_channel_cloud_path_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_cloud_path.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('/RPC2', $feed->cloud(0, 'path'));
    }

    function test_channel_cloud_port_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_cloud_port.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('80', $feed->cloud(0, 'port'));
    }

    function test_channel_cloud_protocol_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_cloud_protocol.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('xml-rpc', $feed->cloud(0, 'protocol'));
    }

    function test_channel_cloud_registerProcedure_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_cloud_registerProcedure.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('myCloud.rssPleaseNotify', $feed->cloud(0, 'registerprocedure'));
    }

    function test_channel_copyright_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_copyright.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example copyright', $feed->copyright);
    }

    function test_channel_dc_author_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_author.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->author);
    }

    function test_channel_dc_author_map_author_detail_email_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_author_map_author_detail_email.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('me@example.com', $feed->author(0, 'email'));
    }

    function test_channel_dc_author_map_author_detail_name_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_author_map_author_detail_name.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->author(0, 'name'));
    }

    function test_channel_dc_contributor_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_contributor.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example contributor', $feed->contributors(0, 'name'));
    }

    function test_channel_dc_creator_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_creator.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->author);
    }

    function test_channel_dc_creator_map_author_detail_email_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_creator_map_author_detail_email.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('me@example.com', $feed->author(0, 'email'));
    }

    function test_channel_dc_creator_map_author_detail_name_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_creator_map_author_detail_name.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->author(0, 'name'));
    }

    function test_channel_dc_publisher_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_publisher.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->publisher);
    }

    function test_channel_dc_publisher_email_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_publisher_email.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('me@example.com', $feed->publisher(0, 'email'));
    }

    function test_channel_dc_publisher_name_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_publisher_name.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->publisher(0, 'name'));
    }

    function test_channel_dc_rights_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_rights.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example copyright', $feed->copyright);
    }

    function test_channel_dc_subject_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_subject.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example category', $feed->category);
    }

    function test_channel_dc_subject_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_subject_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example category', $feed->categories[0][1]);
    }

    function test_channel_dc_subject_multiple_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_subject_multiple.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example category 2', $feed->categories[1][1]);
    }

    function test_channel_dc_title_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_title.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example title', $feed->title);
    }

    function test_channel_description_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_description.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->description);
    }

    function test_channel_description_escaped_markup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_description_escaped_markup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<p>Example description</p>', $feed->description);
    }

    function test_channel_description_map_tagline_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_description_map_tagline.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->tagline);
    }

    function test_channel_description_naked_markup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_description_naked_markup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<p>Example description</p>', $feed->description);
    }

    function test_channel_description_shorttag_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_description_shorttag.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('', $feed->description);
    }

    function test_channel_description_shorttag_2 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_description_shorttag.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/', $feed->link);
    }

    function test_channel_docs_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_docs.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://www.example.com/', $feed->docs);
    }

    function test_channel_generator_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_generator.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example generator', $feed->generator);
    }

    function test_channel_image_description_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_description.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Available in Netscape RSS 0.91', $feed->image(0, 'description'));
    }

    function test_channel_image_height_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_height.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(15, $feed->image(0, 'height'));
    }

    function test_channel_image_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.org/link', $feed->image(0, 'link'));
    }

    function test_channel_image_link_conflict_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_link_conflict.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://channel.example.com/', $feed->link);
    }

    function test_channel_image_title_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_title.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Sample image', $feed->image(0, 'title'));
    }

    function test_channel_image_title_conflict_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_title_conflict.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Real title', $feed->title);
    }

    function test_channel_image_url_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_url.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.org/url', $feed->image(0, 'url'));
    }

    function test_channel_image_width_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_width.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(80, $feed->image(0, 'width'));
    }

    function test_channel_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/', $feed->link);
    }

    function test_channel_managingEditor_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_managingEditor.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->author);
    }

    function test_channel_managingEditor_map_author_detail_email_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_managingEditor_map_author_detail_email.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('me@example.com', $feed->author(0, 'email'));
    }

    function test_channel_managingEditor_map_author_detail_name_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_managingEditor_map_author_detail_name.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->author(0, 'name'));
    }

    function test_channel_textInput_description_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_description.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('textInput description', $feed->textinput(0, 'description'));
    }

    function test_channel_textInput_description_conflict_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_description_conflict.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Real description', $feed->description);
    }

    function test_channel_textInput_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://textinput.example.com/', $feed->textinput(0, 'link'));
    }

    function test_channel_textInput_link_conflict_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_link_conflict.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://channel.example.com/', $feed->link);
    }

    function test_channel_textInput_name_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_name.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('textinput name', $feed->textinput(0, 'name'));
    }

    function test_channel_textInput_title_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_title.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('textInput title', $feed->textinput(0, 'title'));
    }

    function test_channel_textInput_title_conflict_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_title_conflict.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Real title', $feed->title);
    }

    function test_channel_title_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_title.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example feed', $feed->title);
    }

    function test_channel_title_apos_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_title_apos.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(u"Mark's title", $feed->title);
    }

    function test_channel_title_gt_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_title_gt.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('2 > 1', $feed->title);
    }

    function test_channel_title_lt_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_title_lt.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('1 < 2', $feed->title);
    }

    function test_channel_ttl_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_ttl.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('60', $feed->ttl);
    }

    function test_channel_webMaster_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_webMaster.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->publisher);
    }

    function test_channel_webMaster_email_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_webMaster_email.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('me@example.com', $feed->publisher(0, 'email'));
    }

    function test_channel_webMaster_name_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_webMaster_name.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->publisher(0, 'name'));
    }

    function test_item_author_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_author.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->author);
    }

    function test_item_author_map_author_detail_email_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_author_map_author_detail_email.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->author(0, 'email'));
    }

    function test_item_author_map_author_detail_name_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_author_map_author_detail_name.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->author(0, 'name'));
    }

    function test_item_category_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_category.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example category', $feed->getEntryByOffset(0)->category);
    }

    function test_item_category_domain_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_category_domain.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://www.example.com/', $feed->getEntryByOffset(0)->categories[0][0]);
    }

    function test_item_category_multiple_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_category_multiple.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://www.example.com/2', $feed->getEntryByOffset(0)->categories[1][0]);
    }

    function test_item_category_multiple_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_category_multiple_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example category 2', $feed->getEntryByOffset(0)->categories[1][1]);
    }

    function test_item_comments_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_comments.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->comments);
    }

    function test_item_content_encoded_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_content_encoded.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<p>Example content</p>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_content_encoded_mode_0 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_content_encoded_mode.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(, 1);
    }

    function test_item_content_encoded_type_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_content_encoded_type.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('text/html', $feed->getEntryByOffset(0)->content(0, 'type'));
    }

    function test_item_dc_author_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_author.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->author);
    }

    function test_item_dc_author_map_author_detail_email_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_author_map_author_detail_email.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->author(0, 'email'));
    }

    function test_item_dc_author_map_author_detail_name_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_author_map_author_detail_name.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->author(0, 'name'));
    }

    function test_item_dc_contributor_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_contributor.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example contributor', $feed->getEntryByOffset(0)->contributors(0, 'name'));
    }

    function test_item_dc_creator_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_creator.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->author);
    }

    function test_item_dc_creator_map_author_detail_email_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_creator_map_author_detail_email.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->author(0, 'email'));
    }

    function test_item_dc_creator_map_author_detail_name_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_creator_map_author_detail_name.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->author(0, 'name'));
    }

    function test_item_dc_publisher_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_publisher.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->publisher);
    }

    function test_item_dc_publisher_email_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_publisher_email.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->publisher(0, 'email'));
    }

    function test_item_dc_publisher_name_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_publisher_name.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->publisher(0, 'name'));
    }

    function test_item_dc_rights_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_rights.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example copyright', $feed->getEntryByOffset(0)->copyright);
    }

    function test_item_dc_subject_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_subject.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example category', $feed->getEntryByOffset(0)->category);
    }

    function test_item_dc_subject_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_subject_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example category', $feed->getEntryByOffset(0)->categories[0][1]);
    }

    function test_item_dc_subject_multiple_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_subject_multiple.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example category 2', $feed->getEntryByOffset(0)->categories[1][1]);
    }

    function test_item_dc_title_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_title.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example title', $feed->getEntryByOffset(0)->title);
    }

    function test_item_description_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_and_summary_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_and_summary.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_and_summary_2 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_and_summary.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example summary', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_description_br_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_br.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('article title<br /><br /> article byline<br /><br />text of article', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_escaped_markup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_escaped_markup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<p>Example description</p>', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_map_summary_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_map_summary.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->getEntryByOffset(0)->summary);
    }

    function test_item_description_naked_markup_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_naked_markup.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<p>Example description</p>', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_not_a_doctype_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_not_a_doctype.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals("""&lt;!' <a href="foo">""", $feed->getEntryByOffset(0)->description);
    }

    function test_item_enclosure_length_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_enclosure_length.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('100000', $feed->getEntryByOffset(0)->enclosures(0, 'length'));
    }

    function test_item_enclosure_multiple_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_enclosure_multiple.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(array('href' => 'http://example.com/2', 'length' => '200000', 'type' => 'image/gif'), $feed->getEntryByOffset(0)->enclosures[1]);
    }

    function test_item_enclosure_type_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_enclosure_type.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('image/jpeg', $feed->getEntryByOffset(0)->enclosures(0, 'type'));
    }

    function test_item_enclosure_url_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_enclosure_url.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->enclosures(0, 'url'));
    }

    function test_item_fullitem_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_fullitem.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<p>Example content</p>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_fullitem_mode_0 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_fullitem_mode.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(, 1);
    }

    function test_item_fullitem_type_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_fullitem_type.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('text/html', $feed->getEntryByOffset(0)->content(0, 'type'));
    }

    function test_item_guid_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://guid.example.com/', $feed->getEntryByOffset(0)->guid);
    }

    function test_item_guid_conflict_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_conflict_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://link.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_item_guid_guidislink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_guidislink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(, $feed->getEntryByOffset(0)->guidislink);
    }

    function test_item_guid_isPermaLink_conflict_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_isPermaLink_conflict_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://link.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_item_guid_isPermaLink_conflict_link_not_guidislink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_isPermaLink_conflict_link_not_guidislink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(, ! $feed->getEntryByOffset(0)->guidislink);
    }

    function test_item_guid_isPermaLink_guidislink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_isPermaLink_guidislink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(, $feed->getEntryByOffset(0)->guidislink);
    }

    function test_item_guid_isPermaLink_map_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_isPermaLink_map_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://guid.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_item_guid_map_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_map_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://guid.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_item_guid_not_permalink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_not_permalink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(, ! $feed->getEntryByOffset(0).has_key(->));
    }

    function test_item_guid_not_permalink_conflict_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_not_permalink_conflict_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://link.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_item_guid_not_permalink_not_guidislink_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_not_permalink_not_guidislink.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(, ! $feed->getEntryByOffset(0)->guidislink);
    }

    function test_item_guid_not_permalink_not_guidislink_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_not_permalink_not_guidislink_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(, ! $feed->getEntryByOffset(0)->guidislink);
    }

    function test_item_link_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_link.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_item_source_0 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_source.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(, 1);
    }

    function test_item_source_url_0 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_source_url.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(, 1);
    }

    function test_item_summary_and_description_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_summary_and_description.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example summary', $feed->getEntryByOffset(0)->summary);
    }

    function test_item_summary_and_description_2 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_summary_and_description.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_title_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_title.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Item 1 title', $feed->getEntryByOffset(0)->title);
    }

    function test_item_xhtml_body_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_xhtml_body.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<p>Example content</p>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_xhtml_body_mode_0 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_xhtml_body_mode.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(, 1);
    }

    function test_item_xhtml_body_type_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_xhtml_body_type.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('application/xhtml+xml', $feed->getEntryByOffset(0)->content(0, 'type'));
    }

    function test_rss_namespace_1_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_namespace_1.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->description);
    }

    function test_rss_namespace_2_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_namespace_2.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->description);
    }

    function test_rss_namespace_3_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_namespace_3.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->description);
    }

    function test_rss_namespace_4_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_namespace_4.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('Example description', $feed->description);
    }

    function test_rss_version_090_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_090.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('rss090', $feed->version());
    }

    function test_rss_version_091_netscape_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_091_netscape.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('rss091n', $feed->version());
    }

    function test_rss_version_091_userland_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_091_userland.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('rss091', $feed->version());
    }

    function test_rss_version_092_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_092.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('rss092', $feed->version());
    }

    function test_rss_version_093_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_093.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('rss093', $feed->version());
    }

    function test_rss_version_094_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_094.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('rss094', $feed->version());
    }

    function test_rss_version_20_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_20.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('rss20', $feed->version());
    }

    function test_rss_version_201_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_201.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('rss20', $feed->version());
    }

    function test_rss_version_21_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_21.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('rss20', $feed->version());
    }

    function test_rss_version_missing_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_missing.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('rss', $feed->version());
    }
}

$suite = new PHPUnit_TestSuite('rss_TestCase');
$result = PHPUnit::run($suite, '123');
echo $result->toString();

?>
