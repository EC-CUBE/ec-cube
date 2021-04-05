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

namespace Eccube\Tests\Repository;

use Eccube\Entity\Plugin;
use Eccube\Repository\PluginRepository;
use Eccube\Tests\EccubeTestCase;

class PluginRepositoryTest extends EccubeTestCase
{
    /**
     * @var PluginRepository
     */
    protected $pluginRepository;

    public function setUp()
    {
        parent::setUp();
        $this->pluginRepository = $this->entityManager->getRepository(Plugin::class);
        $this->cleanup();
    }

    public function testFindAllEnabled()
    {
        $Plugin1 = $this->createPlugin('Enable1');
        $Plugin1->setEnabled(true);
        $Plugin2 = $this->createPlugin('Enable2');
        $Plugin2->setEnabled(true);
        $Plugin3 = $this->createPlugin('Disable1');
        $Plugin3->setEnabled(false);
        $this->entityManager->flush();

        $Plugins = $this->pluginRepository->findAllEnabled();
        $this->assertCount(2, $Plugins);
        $this->assertEmpty(array_filter($Plugins, function ($Plugin) { return $Plugin->isEnabled() === false; }));
    }

    /**
     * @dataProvider dataFormCodeProvider
     */
    public function testFindByCode($code, $search, $isNotNull)
    {
        $Plugin1 = $this->createPlugin($code);
        $Plugin2 = $this->createPlugin('EnAblE2');
        $Plugin3 = $this->createPlugin('enable3');

        $Result = $this->pluginRepository->findByCode($search);
        if ($isNotNull) {
            $this->assertNotNull($Result);
        } else {
            $this->assertNull($Result);
        }
    }

    public function dataFormCodeProvider()
    {
        return [
            ['Enable1', 'Enable1', true],
            ['Enable1', 'EnAbLe1', true],
            ['Enable1', 'enable1', true],
            ['Enable1', 'disable1', false],
        ];
    }

    /**
     * @param string $code
     *
     * @return Plugin
     */
    private function createPlugin($code)
    {
        $faker = $this->getFaker();
        $Plugin = new Plugin();
        $Plugin->setCode($code)
            ->setName($faker->word)
            ->setVersion($faker->regexify('[0-9]\.[0-9]\.[0-9]'))
            ->setSource($faker->numberBetween(1000, 9999))
        ;

        $this->entityManager->persist($Plugin);
        $this->entityManager->flush();

        return $Plugin;
    }

    private function cleanup()
    {
        $Plugins = $this->pluginRepository->findAll();
        foreach ($Plugins as $Plugin) {
            $this->entityManager->remove($Plugin);
        }
        $this->entityManager->flush();
    }
}
