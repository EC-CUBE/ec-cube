<?php
set_include_path('/Users/james/Projects/PEAR' . PATH_SEPARATOR . get_include_path());
require_once 'PEAR/Config.php';
require_once 'XML/Feed/Parser.php';
require_once 'PHPUnit.php';

abstract class XML_Feed_Parser_TestCase extends PHPUnit_Testcase {
    static function getSampleDir() {
        $config = new PEAR_Config;
        return $config->get('data_dir') . '/XML_Feed_Parser/samples';
    }
}

abstract class XML_Feed_Parser_Converted_TestCase extends XML_Feed_Parser_TestCase {
    function setup() {
        $this->fp_test_dir = XML_Feed_Parser_TestCase::getSampleDir() . 
            DIRECTORY_SEPARATOR . 'feedparsertests';
        if (! is_dir($fp_test_dir)) {
            throw new Exception('Feed parser tests must be unpacked into the folder ' . 
                $this->fp_test_dir);
        }
    }
}

?>
