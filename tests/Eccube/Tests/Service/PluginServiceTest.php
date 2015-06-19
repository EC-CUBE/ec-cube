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

namespace Eccube\Tests\Service;

use Eccube\Application;
use Symfony\Component\Yaml\Yaml;

class PluginServiceTest extends AbstractServiceTestCase
{
    protected $app;

    public function setUp()
    {
        parent::setUp();
    }

    /*
       正しいプラグインの条件
       * tarアーカイブである
       * 展開した直下のディレクトリにconfig.ymlがあり、正しいymlファイルである
       * config.ymlの必須要素が規定の文字数、文字種で定義されている

       * event.ymlが存在する場合、正しいymlである
 
     */

    private function createTempDir(){
        $t = sys_get_temp_dir()."/".sha1(mt_rand());
        if(!mkdir($t)){
            throw new \Exception($php_errormsg);
        }
        return $t;
    }

    public function testInstallPlugin()
    {
        self::markTestSkipped();
        $config=array();
        $config['name'] = "dummy";
        $config['code'] = "dummy";
        $config['version'] = "dumuy";

        $tmpdir=$this->createTempDir();
        $tmpfile=$tmpdir.'/plugin.tar';

        $tar = new \Archive_Tar($tmpfile, true);
        $tar->addString('config.yml',Yaml::dump($config));
        $service = $this->app['eccube.service.plugin']; 
        $this->assertTrue($service->install($tmpfile));

    }

    public function testUnInstallPlugin()
    {
    }

    public function testEnablePlugin()
    {
    }

    public function testDisablePlugin()
    {
    }

}
