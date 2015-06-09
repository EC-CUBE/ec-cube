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

#use Eccube\Event\RenderEvent;

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
exit;
       // TODO:テンポラリファイル削除
       $meta = $this->readMetaData($tmp);
       $pluginBaseDir =   __DIR__.'/../../app/Plugin'.'/'.$meta['name']  ; // ここの埋め込みはなくしたい
       $this->createPluginDir($pluginBaseDir);
       $this->unpackPluginArchive($filename,$pluginBaseDir); // 問題なければ本当のplugindirへ
       $this->registerPlugin($meta);
       $this->registerEventHandler($meta);
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

    public function readMetaData($dir)
    {
        $ymlfile = $dir."/config.yml";
        return Yaml::Parse($ymlfile);
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
    public function registerPlugin()
    {
    }

    public function registerEventHandler()
    {
    }
    public function callInstallMethod()
    {
    }

}
