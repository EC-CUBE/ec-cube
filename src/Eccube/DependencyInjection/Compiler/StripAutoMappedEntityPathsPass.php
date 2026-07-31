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

namespace Eccube\DependencyInjection\Compiler;

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * doctrine.orm.auto_mapping が生成する素の AttributeDriver から、
 * Kernel::addEntityExtensionPass が TraitProxyAttributeDriver で明示登録している
 * Entity ディレクトリを取り除く.
 *
 * doctrine-bundle は auto_mapping 対象バンドルの Entity ディレクトリを
 * 「バンドルクラスが置かれたディレクトリ + /Entity」で検出し (DoctrineExtension::detectMetadataDriver)、
 * 同じドライバ型のバンドルをすべて 1 つの AttributeDriver インスタンスに集約する
 * (DoctrineExtension::registerMappingDrivers). そのため、EC-CUBE が明示登録している
 * ディレクトリ (src/Eccube/Entity, app/Customize/Entity, app/Plugin/<Code>/Entity) が
 * 素の AttributeDriver にも入り込む.
 *
 * 素のドライバは ColocatedMappingDriver::getAllClassNames() で Entity ソースを無条件に
 * require_once するため、Kernel::loadEntityProxies() が app/proxy/entity の Proxy を
 * 先にロードした状態では "Cannot redeclare class" で fatal になる
 * (Entity の if (!class_exists()) ガード全廃前は、そのガードが吸収していた).
 *
 * MappingDriverChain は名前空間ごとに 1 ドライバしか保持しないため、EC-CUBE の明示登録で
 * 上書きされたように見えるが、素のドライバが別の名前空間 (第三者バンドル) でチェーンに
 * 残っていると、その getAllClassNames() が自身の全パスを走査して同じ fatal を引き起こす.
 *
 * バンドル名を列挙する (doctrine.orm.mappings.<Bundle>: false) 方式では、サードパーティ製
 * プラグインが持ち込むバンドル名を事前に知ることができないため、コンパイル時にパスを
 * 取り除く方式とする.
 *
 * @see https://github.com/EC-CUBE/ec-cube/pull/6895 Entity の if(!class_exists()) ガード全廃
 * @see https://github.com/EC-CUBE/ec-cube/issues/6979
 */
final readonly class StripAutoMappedEntityPathsPass implements CompilerPassInterface
{
    /**
     * @param string[] $explicitlyMappedPaths TraitProxyAttributeDriver で明示登録している Entity ディレクトリ
     */
    public function __construct(private array $explicitlyMappedPaths)
    {
    }

    public function process(ContainerBuilder $container): void
    {
        $explicitlyMappedPaths = [];
        foreach ($this->explicitlyMappedPaths as $path) {
            $resolved = $this->resolvePath($container, $path);
            if (null !== $resolved) {
                $explicitlyMappedPaths[] = $resolved;
            }
        }

        if ([] === $explicitlyMappedPaths) {
            return;
        }

        foreach ($container->getDefinitions() as $id => $definition) {
            if (!$this->isAutoMappedAttributeDriver($container, $id, $definition)) {
                continue;
            }

            $arguments = $definition->getArguments();
            $paths = $arguments[0] ?? null;
            if (!\is_array($paths)) {
                continue;
            }

            $remaining = array_values(array_filter(
                $paths,
                fn ($path) => !\in_array($this->resolvePath($container, $path), $explicitlyMappedPaths, true)
            ));

            if (\count($remaining) === \count($paths)) {
                continue;
            }

            if ([] === $remaining) {
                // 担当パスがすべて明示登録済みになった素のドライバはチェーンから外す.
                // paths が空のまま getAllClassNames() を呼ばれると例外になるため.
                $this->removeFromDriverChains($container, $id);
            }

            $arguments[0] = $remaining;
            $definition->setArguments($arguments);
        }
    }

    /**
     * doctrine.orm.auto_mapping が生成する素の AttributeDriver か判定する.
     */
    private function isAutoMappedAttributeDriver(ContainerBuilder $container, string $id, Definition $definition): bool
    {
        if (!str_starts_with($id, 'doctrine.orm.')
            || !(str_ends_with($id, '_attribute_metadata_driver')
                || str_ends_with($id, '_attribute_metadata_driver.inner'))
        ) {
            return false;
        }

        $class = $definition->getClass();
        if (!\is_string($class)) {
            return false;
        }

        $class = $container->getParameterBag()->resolveValue($class);

        return \is_string($class) && is_a($class, AttributeDriver::class, true);
    }

    /**
     * 指定したドライバサービスへの addDriver() 呼び出しを MappingDriverChain から取り除く.
     */
    private function removeFromDriverChains(ContainerBuilder $container, string $driverId): void
    {
        foreach ($container->getDefinitions() as $definition) {
            $methodCalls = $definition->getMethodCalls();
            $remaining = array_values(array_filter(
                $methodCalls,
                function (array $call) use ($driverId) {
                    if ('addDriver' !== $call[0]) {
                        return true;
                    }

                    $driver = $call[1][0] ?? null;

                    return !($driver instanceof Reference && $driverId === (string) $driver);
                }
            ));

            if (\count($remaining) !== \count($methodCalls)) {
                $definition->setMethodCalls($remaining);
            }
        }
    }

    /**
     * パスをコンテナパラメータ解決 + realpath で正規化する.
     */
    private function resolvePath(ContainerBuilder $container, mixed $path): ?string
    {
        if (!\is_string($path)) {
            return null;
        }

        $resolved = $container->getParameterBag()->resolveValue($path);
        if (!\is_string($resolved)) {
            return null;
        }

        return realpath($resolved) ?: null;
    }
}
