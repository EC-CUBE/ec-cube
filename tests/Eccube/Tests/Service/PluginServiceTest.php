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
        $f=new Filesystem();
        return $f->remove($path);
    }


    // 必要最小限のファイルのプラグインのインストールとアンインストールを検証
    public function testInstallPluginMinimum()
    {
        #self::markTestSkipped();

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
        $service = $this->app['eccube.service.plugin']; 

        // インストールできるか
        $this->assertTrue($service->install($tmpfile));

        $this->setExpectedException(
          '\Eccube\Exception\PluginException', 'plugin already installed.'
        );
        // 同じプラグインの二重インストールが蹴られるか
        $service->install($tmpfile);

        // アンインストールできるか
        $this->assertTrue((boolean)$plugin=$this->app['eccube.repository.plugin']->findOneBy(array('name'=>$tmpname)));
        $this->assertTrue($service->uninstall($plugin));
        
    }

    // 必須ファイルがないプラグインがインストール出来ないこと
    public function testInstallPluginEmptyError()
    {
        #self::markTestSkipped();

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
#        self::markTestSkipped();
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
        #self::markTestSkipped();
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
          '\Eccube\Exception\PluginException', 'config.yml name  empty or invalid_character(\W)'
        );
        // インストールできないはず
        $this->assertNull($service->install($tmpfile));
    }

    // イベント定義を含むプラグインのインストールとアンインストールを検証
    public function testInstallPluginWithEvent()
    {
        #self::markTestSkipped();

        // インストールするプラグインを作成する
        $tmpname="dummy".sha1(mt_rand());
        $config=array();
        $config['name'] = $tmpname;
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
        $event['eccube.event.app.before'][] = array("dummyHandler",'NORMAL');
        $event['eccube.event.app.before'][] = array("dummyHandlerFirst",'FIRST');
        $event['eccube.event.app.after'] = array();
        $event['eccube.event.app.after'][] = array("dummyHandlerLast",'LAST');
        $tar->addFromString('event.yml',Yaml::dump($event));

        $service = $this->app['eccube.service.plugin']; 

        // インストールできるか
        $this->assertTrue($service->install($tmpfile));
        $rep= $this->app['eccube.repository.plugin'];

        $plugin=$rep->findOneBy(array('name'=>$tmpname)); // EntityManagerの内部状態を一旦クリア // associationがうまく取れないため
        $this->app['orm.em']->detach($plugin); 


        // インストールした内容は正しいか
        // config.ymlとdtb_pluginの内容を照合
        $this->assertTrue((boolean)$plugin=$rep->findOneBy(array('name'=>$tmpname)));
        $this->assertEquals($plugin->getClassName(),"DummyEvent");
        $this->assertEquals($plugin->getCode(),$tmpname);
        $this->assertEquals($plugin->getVersion(),$tmpname);

        // event.ymlとdtb_plugin_event_handlerの内容を照合(優先度、ハンドラメソッド名、イベント名)
        $this->assertEquals(4,count($plugin->getPluginEventHandlers()->toArray()));

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

        // アンインストールできるか
        $this->assertTrue($service->uninstall($plugin));
        // 正しくアンインストールされているか
        $this->assertFalse((boolean)$rep->findOneBy(array('name'=>$tmpname,'enable'=>1)));
        $this->assertFileNotExists(__DIR__."/../../../../app/Plugin/$tmpname/config.yml");
        $this->assertFileNotExists(__DIR__."/../../../../app/Plugin/$tmpname/event.yml");
        $this->assertFileNotExists(__DIR__."/../../../../app/Plugin/$tmpname/DummyEvent.php");
    }



     // インストーラを含むプラグインが正しくインストールできるか 
    public function testInstallPluginWithManager()
    {
        #self::markTestSkipped();


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
        $this->assertRegexp('/InstalledEnabled/',ob_get_contents()); ob_end_clean();
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




}
