<?php

namespace Eccube\Tests\Repository;

use Eccube\Application;
use Eccube\Entity\PluginOption;
use Eccube\Tests\EccubeTestCase;

class PluginOptionRepositoryTest extends EccubeTestCase
{
    public function testSave()
    {
        $PluginOption = $this->createPluginOption();

        $this->app['eccube.repository.plugin_option']->save($PluginOption);

        $count = $this->app['orm.em']->createQueryBuilder()
            ->select('COUNT(po.id)')
            ->from('Eccube\Entity\PluginOption', 'po')
            ->getQuery()
            ->getSingleScalarResult();

        $this->expected = 1;
        $this->actual = $count;
        $this->verify();
    }

    public function testGetOneByCodeAndKey()
    {
        $PluginOption = $this->createPluginOption();
        $this->app['eccube.repository.plugin_option']->save($PluginOption);

        $actual = $this->app['eccube.repository.plugin_option']->getOneByCodeAndKey(
            'code',
            'key'
        );

        $this->expected = $PluginOption;
        $this->actual = $actual;
        $this->verify();
    }

    public function testGetOneByCodeAndKeyNullResult()
    {
        $actual = $this->app['eccube.repository.plugin_option']->getOneByCodeAndKey(
            'xxx',
            'xxx'
        );

        $this->assertNull($actual);
    }

    protected function createPluginOption()
    {
        $PluginOption = new PluginOption();
        $PluginOption->setPluginCode('code')
            ->setOptionKey('key')
            ->setOptionValue('value');

        return $PluginOption;
    }
}
