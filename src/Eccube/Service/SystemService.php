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
    }
    
    public function getDbversion()
    {
        
        $dbversion = $this->system
           ->fetchAll("SELECT version()");
        if (isset($dbversion)){
        $dbversion = $dbversion[0]['version'];
        }
        
        return $dbversion;
    }
   
}