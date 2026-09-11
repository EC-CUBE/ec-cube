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

namespace Eccube\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * リクエスト処理中に書き込まれるキャッシュの出力先を %eccube_runtime_dir% へ寄せる.
 *
 * kernel.cache_dir / kernel.build_dir は eccube:cache:build (CLI) が生成するディレクトリで,
 * Web サーバーからは読み取り専用で運用できるようにする. そのため, サービス定義に
 * %kernel.cache_dir% がハードコードされていて設定で変更できないものをここで差し替える.
 *
 * 設定で変更できるもの (framework.translator.cache_dir, exercise_html_purifier の
 * default_cache_serializer_path, mcp.http.session.directory 等) は yaml 側で指定する.
 */
class RuntimeCacheDirPass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        $runtimeDir = (string) $container->getParameter('eccube_runtime_dir');

        // cache.adapter.system のディレクトリは framework.cache.directory の対象外で,
        // cache.php:87 が %kernel.cache_dir%/pools/system をハードコードしている.
        // (cache.app 側は framework.cache.directory の既定値が %kernel.share_dir% 起点のため
        //  Kernel::getShareDir() の変更で追従する)
        if ($container->hasDefinition('cache.adapter.system')) {
            $container->getDefinition('cache.adapter.system')
                ->replaceArgument(3, $runtimeDir.'/pools/system');
        }

        $this->moveTwigCache($container, $runtimeDir);
    }

    /**
     * twig のコンパイル結果のうち, リクエスト処理中に書き込まれる側を移す.
     *
     * - prod: twig.template_cache.runtime_cache (build に無いテンプレートのフォールバック先)
     * - dev : auto_reload が有効なため 3 層構成にならず, twig サービスの cache オプションが
     *         %kernel.cache_dir%/twig という文字列になる (TwigExtension.php:172-177)
     */
    private function moveTwigCache(ContainerBuilder $container, string $runtimeDir): void
    {
        if ($container->hasDefinition('twig.template_cache.runtime_cache')) {
            $container->getDefinition('twig.template_cache.runtime_cache')
                ->replaceArgument(0, $runtimeDir.'/twig');
        }

        if (!$container->hasDefinition('twig')) {
            return;
        }

        $definition = $container->getDefinition('twig');
        $options = $definition->getArgument(1);
        if (!is_array($options) || !isset($options['cache']) || !is_string($options['cache'])) {
            return;
        }

        $options['cache'] = $runtimeDir.'/twig';
        $definition->replaceArgument(1, $options);
    }
}
