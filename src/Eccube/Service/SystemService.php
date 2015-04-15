<?php
namespace Eccube\Service;
use Eccube\Application;


class SystemService
{
    private $app;
    
    private $system;
    
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->system = $app['db'];
        //$this->server = $app['request']->server;
    }
    
    public function getDbversion()
    {
        $dbversion = $this->system
           ->fetchAll('SELECT version()');
        
        $dbversion = $dbversion[0]['version'];
        
        return $dbversion;
    }
   
}