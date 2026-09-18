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

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Eccube\DependencyInjection\Compiler\StripAutoMappedEntityPathsPass;
use Eccube\Doctrine\ORM\Mapping\Driver\TraitProxyAttributeDriver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Filesystem;

/**
 * doctrine.orm.auto_mapping が生成する素の AttributeDriver から、EC-CUBE が
 * TraitProxyAttributeDriver で明示登録している Entity ディレクトリが取り除かれることの回帰テスト.
 *
 * 素のドライバは ColocatedMappingDriver::getAllClassNames() で Entity ソースを無条件に
 * require_once するため、Kernel::loadEntityProxies() が Proxy をロード済みの状態では
 * "Cannot redeclare class" で fatal になる.
 *
 * DoctrineBundle の実挙動 (バンドル横断で 1 つの AttributeDriver に paths を集約し、
 * prefix ごとに MappingDriverChain::addDriver する) を模したコンテナで検証する.
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6979
 */
final class StripAutoMappedEntityPathsPassTest extends TestCase
{
    private const DRIVER_ID = 'doctrine.orm.default_attribute_metadata_driver';

    private const CHAIN_ID = 'doctrine.orm.default_metadata_driver';

    private string $projectDir;

    private string $coreEntityDir;

    private string $pluginEntityDir;

    private string $vendorEntityDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projectDir = sys_get_temp_dir().'/strip_auto_mapped_entity_paths_'.uniqid('', true);
        $this->coreEntityDir = $this->projectDir.'/src/Eccube/Entity';
        $this->pluginEntityDir = $this->projectDir.'/app/Plugin/Foo/Entity';
        $this->vendorEntityDir = $this->projectDir.'/vendor/acme/extra-bundle/src/Entity';

