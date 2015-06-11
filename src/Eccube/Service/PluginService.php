<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Service;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class PluginService
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function install($path)
    {
       // エラーチェック
       // ファイルコピー
       // インストーラ起動      
       $tmp = $this->createTempDir();

       $this->unpackPluginArchive($path,$tmp); //一旦テンポラリに展開
       $this->checkPluginArchiveContent($tmp);


       $config = $this->readYml($tmp."/config.yml");
       $event = $this->readYml($tmp."/event.yml");

       $this->deleteFile($tmp); // テンポラリのファイルを削除

       $this->checkSamePlugin($config['code']);

       $pluginBaseDir =  $this->calcPluginDir($config['name'])  ;
       $this->createPluginDir($pluginBaseDir); // 本来の置き場所を作成


       $this->unpackPluginArchive($path,$pluginBaseDir); // 問題なければ本当のplugindirへ

       $this->registerPlugin($config,$event); // dbにプラグイン登録
       $this->callPluginManagerMethod( $config,'install' ); 

    }

    public function uninstall(\Eccube\Entity\Plugin $plugin)
    {
       $pluginDir = $this->calcPluginDir($plugin->getName());

       $this->callPluginManagerMethod( Yaml::Parse($pluginDir.'/'."config.yml" ),'uninstall' ); 
       $this->unregisterPlugin($plugin);
       $this->deleteFile($pluginDir); 

    }
    public function enable(\Eccube\Entity\Plugin $plugin,$enable=true)
    {
        $em = $this->app['orm.em'];
        $plugin->setEnable($enable ? 1:0);
        $em->persist($plugin); 
        $em->flush(); 
        $this->callPluginManagerMethod( Yaml::Parse($pluginDir.'/'."config.yml" ) ,$enable ? 'enable':'disable'    ); 
    }
    public function disable(\Eccube\Entity\Plugin $plugin)
    {
        $this->enable($plugin,false);
    }
    public function update(\Eccube\Entity\Plugin $plugin,$path)
    {
       $tmp = $this->createTempDir();

       $this->unpackPluginArchive($path,$tmp); //一旦テンポラリに展開
       $this->checkPluginArchiveContent($tmp);

       $config = $this->readYml($tmp."/config.yml");
       $event = $this->readYml($tmp."/event.yml");

       if($plugin->getCode != $config['code']){
           throw new \Exception("new/old plugin code is different.");
       }

       $this->deleteFile($tmp); // テンポラリのファイルを削除


       $this->unpackPluginArchive($path,$pluginBaseDir); // 問題なければ本当のplugindirへ

       $this->updatePlugin($config,$event); // dbにプラグイン登録
       $this->callPluginManagerMethod( $config,'update' ); 
    }


    public function calcPluginDir($name)
    {
        return __DIR__.'/../../../app/Plugin'.'/'.$name;
    }
    public function checkSamePlugin($code)
    {
        $em = $this->app['orm.em'];

        $rep=$em->getRepository('Eccube\Entity\Plugin') ;
        if(count($rep->getPluginByCode($code,true))){
            throw new \Exception('plugin already installed.');
        }

    }
    public function checkPluginArchiveContent($dir)
    {
       $meta = $this->readYml($dir."/config.yml");
       $event = $this->readYml($dir."/event.yml");
       if(!$event) {
           throw new \Exception("event.yml not found or syntax error");
       }
       if(!$meta) {
           throw new \Exception("config.yml not found or syntax error");
       }
       if(!file_exists($dir . "/" . $meta['event'].".php")){
           throw new \Exception("event handler class not found");
       }
    }


    public function readYml($yml)
    {
        return Yaml::Parse($yml);
    }
    public function createTempDir()
    {
        $d=(sys_get_temp_dir().'/'.sha1( openssl_random_pseudo_bytes(16) ));
        $b=mkdir($d,0777);
        if(!$b){
            throw new \Exception($php_errormsg);
        }
        return $d;
        
    }
    public function createPluginDir($d)
    {
        $b=mkdir($d);
        if(!$b){
            throw new \Exception($php_errormsg);
        }
    }
    public function unpackPluginArchive($archive,$dir)
    {
        $tar = new \Archive_Tar($archive, true);
        $tar->setErrorHandling(PEAR_ERROR_EXCEPTION);
        $result = $tar->extractModify($dir . '/', '');
    }

    public function updatePlugin(\Eccube\Entity\Plugin $plugin,$meta,$event_yml)
    {
        $em = $this->app['orm.em'];
        $em->getConnection()->beginTransaction(); 
        $plugin->setVersion($meta['version']) 
               ->setEvent($meta['event']) 
               ->setName($meta['name']);

        $rep=$em->getRepository('Eccube\Entity\PluginEventHandler');
        foreach($event_yml as $event=>$handlers){
            foreach($handlers as $handler){
                $peh = $rep->findBy(array('del_flg'=>0,'plugin_id'=> $plugin->getId(),'event' => $event ,'handler' => $handler[0] ));
                if(!$peh){ // 新規にevent.ymlに定義されたハンドラなのでinsertする
                    $peh = new \Eccube\Entity\PluginEventHandler();
                    $peh->setPlugin($p)
                        ->setEvent($event)
                        ->setdelFlg(0)
                        ->setHandler($handler[0])
                        ->setPriority($em->getRepository('Eccube\Entity\PluginEventHandler')->calcNewPriority( $event,$handler[1]) );
                    $em->persist($peh);
                    $em->flush(); 

                }
            }
        }

        $em->persist($plugin); 
        $em->flush(); 
        $em->getConnection()->commit();
    }
    public function registerPlugin( $meta ,$event_yml )
    {

        $em = $this->app['orm.em'];
        $em->getConnection()->beginTransaction(); 
        $p = new \Eccube\Entity\Plugin();
        $p->setName($meta['name'])
          ->setEnable(1)
          ->setClassName($meta['event'])
          ->setVersion($meta['version'])
          ->setDelflg(0)
          ->setSource(0)
          ->setCode($meta['code']);

        $em->persist($p); 
        $em->flush(); 

        foreach($event_yml as $event=>$handlers){
            foreach($handlers as $handler){
                $peh = new \Eccube\Entity\PluginEventHandler();
                $peh->setPlugin($p)
                    ->setEvent($event)
                    ->setdelFlg(0)
                    ->setHandler($handler[0])
                    ->setPriority($em->getRepository('Eccube\Entity\PluginEventHandler')->calcNewPriority( $event,$handler[1]) );
                $em->persist($peh);
                $em->flush(); 
            }
        }

        $em->persist($p); 
        $em->flush(); 
        $em->getConnection()->commit();

    }

    public function unregisterPlugin(\Eccube\Entity\Plugin $p){
        $em = $this->app['orm.em'];
        $em->getConnection()->beginTransaction(); 

        $p->setDelFlg(1)->setEnable(0);

/*
        foreach($p->getPluginEventHandlers()->toArray() as $handler){
            $handler->setDelFlg(1);
            $em->persist($handler); 
        }
*/
       
        $rep=$em->getRepository('Eccube\Entity\PluginEventHandler');
        foreach($rep->findBy(array('plugin_id'=> $p->getId()  )) as $peh ) {
            $peh->setDelFlg(1); 
            $em->persist($peh); 
        }

        $em->persist($p); 
        $em->flush(); 

        $em->getConnection()->commit();
    }

    public function callPluginManagerMethod($meta,$method)
    {
        $class = '\\Plugin'.'\\'.$meta['name'].'\\' .'PluginManager';
        if(class_exists($class)){
            $installer = new $class(); // マネージャクラスに所定のメソッドがある場合だけ実行する
            if(method_exists(  $installer , $method )){
                $installer->$method($meta,$this->app);
            }
        }
    }

    public function deleteFile($path)
    {
        $f=new Filesystem();
        return $f->remove($path);
    }
}
