<?php

declare(strict_types=1);

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
use PHPUnit\Framework\Attributes\DataProvider;

final class PluginRepositoryTest extends EccubeTestCase
{
    protected ?PluginRepository $pluginRepository = null;

    protected function setUp(): void
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
        $this->assertEmpty(array_filter($Plugins, fn ($Plugin) => $Plugin->isEnabled() === false));
    }

    /**
     * @param mixed $code
     * @param mixed $search
     * @param mixed $isNotNull
     */
    #[DataProvider(methodName: 'dataFormCodeProvider')]
    public function testFindByCode($code, $search, $isNotNull)
    {
        $this->createPlugin($code);
        $this->createPlugin('EnAblE2');
        $this->createPlugin('enable3');

        $Result = $this->pluginRepository->findByCode($search);
        if ($isNotNull) {
            $this->assertInstanceOf(Plugin::class, $Result);
        } else {
            $this->assertNotInstanceOf(Plugin::class, $Result);
        }
    }

    public static function dataFormCodeProvider(): \Iterator
    {
        yield ['Enable1', 'Enable1', true];
        yield ['Enable1', 'EnAbLe1', true];
        yield ['Enable1', 'enable1', true];
        yield ['Enable1', 'disable1', false];
    }

    private function createPlugin(string $code): Plugin
    {
        $faker = $this->getFaker();
        $Plugin = new Plugin();
        $Plugin->setCode($code)
            ->setName($faker->word())
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
