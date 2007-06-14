<?php

require_once '../XML_Feed_Parser_TestCase.php';

class amp_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_amp01_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp01.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&#38;', $feed->getEntryByOffset(0)->title);
    }

    function test_amp02_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp02.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&#x26;', $feed->getEntryByOffset(0)->title);
    }

    function test_amp03_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp03.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&', $feed->getEntryByOffset(0)->title);
    }

    function test_amp04_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp04.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&amp;', $feed->getEntryByOffset(0)->title);
    }

    function test_amp05_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp05.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&#38;', $feed->getEntryByOffset(0)->title);
    }

    function test_amp06_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp06.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&#x26;', $feed->getEntryByOffset(0)->title);
    }

    function test_amp07_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp07.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&', $feed->getEntryByOffset(0)->title);
    }

    function test_amp08_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp08.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&amp;', $feed->getEntryByOffset(0)->title);
    }

    function test_amp09_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp09.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&#38;', $feed->getEntryByOffset(0)->title);
    }

    function test_amp10_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp10.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&#x26;', $feed->getEntryByOffset(0)->title);
    }

    function test_amp11_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp11.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&', $feed->getEntryByOffset(0)->title);
    }

    function test_amp12_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp12.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&amp;', $feed->getEntryByOffset(0)->title);
    }

    function test_amp13_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp13.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#38;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp14_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp14.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#x26;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp15_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp15.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&amp;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp16_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp16.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#38;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp17_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp17.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#x26;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp18_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp18.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&amp;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp19_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp19.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#38;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp20_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp20.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#x26;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp21_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp21.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&amp;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp22_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp22.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#38;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp23_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp23.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#x26;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp24_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp24.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&amp;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp25_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp25.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#38;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp26_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp26.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#x26;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp27_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp27.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&amp;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp28_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp28.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#38;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp29_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp29.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#x26;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp30_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp30.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&amp;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp31_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp31.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#38;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp32_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp32.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#x26;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp33_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp33.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&amp;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp34_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp34.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#38;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp35_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp35.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#x26;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp36_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp36.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&amp;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp37_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp37.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#38;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp38_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp38.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#x26;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp39_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp39.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&amp;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp40_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp40.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#38;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp41_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp41.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#x26;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp42_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp42.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&amp;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp43_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp43.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#38;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp44_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp44.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#x26;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp45_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp45.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&amp;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp46_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp46.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#38;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp47_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp47.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#x26;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp48_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp48.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&amp;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp49_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp49.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&#38;', $feed->getEntryByOffset(0)->title);
    }

    function test_amp50_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp50.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&#x26;', $feed->getEntryByOffset(0)->title);
    }

    function test_amp51_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp51.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&', $feed->getEntryByOffset(0)->title);
    }

    function test_amp52_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp52.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('&amp;', $feed->getEntryByOffset(0)->title);
    }

    function test_amp53_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp53.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#38;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp54_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp54.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&#x26;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp55_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp55.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<b>&amp;</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp56_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp56.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#38;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp57_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp57.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&#x26;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp58_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp58.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<strong>&amp;</strong>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp59_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp59.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div><b>&amp;</b></div>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp60_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp60.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div><b>&amp;</b></div>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp61_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp61.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div><b>&amp;</b></div>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp62_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp62.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div><strong>&amp;</strong></div>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp63_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp63.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div><strong>&amp;</strong></div>', $feed->getEntryByOffset(0)->title);
    }

    function test_amp64_1 () { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/amp/amp64.xml');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals('<div><strong>&amp;</strong></div>', $feed->getEntryByOffset(0)->title);
    }
}

$suite = new PHPUnit_TestSuite('amp_TestCase');
$result = PHPUnit::run($suite, '123');
echo $result->toString();

?>
