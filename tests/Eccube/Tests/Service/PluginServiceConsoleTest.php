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
use Eccube\Common\Constant;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class PluginServiceTest extends AbstractServiceTestCase
{
    protected $app;
    protected $pluginpath;

    public __construct()
    {
        $this->$pluginpath = $this->app['config']['plugin_realdir'].DIRECTORY_SEPARATOR;
    }

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

    /*
       設置のみプラグインの正しい挙動
       * 設置した直下のディレクトリにconfig.ymlがあり、正しいymlファイルである
       * config.ymlの必須要素が規定の文字数、文字種で定義されている
       * event.ymlが存在する場合、正しいymlである
       * DBには登録されていない

     */

    // テスト用のダミープラグインを配置する
    private function createTempDir()
    {
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

    public function setUnregisteredPlugin()
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

        // 解凍後インストール
        // テスト用プラグインの設置
        $service->install($tmpfile);

        $this->assertTrue($service->sandBoxExcute($this->pluginpath.$tmpname, 'uninstall'));
    }

    // consoleでのインストールを検証
    public function testConsoleInstallPlugin()
    {
        // プラグインの設置
        $this->setUnregisteredPlugin();
        $this->assertTrue($service->sandBoxExcute($this->pluginpath.$tmpname, 'install'));
    }

    // consoleでのアンインストールを検証
    public function testConsoleUninstallPlugin()
    {
        // プラグインの設置
        $this->setUnregisteredPlugin();
        $this->assertTrue($service->sandBoxExcute($this->pluginpath.$tmpname, 'install'));
        $this->assertTrue($service->sandBoxExcute($this->pluginpath.$tmpname, 'uninstall'));
    }

    // consoleでの有効化を検証
    public function testConsoleDiablePlugin()
    {
        // プラグインの設置
        $this->setUnregisteredPlugin();
        $this->assertTrue($service->sandBoxExcute($this->pluginpath.$tmpname, 'install'));
        $this->assertTrue($service->sandBoxExcute($this->pluginpath.$tmpname, 'disable'));
    }

    // consoleでの有効化を検証
    public function testConsoleEnablePlugin()
    {
        // プラグインの設置
        $this->setUnregisteredPlugin();
        $this->assertTrue($service->sandBoxExcute($this->pluginpath.$tmpname, 'install'));
        $this->assertTrue($service->sandBoxExcute($this->pluginpath.$tmpname, 'disable'));
        $this->assertTrue($service->sandBoxExcute($this->pluginpath.$tmpname, 'enable'));
    }

    // consoleでのリロードを検証
    public function testConsoleReloadPlugin()
    {
        // プラグインの設置
        $this->setUnregisteredPlugin();
        $this->assertTrue($service->sandBoxExcute($this->pluginpath.$tmpname, 'install'));
        $this->assertTrue($service->sandBoxExcute($this->pluginpath.$tmpname, 'reload'));
    }
}
