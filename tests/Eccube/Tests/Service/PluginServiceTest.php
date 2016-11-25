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
       正しいプラグインの条件
       * tar/zipアーカイブである
       * 展開した直下のディレクトリにconfig.ymlがあり、正しいymlファイルである
       * config.ymlの必須要素が規定の文字数、文字種で定義されている
       * event.ymlが存在する場合、正しいymlである

     */

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

    // 必要最小限のファイルのプラグインのインストールとアンインストールを検証
    public function testInstallPluginMinimum()
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

        // インストールできるか
        $this->assertTrue($service->install($tmpfile));

        try{
            $service->install($tmpfile);
            $this->fail("checkSamePlugin dont throw exception.");
        }catch(\Eccube\Exception\PluginException $e){
        }catch(\Exception $e){
            $this->fail("checkSamePlugin throw unexpected exception.".$e->toString());
        }
        // 同じプラグインの二重インストールが蹴られるか

        // アンインストールできるか
        $this->assertTrue((boolean)$plugin=$this->app['eccube.repository.plugin']->findOneBy(array('code'=>$tmpname)));
        $this->assertEquals(Constant::DISABLED,$plugin->getEnable());
        $this->assertTrue($service->uninstall($plugin));


    }

    // 必須ファイルがないプラグインがインストール出来ないこと
    public function testInstallPluginEmptyError()
    {
        $this->setExpectedException(
          '\Eccube\Exception\PluginException', 'config.yml not found or syntax error'
        );
        $service = $this->app['eccube.service.plugin'];

        // インストールするプラグインを作成する
        $tmpname="dummy".sha1(mt_rand());
        $tmpdir=$this->createTempDir();
        $tmpfile=$tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('dummy','dummy');
        // インストールできるか
        $service->install($tmpfile);

    }

    // config.ymlのフォーマット確認
    public function testConfigYmlFormat()
    {
        $service = $this->app['eccube.service.plugin'];
        $tmpname='dummy'.mt_rand();
        $tmpfile=sys_get_temp_dir().'/dummy'.mt_rand();


        // 必須項目のチェック
        $config=array();
        #$config['name'] = $tmpname;
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;
        try{
            file_put_contents($tmpfile,Yaml::dump($config));
            $service->checkPluginArchiveContent($tmpfile);
            $this->fail("testConfigYmlFormat dont throw exception.");
        }catch(\Eccube\Exception\PluginException $e){ }

        $config=array();
        $config['name'] = $tmpname;
        #$config['code'] = $tmpname;
        $config['version'] = $tmpname;
        try{
            file_put_contents($tmpfile,Yaml::dump($config));
            $service->checkPluginArchiveContent($tmpfile);
            $this->fail("testConfigYmlFormat dont throw exception.");
        }catch(\Eccube\Exception\PluginException $e){ }

        $config=array();
        $config['name'] = $tmpname;
        $config['code'] = $tmpname;
        #$config['version'] = $tmpname;
        try{
            file_put_contents($tmpfile,Yaml::dump($config));
            $service->checkPluginArchiveContent($tmpfile);
            $this->fail("testConfigYmlFormat dont throw exception.");
        }catch(\Eccube\Exception\PluginException $e){ }

        // 禁止文字のチェック

        $config['name'] = $tmpname."@";
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;
        try{
            file_put_contents($tmpfile,Yaml::dump($config));
            $service->checkPluginArchiveContent($tmpfile);
            $this->fail("testConfigYmlFormat dont throw exception.");
        }catch(\Eccube\Exception\PluginException $e){ }

        $config=array();
        $config['name'] = $tmpname;
        $config['code'] = $tmpname."#";
        $config['version'] = $tmpname;
        try{
            file_put_contents($tmpfile,Yaml::dump($config));
            $service->checkPluginArchiveContent($tmpfile);
            $this->fail("testConfigYmlFormat dont throw exception.");
        }catch(\Eccube\Exception\PluginException $e){ }

        // 長さのチェック
        $config=array();
        $config['name'] = $tmpname;
        $config['code'] = $tmpname;
        $config['version'] = str_repeat('a',256);
        try{
            file_put_contents($tmpfile,Yaml::dump($config));
            $service->checkPluginArchiveContent($tmpfile);
            $this->fail("testConfigYmlFormat dont throw exception.");
        }catch(\Eccube\Exception\PluginException $e){ }

        $config=array();
        $config['name'] = $tmpname;
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;
        $config['event'] = "&".$tmpname;
        try{
            file_put_contents($tmpfile,Yaml::dump($config));
            $service->checkPluginArchiveContent($tmpfile);
            $this->fail("testConfigYmlFormat dont throw exception.");
        }catch(\Eccube\Exception\PluginException $e){ }
    }

    // config.ymlに異常な項目がある場合
    public function testnstallPluginMalformedConfigError()
    {
        $service = $this->app['eccube.service.plugin'];
        $tmpdir=$this->createTempDir();
        $tmpfile=$tmpdir.'/plugin.tar';
        $tar = new \PharData($tmpfile);

        // インストールするプラグインを作成する
        $tmpname="dummy".sha1(mt_rand());
        $config=array();
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;
        $tar->addFromString('config.yml',Yaml::dump($config));

        $this->setExpectedException(
          '\Eccube\Exception\PluginException', 'config.yml name empty'
        );
        // インストールできないはず
        $this->assertNull($service->install($tmpfile));
    }

    // イベント定義を含むプラグインのインストールとアンインストールを検証
    public function testInstallPluginWithEvent()
    {
        // インストールするプラグインを作成する
        $tmpname="dummy".sha1(mt_rand());
        $config=array();
        $config['name'] = $tmpname."_name";
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;
        $config['event'] = 'DummyEvent';

        $tmpdir=$this->createTempDir();
        $tmpfile=$tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml',Yaml::dump($config));


        $dummyEvent=<<<'EOD'
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
        $dummyEvent=str_replace('@@@@',$tmpname,$dummyEvent); // イベントクラス名はランダムなのでヒアドキュメントの@@@@部分を置換
        $tar->addFromString("DummyEvent.php" , $dummyEvent);

        // イベント定義を作成する
        $event=array();
        $event['eccube.event.app.before'] = array();
        $event['eccube.event.app.before'][] = array("dummyHandler",'NORMAL');
        $event['eccube.event.app.before'][] = array("dummyHandlerFirst",'FIRST');
        $event['eccube.event.app.after'] = array();
        $event['eccube.event.app.after'][] = array("dummyHandlerLast",'LAST');
        $tar->addFromString('event.yml',Yaml::dump($event));

        $service = $this->app['eccube.service.plugin'];

        // インストールできるか
        $this->assertTrue($service->install($tmpfile));
        $rep= $this->app['eccube.repository.plugin'];

        $plugin=$rep->findOneBy(array('code'=>$tmpname)); // EntityManagerの内部状態を一旦クリア // associationがうまく取れないため
        $this->app['orm.em']->detach($plugin);


        // インストールした内容は正しいか
        // config.ymlとdtb_pluginの内容を照合
        $this->assertTrue((boolean)$plugin=$rep->findOneBy(array('code'=>$tmpname)));
        $this->assertEquals($plugin->getClassName(),"DummyEvent");
        $this->assertEquals($plugin->getName(),$tmpname."_name");
        $this->assertEquals($plugin->getVersion(),$tmpname);

        // event.ymlとdtb_plugin_event_handlerの内容を照合(優先度、ハンドラメソッド名、イベント名)
        $this->assertEquals(3,count($plugin->getPluginEventHandlers()->toArray()));

        foreach($plugin->getPluginEventHandlers() as $handler){
            if($handler->getHandlerType()==\Eccube\Entity\PluginEventHandler::EVENT_HANDLER_TYPE_NORMAL){
                $this->assertGreaterThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_NORMAL_END,$handler->getPriority() );
                $this->assertLessThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_NORMAL_START,$handler->getPriority() );
                $this->assertEquals('dummyHandler',$handler->getHandler());
                $this->assertEquals('eccube.event.app.before',$handler->getEvent());
            }
            if($handler->getHandlerType()==\Eccube\Entity\PluginEventHandler::EVENT_HANDLER_TYPE_FIRST){
                $this->assertGreaterThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_FIRST_END,$handler->getPriority() );
                $this->assertLessThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_FIRST_START,$handler->getPriority() );
                $this->assertEquals('dummyHandlerFirst',$handler->getHandler());
                $this->assertEquals('eccube.event.app.before',$handler->getEvent());

            }
            if($handler->getHandlerType()==\Eccube\Entity\PluginEventHandler::EVENT_HANDLER_TYPE_LAST){
                $this->assertGreaterThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_LAST_END,$handler->getPriority() );
                $this->assertLessThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_LAST_START,$handler->getPriority() );
                $this->assertEquals('dummyHandlerLast',$handler->getHandler());
                $this->assertEquals('eccube.event.app.after',$handler->getEvent());
            }
        }

        // ちゃんとファイルが展開されているか
        $this->assertFileExists(__DIR__."/../../../../app/Plugin/$tmpname/config.yml");
        $this->assertFileExists(__DIR__."/../../../../app/Plugin/$tmpname/event.yml");
        $this->assertFileExists(__DIR__."/../../../../app/Plugin/$tmpname/DummyEvent.php");

        // enable/disableできるか
        $this->assertTrue($service->disable($plugin));
        $this->assertTrue($service->enable($plugin));

        // イベント定義を更新する
        $event=array();
        $event['eccube.event.controller.cart.after'] = array();
        $event['eccube.event.controller.cart.after'][] = array("dummyCartHandlerLast",'LAST');
        $event['eccube.event.app.before'] = array();
        $event['eccube.event.app.before'][] = array("dummyHandler",'NORMAL');
        $event['eccube.event.app.after'] = array();
        $event['eccube.event.app.after'][] = array("dummyHandlerLast",'LAST');
        $tar->addFromString('event.yml',Yaml::dump($event));

        // config.ymlを更新する
        $config=array();
        $config['name'] = $tmpname."_name";
        $config['code'] = $tmpname;
        $config['version'] = $tmpname."u";
        $config['event'] = 'DummyEvent';
        $tar->addFromString('config.yml',Yaml::dump($config));

        $tar->addFromString('update_dummy',"update dummy");

        // updateできるか
        $this->assertTrue($service->update($plugin,$tmpfile));
        $this->assertEquals($plugin->getVersion(),$tmpname."u");

        // イベントハンドラが新しいevent.ymlと整合しているか(追加、削除)
        $this->app['orm.em']->detach($plugin);
        $this->assertTrue((boolean)$plugin=$rep->findOneBy(array('code'=>$tmpname)));
        $this->assertEquals(3,count($plugin->getPluginEventHandlers()->toArray()));

        foreach($plugin->getPluginEventHandlers() as $handler){
            if($handler->getHandlerType()==\Eccube\Entity\PluginEventHandler::EVENT_HANDLER_TYPE_NORMAL){
                $this->assertGreaterThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_NORMAL_END,$handler->getPriority() );
                $this->assertLessThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_NORMAL_START,$handler->getPriority() );
                $this->assertEquals('dummyHandler',$handler->getHandler());
                $this->assertEquals('eccube.event.app.before',$handler->getEvent());
            }
            if($handler->getHandlerType()==\Eccube\Entity\PluginEventHandler::EVENT_HANDLER_TYPE_LAST){
                $this->assertGreaterThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_LAST_END,$handler->getPriority() );
                $this->assertLessThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_LAST_START,$handler->getPriority() );
                $this->assertContains($handler->getHandler(), array('dummyHandlerLast','dummyCartHandlerLast'));
                $this->assertContains($handler->getEvent(),array('eccube.event.app.after','eccube.event.controller.cart.after') );
            }
        }
        // 追加されたファイルが配置されているか
        $this->assertFileExists(__DIR__."/../../../../app/Plugin/$tmpname/update_dummy");

        // アンインストールできるか
        $this->assertTrue($service->uninstall($plugin));
        // ちゃんとファイルが消えているか
        $this->assertFalse((boolean)$rep->findOneBy(array('name'=>$tmpname,'enable'=>1)));
        $this->assertFileNotExists(__DIR__."/../../../../app/Plugin/$tmpname/config.yml");
        $this->assertFileNotExists(__DIR__."/../../../../app/Plugin/$tmpname/event.yml");
        $this->assertFileNotExists(__DIR__."/../../../../app/Plugin/$tmpname/DummyEvent.php");
    }

    // インストーラが例外を上げた場合ロールバックできるか
    public function testInstallPluginWithBrokenManagerAfterInstall()
    {
        // インストールするプラグインを作成する
        $tmpname="dummy".sha1(mt_rand());
        $config=array();
        $config['name'] = $tmpname;
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;

        $tmpdir=$this->createTempDir();
        $tmpfile=$tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml',Yaml::dump($config));
        $dummyManager=<<<'EOD'
<?php
namespace Plugin\@@@@ ;

use Eccube\Plugin\AbstractPluginManager;
class PluginManager extends AbstractPluginManager
{
    public function install($plugin,$app)
    {
        echo "";
    }
    public function uninstall($config,$app)
    {
        throw new \Exception('hoge',1);
    }
    public function enable($config,$app)
    {
        throw new \Exception('hoge',1);
    }
    public function disable($config,$app)
    {
        throw new \Exception('hoge',1);
    }
    public function update($config,$app)
    {
        throw new \Exception('hoge',1);
    }

}

EOD;
        $dummyManager=str_replace('@@@@',$tmpname,$dummyManager); // イベントクラス名はランダムなのでヒアドキュメントの@@@@部分を置換
        $tar->addFromString("PluginManager.php" , $dummyManager);
        $service = $this->app['eccube.service.plugin'];

        // 正しくインストールでき、enableのハンドラが呼ばれないことを確認
        $this->assertTrue($service->install($tmpfile));
        $this->assertTrue((boolean)$plugin=$this->app['eccube.repository.plugin']->findOneBy(array('name'=>$tmpname)));
        $this->assertEquals(Constant::DISABLED,$plugin->getEnable()); // インストール直後にプラグインがdisableになっているか
        try{
            $this->assertTrue($service->enable($plugin));// enableにしようとするが、例外発生
        }catch(\Exception $e){ }
        $this->app['orm.em']->detach($plugin);
        $this->assertTrue((boolean)$plugin=$this->app['eccube.repository.plugin']->findOneBy(array('name'=>$tmpname)));
        $this->assertEquals(Constant::DISABLED,$plugin->getEnable()); // プラグインがdisableのままになっていることを確認

    }

    // インストーラを含むプラグインが正しくインストールできるか
    public function testInstallPluginWithManager()
    {
        // インストールするプラグインを作成する
        $tmpname="dummy".sha1(mt_rand());
        $config=array();
        $config['name'] = $tmpname;
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;

        $tmpdir=$this->createTempDir();
        $tmpfile=$tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml',Yaml::dump($config));
        $dummyManager=<<<'EOD'
<?php
namespace Plugin\@@@@ ;

use Eccube\Plugin\AbstractPluginManager;
class PluginManager extends AbstractPluginManager
{
    public function install($plugin,$app)
    {
        echo "Installed";
    }

    public function uninstall($config,$app)
    {
        echo "Uninstalled";
    }

    public function enable($config,$app)
    {
        echo "Enabled";
    }

    public function disable($config,$app)
    {
        echo "Disabled";
    }

    public function update($config,$app)
    {
        echo "Updated";
    }
}

EOD;
        $dummyManager=str_replace('@@@@',$tmpname,$dummyManager); // イベントクラス名はランダムなのでヒアドキュメントの@@@@部分を置換
        $tar->addFromString("PluginManager.php" , $dummyManager);
        $service = $this->app['eccube.service.plugin'];

        // インストールできるか、インストーラが呼ばれるか
        ob_start();
        $this->assertTrue($service->install($tmpfile));
        $this->assertRegexp('/Installed/',ob_get_contents()); ob_end_clean();
        $this->assertFileExists(__DIR__."/../../../../app/Plugin/$tmpname/PluginManager.php");


        $this->assertTrue((boolean)$plugin=$this->app['eccube.repository.plugin']->findOneBy(array('name'=>$tmpname)));

        ob_start();
        $service->enable($plugin);
        $this->assertRegexp('/Enabled/',ob_get_contents()); ob_end_clean();
        ob_start();
        $service->disable($plugin);
        $this->assertRegexp('/Disabled/',ob_get_contents()); ob_end_clean();


        // アンインストールできるか、アンインストーラが呼ばれるか
        ob_start();
        $service->disable($plugin);
        $this->assertTrue($service->uninstall($plugin));
        $this->assertRegexp('/DisabledUninstalled/',ob_get_contents()); ob_end_clean();
    }



    // const定義を含むpluginのインストール
    public function testInstallPluginWithConst()
    {
        // インストールするプラグインを作成する
        $tmpname="dummy".sha1(mt_rand());
        $config=array();
        $config['name'] = $tmpname."_name";
        $config['code'] = $tmpname;
        $config['version'] = $tmpname."_version";
        $config['const']['A'] = 'A';
        $config['const']['C'] =  1;

        $tmpdir=$this->createTempDir();
        $tmpfile=$tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml',Yaml::dump($config));
        $service = $this->app['eccube.service.plugin'];

        // インストールできるか
        $this->assertTrue($service->install($tmpfile));

        $this->assertTrue((boolean)$plugin=$this->app['eccube.repository.plugin']->findOneBy(array('code'=>$tmpname)));

        // インストール後disable状態でもconstがロードされているか
        $config = $this->app['config'];
        $config[$tmpname]['const']['A'] = null;
        $config[$tmpname]['const']['C'] = null;
        // const が存在しないのを確認後, 再ロード
        $this->assertFalse(isset($this->app['config'][$tmpname]['const']['A']));
        $this->assertFalse(isset($this->app['config'][$tmpname]['const']['C']));

        $this->app->initPluginEventDispatcher();
        $this->app->loadPlugin();
        $this->app->boot();
        $this->assertEquals('A',$this->app['config'][$tmpname]['const']['A']);
        $this->assertEquals('1',$this->app['config'][$tmpname]['const']['C']);

        // アンインストールできるか
        $this->assertTrue($service->uninstall($plugin));
    }

    /**
     * プラグイン設定ファイルキャッシュの検証
     */
    public function testPluginConfigCache()
    {
        $this->app['debug'] = false;
        $pluginConfigCache = $this->app['config']['plugin_temp_realdir'].'/config_cache.php';

        // 事前にキャッシュを削除しておく
        if (file_exists($pluginConfigCache)) {
            unlink($pluginConfigCache);
        }

        // インストールするプラグインを作成する
        $tmpname="dummy".sha1(mt_rand());
        $config=array();
        $config['name'] = $tmpname."_name";
        $config['code'] = $tmpname;
        $config['version'] = $tmpname."_version";
        $config['const']['A'] = 'A';
        $config['const']['C'] =  1;

        $event = array (
            'eccube.event.app.request' =>
            array (
                0 =>
                array (
                    0 => 'onAppRequest',
                    1 => 'NORMAL',
                ),
            )
        );

        $tmpdir=$this->createTempDir();
        $tmpfile=$tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml', Yaml::dump($config));
        $tar->addFromString('event.yml', Yaml::dump($event));
        $service = $this->app['eccube.service.plugin'];

        // インストールできるか
        $this->assertTrue($service->install($tmpfile));

        $this->assertTrue((boolean)$plugin=$this->app['eccube.repository.plugin']->findOneBy(array('code'=>$tmpname)));

        $this->expected = $pluginConfigCache;
        $this->actual = $this->app->getPluginConfigCacheFile();
        $this->verify('キャッシュファイル名が一致するか');

        $pluginConfigs = $this->app->parsePluginConfigs();
        $this->assertTrue(array_key_exists($tmpname, $pluginConfigs));
        $this->expected = $config;
        $this->actual = $pluginConfigs[$tmpname]['config'];
        $this->verify('parsePluginConfigs の結果が一致するか');

        $this->assertTrue(false !== $this->app->writePluginConfigCache(), 'キャッシュファイルが書き込まれるか');

        $this->assertTrue(file_exists($pluginConfigCache), 'キャッシュファイルが存在するか');

        $this->assertTrue($this->app->removePluginConfigCache(), 'キャッシュファイルを削除できるか');

        $this->assertFalse(file_exists($pluginConfigCache), 'キャッシュファイルが削除されているか');

        $pluginConfigs = $this->app->getPluginConfigAll();

        $this->assertTrue(file_exists($pluginConfigCache), 'キャッシュファイルが再生成されているか');

        $this->expected = $config;
        $this->actual = $pluginConfigs[$tmpname]['config'];
        $this->verify('getPluginConfigAll の結果が一致するか');


        // インストール後disable状態でもconstがロードされているか
        $config = $this->app['config'];
        $config[$tmpname]['const']['A'] = null;
        $config[$tmpname]['const']['C'] = null;
        // const が存在しないのを確認後, 再ロード
        $this->assertFalse(isset($this->app['config'][$tmpname]['const']['A']));
        $this->assertFalse(isset($this->app['config'][$tmpname]['const']['C']));

        $this->app->initPluginEventDispatcher();
        $this->app->loadPlugin();
        $this->app->boot();
        $this->assertEquals('A',$this->app['config'][$tmpname]['const']['A']);
        $this->assertEquals('1',$this->app['config'][$tmpname]['const']['C']);

        // アンインストールできるか
        $this->assertTrue($service->uninstall($plugin));

        $pluginConfigs = $this->app->getPluginConfigAll();
        $this->assertFalse(array_key_exists($tmpname, $pluginConfigs), 'キャッシュからプラグインが削除されているか');
    }
}
