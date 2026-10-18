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

namespace Eccube\Tests\DependencyInjection\Resource;

use Eccube\DependencyInjection\EccubeExtension;
use Eccube\DependencyInjection\Resource\PluginStateResource;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PluginStateResourceTest extends KernelTestCase
{
    public function testToStateReturnsCodeToEnabledMapSortedByCode(): void
    {
        $state = PluginStateResource::toState([
            ['code' => 'Zebra', 'enabled' => 0],
            ['code' => 'Apple', 'enabled' => 1],
        ]);

        $this->assertSame(['Apple' => true, 'Zebra' => false], $state);
        // 比較でぶれないよう, コード順に並んでいること
        $this->assertSame(['Apple', 'Zebra'], array_keys($state));
    }

    public function testToStateSkipsRowsWithoutCode(): void
    {
        $this->assertSame([], PluginStateResource::toState([['enabled' => 1]]));
    }

    public function testIsFreshReturnsFalseWhenPluginStateChanged(): void
    {
        $current = PluginStateResource::currentState();
        if (null === $current) {
            $this->markTestSkipped('dtb_plugin を読めない環境のためスキップする');
        }

        // 現在の状態と同じなら新鮮
        $this->assertTrue((new PluginStateResource($current))->isFresh(time()));

        // 実在しないプラグインが有効だったことにすると, 状態が変わったとみなされる
        $this->assertFalse((new PluginStateResource($current + ['NotInstalled' => true]))->isFresh(time()));
    }

    public function testConfigurePluginsRegistersResourceSoContainerTracksPluginState(): void
    {
        if (null === PluginStateResource::currentState()) {
            $this->markTestSkipped('dtb_plugin を読めない環境のためスキップする');
        }

        $container = new ContainerBuilder();
        $container->setParameter('kernel.project_dir', static::getContainer()->getParameter('kernel.project_dir'));

        // prepend() は他にも多くの設定を読むため, 対象のメソッドだけを直接呼ぶ
        $extension = new EccubeExtension();
        $configurePlugins = new \ReflectionMethod($extension, 'configurePlugins');
        $configurePlugins->invoke($extension, $container);

        $resources = array_filter(
            $container->getResources(),
            static fn ($resource): bool => $resource instanceof PluginStateResource
        );

        $this->assertNotEmpty(
            $resources,
            'dtb_plugin の変化でコンテナが作り直されるよう, PluginStateResource が登録されている必要がある'
        );
    }
}
