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
        
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('version', 'v');

        $version = $this->app['orm.em']->createNativeQuery('select version()', $rsm)
                                 ->getSingleScalarResult();
          
        return $version;
    }
   
}