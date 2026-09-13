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

namespace Eccube\Tests\DependencyInjection\Compiler;

use Eccube\Cache\WriteFailsafeFilesystemAdapter;
use Eccube\Cache\WriteFailsafePhpFilesAdapter;
use Eccube\DependencyInjection\Compiler\RuntimeCachePoolFailsafePass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class RuntimeCachePoolFailsafePassTest extends TestCase
{
    public function testReplacesFilesystemAdapterPool(): void
    {
        $container = new ContainerBuilder();
        $definition = new Definition(FilesystemAdapter::class, ['ns', 0, '/var/www/html/var/runtime/prod/pools/app']);
        $definition->addTag('cache.pool');
        $container->setDefinition('cache.app', $definition);

        (new RuntimeCachePoolFailsafePass())->process($container);

        $this->assertSame(WriteFailsafeFilesystemAdapter::class, $container->getDefinition('cache.app')->getClass());
    }

    public function testReplacesSystemCacheFactory(): void
    {
        $container = new ContainerBuilder();
        $definition = new Definition(FilesystemAdapter::class);
        $definition->setFactory([AbstractAdapter::class, 'createSystemCache']);
        $definition->addTag('cache.pool');
        $container->setDefinition('cache.system', $definition);

        (new RuntimeCachePoolFailsafePass())->process($container);

        $factory = $container->getDefinition('cache.system')->getFactory();

        $this->assertIsArray($factory);
        $this->assertSame(WriteFailsafePhpFilesAdapter::class, $factory[0]);
        $this->assertSame('createSystemCache', $factory[1]);
    }

    /**
     * ChildDefinition はクラスを持たないため, 親をたどって判定する.
     */
    public function testResolvesClassThroughParentDefinition(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('cache.adapter.filesystem', new Definition(FilesystemAdapter::class));
        $child = new ChildDefinition('cache.adapter.filesystem');
        $child->addTag('cache.pool');
        $container->setDefinition('doctrine.app_cache_pool', $child);

        (new RuntimeCachePoolFailsafePass())->process($container);

        $this->assertSame(
            WriteFailsafeFilesystemAdapter::class,
            $container->getDefinition('doctrine.app_cache_pool')->getClass()
        );
    }

    public function testKeepsOtherAdapters(): void
    {
        $container = new ContainerBuilder();
        $definition = new Definition(ArrayAdapter::class);
        $definition->addTag('cache.pool');
        $container->setDefinition('cache.array', $definition);

        (new RuntimeCachePoolFailsafePass())->process($container);

        $this->assertSame(ArrayAdapter::class, $container->getDefinition('cache.array')->getClass());
    }
}
