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
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class PluginCommandControllerTest extends AbstractAdminWebTestCase
{
    protected $app;
    protected $pluginpath;

    public function __construct()
    {
        parent::__construct();
        $this->app = \Eccube\Application::getInstance();
        $this->pluginpath = $this->app['config']['plugin_realdir'].DIRECTORY_SEPARATOR;
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

    /**
     * 置くだけプラグインを設置
     * @return boolean
     */
    private function createTempDir()
    {
        $t = sys_get_temp_dir()."/plugintest.".sha1(mt_rand());
        if (!mkdir($t)) {
            throw new \Exception("$t ".$php_errormsg);
        }

        return $t;
    }

    public function deleteFile($path)
    {
        $f = new Filesystem();

        return $f->remove($path);
    }

    public function setPluginOnFolder()
    {
        // インストールするプラグインを作成する
        $tmpname = "dummy".sha1(mt_rand());
        $config = array();
        $config['name'] = $tmpname."_name";
        $config['code'] = $tmpname;
        $config['version'] = $tmpname."_version";

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar.gz';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml', Yaml::dump($config));
        $service = $this->app['eccube.service.plugin'];

        $dummyEvent = <<<'EOD'
<?php
namespace Plugin\@@@@ ;


class DummyEvent
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    public function dummyHandler()
    {
        echo "dummyHandler\n";
    }
    public function dummyHandlerFirst()
    {
        echo "dummyHandlerFirst\n";
    }
    public function dummyHandlerLast()
    {
        echo "dummyHandlerLast\n";
    }

}

EOD;
        $dummyEvent = str_replace('@@@@', $tmpname, $dummyEvent); // イベントクラス名はランダムなのでヒアドキュメントの@@@@部分を置換
        $tar->addFromString("DummyEvent.php", $dummyEvent);

        // イベント定義を作成する
        $event = array();
        $event['eccube.event.app.before'] = array();
        $event['eccube.event.app.before'][] = array("dummyHandler", 'NORMAL');
        $event['eccube.event.app.before'][] = array("dummyHandlerFirst", 'FIRST');
        $event['eccube.event.app.after'] = array();
        $event['eccube.event.app.after'][] = array("dummyHandlerLast", 'LAST');
        $tar->addFromString('event.yml', Yaml::dump($event));


        $dummyPluginManager = <<<'PMEOD'
<?php
namespace Plugin\@@@@ ;

use Eccube\Plugin\AbstractPluginManager;
use Eccube\Common\Constant;
use Eccube\Util\Cache;
use Symfony\Component\Filesystem\Filesystem;

class PluginManager extends AbstractPluginManager
{
    public function __construct()
    {
    }

    public function install($config, $app)
    {
        return 'install';
    }

    public function uninstall($config, $app)
    {
        return 'uninstall';
    }

    public function enable($config, $app)
    {
        return 'enable';
    }

    public function disable($config, $app)
    {
        return 'disable';
    }

    public function update($config, $app)
    {
        return 'update';
    }
}

PMEOD;
        $dummyPluginManager = str_replace('@@@@', $tmpname, $dummyPluginManager);
        $tar->addFromString("PluginManager.php", $dummyPluginManager);


        return array('code' => $tmpname, 'path' => $tmpfile);
    }

    public function getPluginFromCode($pluginCode)
    {
        return $this->app['eccube.repository.plugin']->findOneBy(array('del_flg' => 0, 'code' => $pluginCode));
    }

    public function test_command_AdminStore_PluginCommand_Install()
    {
        $tmpinfo = $this->setPluginOnFolder();

        $application = new Application();

        $command = $application->find('plugin:develop');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array('mode' => 'install', '--path' => $tmpinfo['path'])
        );

        $this->assertRegExp('/success/', $commandTester->getDisplay());
    }

    public function test_command_AdminStore_PluginCommand_UnInstall_OnlyDb()
    {
        $tmpinfo = $this->setPluginOnFolder();

        $application = new Application();

        $command = $application->find('plugin:develop');
        $commandTester = new CommandTester($command);

        // 圧縮プラグインインストール
        $commandTester->execute(
            array('mode' => 'install', '--path' => $tmpinfo['path'])
        );

        // 圧縮プラグインアンインストール
        $commandTester->execute(
            array('mode' => 'uninstall', '--code' => $tmpinfo['code'])
        );

        $this->assertRegExp('/success/', $commandTester->getDisplay());
    }

    public function test_command_AdminStore_PluginCommand_Reload()
    {
        $tmpinfo = $this->setPluginOnFolder();

        $application = new Application();

        $command = $application->find('plugin:develop');
        $commandTester = new CommandTester($command);

        // 圧縮プラグインインストール
        $commandTester->execute(
            array('mode' => 'install', '--path' => $tmpinfo['path'])
        );

        // 圧縮プラグインリロード
        $commandTester->execute(
            array('mode' => 'reload', '--code' => $tmpinfo['code'])
        );

        $this->assertRegExp('/success/', $commandTester->getDisplay());
    }

    public function test_command_AdminStore_PluginCommand_install_OnlyDb()
    {
        $tmpinfo = $this->setPluginOnFolder();

        $application = new Application();

        $command = $application->find('plugin:develop');
        $commandTester = new CommandTester($command);

        // 圧縮プラグインインストール
        $commandTester->execute(
            array('mode' => 'install', '--path' => $tmpinfo['path'])
        );

        // 圧縮プラグインアンインストール
        $commandTester->execute(
            array('mode' => 'uninstall', '--code' => $tmpinfo['code'])
        );

        // 圧縮プラグインインストール
        $commandTester->execute(
            array('mode' => 'install', '--code' => $tmpinfo['code'])
        );

        $this->assertRegExp('/success/', $commandTester->getDisplay());
    }

    public function test_command_AdminStore_PluginCommand_Uninstall_All()
    {
        $tmpinfo = $this->setPluginOnFolder();

        $application = new Application();

        $command = $application->find('plugin:develop');
        $commandTester = new CommandTester($command);

        // 圧縮プラグインインストール
        $commandTester->execute(
            array('mode' => 'install', '--path' => $tmpinfo['path'])
        );

        // 圧縮プラグインアンインストール
        $commandTester->execute(
            array('mode' => 'uninstall', '--code' => $tmpinfo['code'], 'force' => '1')
        );

        $this->assertRegExp('/success/', $commandTester->getDisplay());
        // 削除されているか確認
        $this->assertFileNotExists(__DIR__."/../../../../../../app/Plugin/$tmpname/config.yml");
    }
}
