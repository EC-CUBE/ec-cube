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
            throw new \Exception("$t ".$php_errormsg);
        }
        return $t;
    }
    public function deleteFile($path)
    {
        $f=new Filesystem();
        return $f->remove($path);
    }


    public function testInstallPluginMinimum()
    {
        self::markTestSkipped();

        $tmpname="dummy".sha1(mt_rand());
        $config=array();
        $config['name'] = $tmpname;
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;

        $tmpdir=$this->createTempDir();
        $tmpfile=$tmpdir.'/plugin.tar';

        $tar = new \Archive_Tar($tmpfile, true);
        $tar->addString('config.yml',Yaml::dump($config));
        $service = $this->app['eccube.service.plugin']; 

        // インストールできるか
        $this->assertTrue($service->install($tmpfile));

        $this->setExpectedException(
          'Exception', 'plugin already installed.'
        );
        // 同じプラグインの二重インストールが蹴られるか
        $service->install($tmpfile);
        
    }

    public function testInstallPluginWithEvent()
    {
        self::markTestSkipped();
        $tmpname="dummy".sha1(mt_rand());
        $config=array();
        $config['name'] = $tmpname;
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;
        $config['event'] = 'DummyEvent';

        $tmpdir=$this->createTempDir();
        $tmpfile=$tmpdir.'/plugin.tar';

        $tar = new \Archive_Tar($tmpfile, true);
        $tar->addString('config.yml',Yaml::dump($config));


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
        echo "dummyHandler";
    }

}

EOD;
        $dummyEvent=str_replace('@@@@',$tmpname,$dummyEvent);
        $tar->addString("DummyEvent.php" , $dummyEvent);

        $event=array();
        $event['eccube.event.app.before'] = array();
        $event['eccube.event.app.before'][] = array("dummyHandler",'NORMAL');
        $event['eccube.event.app.before'][] = array("dummyHandler",'FIRST');
        $event['eccube.event.app.after'] = array();
        $event['eccube.event.app.before'][] = array("dummyHandler",'LAST');
        $tar->addString('event.yml',Yaml::dump($event));
         

        $service = $this->app['eccube.service.plugin']; 

        // インストールできるか
        $this->assertTrue($service->install($tmpfile));
        $rep=$this->app['orm.em']->getRepository('Eccube\Entity\Plugin'); 
        $plugin=$rep->findOneBy(array('name'=>$tmpname));

        $this->assertTrue((boolean)$plugin);
        $this->assertEquals($plugin->getClassName(),"DummyEvent");
        
    }


/*
    public function testUnInstallPlugin()
    {
    }

    public function testEnablePlugin()
    {
    }

    public function testDisablePlugin()
    {
    }
*/
}
