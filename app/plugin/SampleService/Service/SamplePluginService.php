<?php

namespace Plugin\SampleService\Service;

use Eccube\Application;

class SamplePluginService
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function sample()
    {
        return 'sample service';
    }
}