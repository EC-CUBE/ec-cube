<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Service;

use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use Symfony\Component\Yaml\Yaml;

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
    /**
     * @var PluginRepository
     */
    protected $pluginRepository;

    /**
     * @var PluginService
     */
    protected $pluginService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->pluginRepository = $this->entityManager->getRepository(\Eccube\Entity\Plugin::class);
        $this->pluginService = self::$container->get(PluginService::class);
    }

    // インストーラが例外を上げた場合ロールバックできるか
    public function testInstallPluginWithBrokenManager()
    {
        // インストールするプラグインを作成する
        $tmpname = 'dummy'.sha1(mt_rand());
        $config = [];
        $config['name'] = $tmpname;
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml', Yaml::dump($config));
        $dummyManager = <<<'EOD'
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
        $dummyManager = str_replace('@@@@', $tmpname, $dummyManager); // イベントクラス名はランダムなのでヒアドキュメントの@@@@部分を置換
        $tar->addFromString('PluginManager.php', $dummyManager);
        try {
            $this->assertTrue($this->pluginService->install($tmpfile));
            $this->fail('BrokenManager dont throw exception.');
        } catch (\Exception $e) {
        }

        // インストーラで例外発生時にテーブルやファイスシステム上にゴミが残らないか
        $this->assertFileNotExists(__DIR__."/../../../../app/Plugin/$tmpname");
        // XXX PHPUnit によってロールバックが遅延してしまうので, 検証できないが, 消えているはず
        $this->assertFalse((bool) $plugin = $this->pluginRepository->findOneBy(['name' => $tmpname]));
    }

    private function createTempDir()
    {
        $t = sys_get_temp_dir().'/plugintest.'.sha1(mt_rand());
        if (!mkdir($t)) {
            throw new \Exception("$t ".$php_errormsg);
        }

        return $t;
    }
}
