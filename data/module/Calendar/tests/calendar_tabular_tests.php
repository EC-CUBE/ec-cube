<?php
// $Id$

require_once('simple_include.php');
require_once('calendar_include.php');

class CalendarTabularTests extends GroupTest {
    function CalendarTabularTests() {
        $this->GroupTest('Calendar Tabular Tests');
        $this->addTestFile('month_weekdays_test.php');
        $this->addTestFile('month_weeks_test.php');
        $this->addTestFile('week_test.php');
        //$this->addTestFile('week_firstday_0_test.php'); //switch with the above
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new CalendarTabularTests();
    $test->run(new HtmlReporter());
}
?>
