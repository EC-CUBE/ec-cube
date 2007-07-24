<?php

require_once 'XML_Feed_Parser_TestCase.php';

class XML_Feed_Parser_Farsi_TestCase extends XML_Feed_Parser_TestCase
{
    function __construct($name)
    {
        $this->PHPUnit_TestCase($name);
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'hoder.xml');
        $this->feed = new XML_Feed_Parser($this->file);
        $this->entry = $this->feed->getEntryByOffset(0);
    }

    function test_itemTitleFarsi()
    {
        $value = 'لينکدونی‌ | جلسه‌ی امریکن انترپرایز برای تقسیم قومی ایران';
        $this->assertEquals($value, $this->entry->title);
    }
}

$suite = new PHPUnit_TestSuite('XML_Feed_Parser_Farsi_TestCase');
$result = PHPUnit::run($suite, '123');
echo $result->toString();

?>