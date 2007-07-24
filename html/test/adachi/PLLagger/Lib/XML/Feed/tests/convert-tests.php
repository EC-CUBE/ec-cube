<?php

$template = '<?php
require_once \'PHPUnit.php\';
require_once \'XML/Feed/Parser.php\';
class ';

$endTemplate = '
$result = PHPUnit::run($suite, \'123\');
echo $result->toString();

?>
';
error_reporting(E_ERROR);

function applyFilters(&$test)
{
    if (! strstr($test, 'bozo') and ! preg_match('/^encoding/', $test)
        and ! preg_match('/^header/', $test))
    {
        $testparts = explode(' == ', $test);
        $testparts[0] = preg_replace('/^not/', '!', $testparts[0]);
        $testparts[0] = preg_replace('/(^|\s)feed/', '$1\$feed', $testparts[0]);
        $testparts[0] = preg_replace('/entries\[(\d+)\]/', '\$feed->getEntryByOffset($1)', $testparts[0]);
        $testparts[0] = preg_replace('/\[\'(.*)\'\]/', '->$1', $testparts[0]);
        $testparts[0] = preg_replace('/\'.*?\'/', '->', $testparts[0]);
        $testparts[0] = preg_replace('/^version$/', '\$feed->version()', $testparts[0]);
        $testparts[0] = preg_replace('/_detail->value/', '', $testparts[0]);
        $testparts[0] = preg_replace('/_detail->(.*)/', '(0, \'$1\')', $testparts[0]);
        $testparts[0] = preg_replace('/getEntryByOffset\(0\)->(.*?)->(.*)/', 
            'getEntryByOffset(0)->$1(0, \'$2\')', $testparts[0]);

        if (! preg_match('/feed->getEntry/', $testparts[0])) {
            $testparts[0] = preg_replace('/feed->(.*?)->(.*)/', 'feed->$1(0, \'$2\')', $testparts[0]);
        }

        $testparts[1] = preg_replace('/u\'/', '\'', $testparts[1]);
        $testparts[1] = preg_replace('/\'(.*?)\': \'(.*?)\'/', '\'$1\' => \'$2\'', $testparts[1]);
        $testparts[1] = preg_replace('/{(.*?)}/', 'array($1)', $testparts[1]);
        $testparts[1] = preg_replace('/\[(.*?)\]/', 'array($1)', $testparts[1]);
        $testparts[1] = preg_replace('/^\((.*)\)$/', 'array($1)', $testparts[1]);
        $testparts[1] = preg_replace('/^<div>(.*)<\/div>$/', '$1', $testparts[1]);

        $test = implode(' == ', $testparts);
        return $test;
    }
}

function testToPHP($test)
{
    $tests = explode(' and ', $test);
    return $tests;
}

function extractTest($path)
{
    $data = array();
    $f = fopen('./feedparsertests/wellformed/' . $path, 'r');
    while ($line = fgets($f))
    {
        $line = trim($line);
        if (preg_match('/^Description:/', $line))
        {
            $data['description'] = trim(preg_replace('/Description:/', '', $line));
        } else if (preg_match('/^Expect:/', $line))
        {
            $data['expect'] = trim(preg_replace('/Expect:/', '', $line));
        }
        if (!empty($data['expect']) and !empty($data['description']))
        {
            break;
        }
    }
    fclose($f);
    return $data;
}

$handle = opendir('./feedparsertests/wellformed/');
$tests_passed = array();
$tests_failed = array();
$all_tests = array();
while (false !== ($dir = readdir($handle)))
{
    if (! preg_match('/^\./', $dir) and is_dir('./feedparsertests/wellformed/' . $dir))
    {
        $tests_passed[$dir] = array();
        $tests_failed[$dir] = array();
        $allTests[$dir] = array();
        $innerHandle = opendir('feedparsertests/wellformed/' . $dir);
        while (false !== ($file = readdir($innerHandle)))
        {
            if (preg_match('/.xml$/', $file))
            {
                $parts = extractTest($dir . '/' . $file);
                $theseTests = testToPHP($parts['expect']);
                foreach ($theseTests as $thisKey => $thisTest)
                {
                    $allTests[$dir][$file . '_' . $thisKey] = $thisTest;
                }
            }
        }
        $iterTests = array_filter($allTests[$dir], 'applyFilters');
        $fw = fopen('./convertedtests/' . $dir . '.php', 'w');
        fwrite($fw, $template . $dir . "_TestCase extends XML_Feed_Parser_TestCase {\n");
        foreach($iterTests as $key => $test)
        {
            $funcname = str_replace('.xml', '', $key);
            $funcname = str_replace('.', '_', $funcname);
            $file = preg_replace('/(.*)_.*/', '$1', $key);
            fwrite($fw, '
    function test_' . $funcname . ' () { 
        $content = file_get_contents(\'../feedparsertests/wellformed/' . $dir . '/' . $file . '\');
        try {
            $feed = new XML_Feed_Parser($content);
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(' . implode(', ', array_reverse(explode(' == ', $test))) .');
    }
');
        }
        fwrite($fw, '}

$suite = new PHPUnit_TestSuite(\'' . $dir . '_TestCase\');');
        fwrite($fw, $endTemplate);
        fclose($fw);
        $all_tests = array_merge($all_tests, $allTests[$dir]);
    }
}

$total_tests = 0;
$total_passed = 0;
$total_failed = 0;

foreach($tests_passed as $test)
{
    $total_tests += count($test);
    $total_passed += count($test);
}

foreach ($tests_failed as $testf)
{
    $total_tests += count($testf);
    $total_failed += count($testf);
}

?>
