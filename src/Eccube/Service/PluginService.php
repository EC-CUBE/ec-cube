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

class PluginService
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function install($filename)
    {
       // エラーチェック
       // ファイルコピー
       // インストーラ起動      
       $tmp = $this->createTempDir();

       $this->unpackPluginArchive($filename,$tmp); //一旦テンポラリに展開

       // TODO:テンポラリファイル削除
       $meta = $this->readYml($tmp."/config.yml");
       $event = $this->readYml($tmp."/event.yml");
       if(!$event) {
           throw new \Exception("event.yml not found or syntax error");
       }
       if(!$meta) {
           throw new \Exception("config.yml not found or syntax error");
       }

       $pluginBaseDir =   __DIR__.'/../../../app/Plugin'.'/'.$meta['name']  ; // ここの埋め込みはなくしたい
       $this->createPluginDir($pluginBaseDir);


       $this->unpackPluginArchive($filename,$pluginBaseDir); // 問題なければ本当のplugindirへ
       $this->registerPlugin($meta,$event);
echo "<hr>";
exit;
       $this->callInstallMethod(  );

    }
    public function uninstall($filename)
    {
       // エラーチェック
       // ファイルコピー
       // インストーラ起動      
    }
    public function enable($filename)
    {
    }
    public function disable($filename)
    {
    }
    public function update($filename)
    {
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
    public function registerPlugin( $meta ,$event_yml )
    {

        $em = $this->app['orm.em'];
        $p = new \Eccube\Entity\Plugin();
        $p->setName($meta['name'])
          ->setEnable(1)
          ->setClassName($meta['event'])
          ->setVersion($meta['version'])
          ->setDelflg(0)
          ->setSource(0)
          ->setCode($meta['code']);

           $handlers=$em->getRepository('Eccube\Entity\PluginEventHandler')->getHandlers() ;


        foreach($event_yml as $event=>$handlers){
            foreach($handlers as $handler){
                $peh = new \Eccube\Entity\PluginEventHandler();
                $peh->setPlugin($p)
                    ->setEvent($event)
                    ->setdelFlg(0)
                    ->setHandler($handler[0])
                    ->setPriority($handlers=$em->getRepository('Eccube\Entity\PluginEventHandler')->calcNewPriority( $event,$handler[1]) );
                $em->persist($peh);
            }
        }

        $em->persist($p); 
        $em->flush(); 


    }

    public function registerEventHandler()
    {
    }
    public function callInstallMethod()
    {
    }

}