        (new Filesystem())->mkdir([$this->coreEntityDir, $this->pluginEntityDir, $this->vendorEntityDir]);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->projectDir);

        parent::tearDown();
    }

    /**
     * 明示登録済みのパスだけが取り除かれ、第三者バンドルのパスは残ること.
     */
    public function testExplicitlyMappedPathsAreStripped(): void
    {
        $container = $this->createContainer([
            'Eccube\\Entity' => $this->coreEntityDir,
            'Plugin\\Foo\\Entity' => $this->pluginEntityDir,
            'Acme\\ExtraBundle\\Entity' => $this->vendorEntityDir,
        ]);

        $this->process($container);

        $this->assertSame(
            [$this->vendorEntityDir],
            $container->getDefinition(self::DRIVER_ID)->getArgument(0),
            '明示登録済みの Entity ディレクトリは素の AttributeDriver から取り除かれる'
        );
    }

    /**
     * パスが残る場合は MappingDriverChain への登録を維持すること.
     */
    public function testDriverStaysInChainWhenPathsRemain(): void
    {
        $container = $this->createContainer([
            'Eccube\\Entity' => $this->coreEntityDir,
            'Acme\\ExtraBundle\\Entity' => $this->vendorEntityDir,
        ]);

        $this->process($container);

        $this->assertSame(
            ['Eccube\\Entity', 'Acme\\ExtraBundle\\Entity'],
            $this->autoMappedPrefixesInChain($container),
            '第三者バンドルのマッピングを解決するため、素のドライバはチェーンに残す'
        );
    }

    /**
     * 全パスが明示登録済みになった素のドライバは MappingDriverChain から外すこと.
     *
     * paths が空のまま getAllClassNames() を呼ばれると
     * MappingException::pathRequiredForDriver で例外になるため.
     */
    public function testDriverIsRemovedFromChainWhenAllPathsAreStripped(): void
    {
        $container = $this->createContainer([
            'Eccube\\Entity' => $this->coreEntityDir,
            'Plugin\\Foo\\Entity' => $this->pluginEntityDir,
        ]);

        $this->process($container);

        $this->assertSame([], $container->getDefinition(self::DRIVER_ID)->getArgument(0));
        $this->assertSame([], $this->autoMappedPrefixesInChain($container));

        // EC-CUBE が明示登録した TraitProxyAttributeDriver の登録は残す
        $this->assertSame(
            ['Eccube\\Entity', 'Plugin\\Foo\\Entity'],
            $this->explicitlyMappedPrefixesInChain($container)
        );
    }

    /**
     * 明示登録側がコンテナパラメータ表記でも解決されること.
     */
    public function testParameterPlaceholderIsResolved(): void
    {
        $container = $this->createContainer([
            'Eccube\\Entity' => $this->coreEntityDir,
            'Acme\\ExtraBundle\\Entity' => $this->vendorEntityDir,
        ]);

        (new StripAutoMappedEntityPathsPass(['%kernel.project_dir%/src/Eccube/Entity']))->process($container);

        $this->assertSame([$this->vendorEntityDir], $container->getDefinition(self::DRIVER_ID)->getArgument(0));
    }

    /**
     * doctrine.orm.auto_mapping 由来でないドライバ定義は変更しないこと.
     */
    public function testUnrelatedDriverIsNotTouched(): void
    {
        $container = $this->createContainer([
            'Acme\\ExtraBundle\\Entity' => $this->vendorEntityDir,
        ]);
        $container->setDefinition(
            'acme.custom_metadata_driver',
            new Definition(AttributeDriver::class, [[$this->coreEntityDir]])
        );

        $this->process($container);

        $this->assertSame(
            [$this->coreEntityDir],
            $container->getDefinition('acme.custom_metadata_driver')->getArgument(0)
        );
    }

    /**
     * DoctrineBundle が生成するコンテナ構造を模して組み立てる.
     *
     * @param array<string, string> $autoMappedPaths prefix => Entity ディレクトリ
     */
    private function createContainer(array $autoMappedPaths): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.project_dir', $this->projectDir);

        // DoctrineExtension::registerMappingDrivers 相当:
        // 同じドライバ型のバンドルを 1 インスタンスに集約し、prefix ごとにチェーンへ登録する
        $container->setDefinition(
            self::DRIVER_ID,
            new Definition(AttributeDriver::class, [array_values($autoMappedPaths)])
        );

        $chain = new Definition(MappingDriverChain::class);
        foreach (array_keys($autoMappedPaths) as $prefix) {
            $chain->addMethodCall('addDriver', [new Reference(self::DRIVER_ID), $prefix]);
        }

        // Kernel::addEntityExtensionPass 相当: 明示登録は同じ prefix を後から上書きする
        foreach ($this->explicitlyMappedPaths() as $prefix => $path) {
            $chain->addMethodCall('addDriver', [new Definition(TraitProxyAttributeDriver::class, [[$path]]), $prefix]);
        }

        $container->setDefinition(self::CHAIN_ID, $chain);

        return $container;
    }

    private function process(ContainerBuilder $container): void
    {
        (new StripAutoMappedEntityPathsPass(array_values($this->explicitlyMappedPaths())))->process($container);
    }

    /**
     * @return array<string, string>
     */
    private function explicitlyMappedPaths(): array
    {
        return [
            'Eccube\\Entity' => $this->coreEntityDir,
            'Plugin\\Foo\\Entity' => $this->pluginEntityDir,
        ];
    }

    /**
     * 素の AttributeDriver がチェーンに登録されている prefix.
     *
     * @return list<string>
     */
    private function autoMappedPrefixesInChain(ContainerBuilder $container): array
    {
        $prefixes = [];
        foreach ($container->getDefinition(self::CHAIN_ID)->getMethodCalls() as [$method, $arguments]) {
            if ('addDriver' === $method && $arguments[0] instanceof Reference && self::DRIVER_ID === (string) $arguments[0]) {
                $prefixes[] = $arguments[1];
            }
        }

        return $prefixes;
    }

    /**
     * TraitProxyAttributeDriver がチェーンに登録されている prefix.
     *
     * @return list<string>
     */
    private function explicitlyMappedPrefixesInChain(ContainerBuilder $container): array
    {
        $prefixes = [];
        foreach ($container->getDefinition(self::CHAIN_ID)->getMethodCalls() as [$method, $arguments]) {
            if ('addDriver' === $method && $arguments[0] instanceof Definition
                && TraitProxyAttributeDriver::class === $arguments[0]->getClass()) {
                $prefixes[] = $arguments[1];
            }
        }

        return $prefixes;
    }
}
