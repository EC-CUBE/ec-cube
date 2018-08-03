<?php


namespace Interactions;


use Facebook\WebDriver\WebDriverAction;

class WaitAction implements WebDriverAction
{

    /**
     * @var int $timeout_in_second
     */
    private $timeout_in_second;

    /**
     * @param integer $timeout_in_second
     */
    function __construct($timeout_in_second)
    {
        $this->timeout_in_second = $timeout_in_second;
    }

    /**
     * @return void
     */
    public function perform()
    {
        sleep($this->timeout_in_second);
    }
}