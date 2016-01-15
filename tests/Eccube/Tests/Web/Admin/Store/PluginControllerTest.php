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


namespace Eccube\Tests\Web\Admin\Store;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class PluginControllerTest extends AbstractAdminWebTestCase
{
    protected $app;

    public function tearDown()
    {
        $dirs = array();
        $finder = new Finder();
        $iterator = $finder
            ->in($this->app['config']['plugin_realdir'])
            ->name('dummy*')
            ->directories();
        foreach ($iterator as $dir) {
            $dirs[] = $dir->getPathName();
        }

        foreach ($dirs as $dir) {
            $this->deleteFile($dir);
        }
        parent::tearDown();
    }
    // テスト用のダミープラグインを配置する
    private function createTempDir(){
        $t = sys_get_temp_dir()."/plugintest.".sha1(mt_rand());
        if(!mkdir($t)){
            throw new \Exception("$t ".$php_errormsg);
        }
        return $t;
    }

    public function deleteFile($path)
    {
        $f = new Filesystem();
        return $f->remove($path);
    }

    public function test_routing_AdminPlugin_index()
    {
        // インストールするプラグインを作成する
        $tmpname="dummy".sha1(mt_rand());
        $config=array();
        $config['name'] = $tmpname."_name";
        $config['code'] = $tmpname;
        $config['version'] = $tmpname."_version";

        $tmpdir=$this->createTempDir();
        $tmpfile=$tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml',Yaml::dump($config));
        $service = $this->app['eccube.service.plugin'];
        $pluginBaseDir = $service->calcPluginDir($config['code']);
        $service->createPluginDir($pluginBaseDir); // 本来の置き場所を作成
        $service->unpackPluginArchive($tmpfile, $pluginBaseDir); // 問題なければ本当のplugindirへ

        // インストールできるか
        $service->checkPluginArchiveContent($pluginBaseDir);
        $config = $service->readYml($pluginBaseDir.'/'.'config.yml');
        $event = $service->readYml($pluginBaseDir.'/'.'event.yml');
        $service->callPluginManagerMethod($config, 'install');

        $crawler = $this->client->request('GET', $this->app->url('admin_store_plugin'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertRegExp(
            '/'.$tmpname.'/',
            $this->client->getResponse()->getContent()
        );
    }


    public function testIndexWithNotFound()
    {
        $client = $this->createClient();
        try {
            $crawler = $this->client->request('GET', $this->app->url('admin_store_plugin').DIRECTORY_SEPARATOR.'test');
            $this->fail();
        } catch (NotFoundHttpException $e) {
            $this->expected = 404;
            $this->actual = $e->getStatusCode();
        }
        $this->verify();
    }
}
