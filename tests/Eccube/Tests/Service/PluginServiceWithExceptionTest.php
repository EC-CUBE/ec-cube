<?php

namespace Eccube\Tests\Service;

use Eccube\Application;
use Symfony\Component\Yaml\Yaml;
use Eccube\Common\Constant;

/**
 * 例外系の PluginService テストケース.
 *
 * 通常は EccubeTestCase によって、 begin/rollback されるため、実装コード内での
 * rollback が検証できない。
 * このクラスは、 setUp()/tearDown() で begin/rollback しないようにし、
 * 実装コード内での rollback を検証する.
 */
class PluginServiceWithExceptionTest extends AbstractServiceTestCase
{
    private function createTempDir(){
        $t = sys_get_temp_dir()."/plugintest.".sha1(mt_rand());
        if(!mkdir($t)){
            throw new \Exception("$t ".$php_errormsg);
        }
        return $t;
    }

    public function setUp()
    {
        $this->app = $this->createApplication();
        // in the case of sqlite in-memory database only.
        if ($this->isSqliteInMemory()) {
            $this->initializeDatabase();
        }
    }

    public function tearDown()
    {
        if (!$this->isSqliteInMemory()) {
            $this->app['orm.em']->getConnection()->close();
        }
        $this->cleanUpProperties();
    }

    // インストーラが例外を上げた場合ロールバックできるか
    public function testInstallPluginWithBrokenManager()
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
        throw new \Exception('hoge',1);
    }

}

EOD;
        $dummyManager=str_replace('@@@@',$tmpname,$dummyManager); // イベントクラス名はランダムなのでヒアドキュメントの@@@@部分を置換
        $tar->addFromString("PluginManager.php" , $dummyManager);
        $service = $this->app['eccube.service.plugin'];
        try{

            $this->assertTrue($service->install($tmpfile));
            $this->fail("BrokenManager dont throw exception.");
        }catch(\Exception $e){ }

        // インストーラで例外発生時にテーブルやファイスシステム上にゴミが残らないか
        $this->assertFileNotExists(__DIR__."/../../../../app/Plugin/$tmpname");
        // XXX PHPUnit によってロールバックが遅延してしまうので, 検証できないが, 消えているはず
        $this->assertFalse((boolean)$plugin=$this->app['eccube.repository.plugin']->findOneBy(array('name'=>$tmpname)));
    }
}
