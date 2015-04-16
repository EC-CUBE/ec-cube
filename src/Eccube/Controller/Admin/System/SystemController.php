<?php

namespace Eccube\Controller\Admin\System;

use Eccube\Application;

class SystemController
{
    
    private $maintitle;

    private $subtitle;

    public function __construct()
    {
        $this->maintitle = 'システム設定';
        $this->subtitle = 'システム情報';
    }
    
    public function Index(Application $app)
    { 
         switch ($app['request']->get('mode')) {
            case 'info':
                ob_start();
                phpinfo();
                $phpinfo = ob_get_contents();
                ob_end_clean();

                return $phpinfo;
               
                break;
            default:
                break;
        }
        
        $this->arrSystemInfo = $this->getSystemInfo($app);
        
        return $app['twig']->render('Admin/System/system.twig', array(
            'tpl_maintitle' => $this->maintitle,
            'tpl_subtitle'  => $this->subtitle,
            'arrSystemInfo' => $this->arrSystemInfo,
        ));
    }
    
     public function getSystemInfo(Application $app)
    {
       $system = $app['eccube.service.system'];
       $server = $app['request'];
      
       $arrSystemInfo = array(
            array('title' => 'EC-CUBE',     'value' => $app['config']['ECCUBE_VERSION']),
            array('title' => 'サーバーOS',    'value' => php_uname()),
            array('title' => 'DBサーバー',    'value' => $system->getDbversion()),
            array('title' => 'WEBサーバー',   'value' => $server->server->get("SERVER_SOFTWARE")),
        );
        
        $value = phpversion() . ' (' . implode(', ', get_loaded_extensions()) . ')';
        $arrSystemInfo[] = array('title' => 'PHP', 'value' => $value);

        if (extension_loaded('GD') || extension_loaded('gd')) {
            $arrValue = array();
            foreach (gd_info() as $key => $val) {
                $arrValue[] = "$key => $val";
            }
            $value = '有効 (' . implode(', ', $arrValue) . ')';
        } else {
            $value = '無効';
        }
        $arrSystemInfo[] = array('title' => 'GD', 'value' => $value);
        $arrSystemInfo[] = array('title' => 'HTTPユーザーエージェント', 'value' => $server->headers->get('User-Agent'));

        return $arrSystemInfo;
    }

}