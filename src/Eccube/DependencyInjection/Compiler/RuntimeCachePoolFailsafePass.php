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

use Eccube\Cache\WriteFailsafeFilesystemAdapter;
use Eccube\Cache\WriteFailsafePhpFilesAdapter;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * ファイルへ書き込む cache pool を, 書き込めない場合に保存を諦める実装へ差し替える.
 *
 * 実行時キャッシュ (%eccube_runtime_dir%/pools) は Web サーバー所有 (レーン W) のため
 * CLI からは書き込めない. 既定のアダプタは保存に失敗するたびに警告を記録するので,
 * CLI を実行するたびに大量の警告が並ぶ.
 *
 * 書き込めるかどうかは実行時にしか分からない (コンパイル済みコンテナは Web と CLI で共有する)
 * ため, 判定はアダプタ側で行う. 本パスは差し替えのみを担う.
 */
class RuntimeCachePoolFailsafePass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        foreach (array_keys($container->findTaggedServiceIds('cache.pool')) as $id) {
            if (!$container->hasDefinition($id)) {
                continue;
            }

            $definition = $container->getDefinition($id);

            if ($this->isSystemCacheFactory($definition)) {
                $definition->setFactory([WriteFailsafePhpFilesAdapter::class, 'createSystemCache']);
                continue;
            }

            if (FilesystemAdapter::class === $this->resolveClass($container, $definition)) {
                $definition->setClass(WriteFailsafeFilesystemAdapter::class);
            }
        }
    }

    private function isSystemCacheFactory(Definition $definition): bool
    {
        $factory = $definition->getFactory();

        return \is_array($factory)
            && AbstractAdapter::class === $factory[0]
            && 'createSystemCache' === $factory[1];
    }

    /**
     * ChildDefinition はクラスを持たないことがあるため, 親をたどって解決する.
     */
    private function resolveClass(ContainerBuilder $container, Definition $definition): ?string
    {
        $class = $definition->getClass();
        while (null === $class && $definition instanceof ChildDefinition) {
            $parent = $definition->getParent();
            if (!$container->hasDefinition($parent)) {
                return null;
            }
            $definition = $container->getDefinition($parent);
            $class = $definition->getClass();
        }

        return null === $class ? null : (string) $container->getParameterBag()->resolveValue($class);
    }
}
