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
 * プラグインのコンポーネント定義を制御するクラス.
 */
class PluginPass implements CompilerPassInterface
{
    /**
     * プラグインのコンポーネント定義を制御する.
     *
     * 無効状態のプラグインに対し, 付与されているサービスタグをクリアすることで,
     * プラグインが作成しているEventListener等の拡張機構が呼び出されないようにする.
     *
     * サービスタグが収集されるタイミング(一般的にPassConfig::TYPE_BEFORE_OPTIMIZATIONの0)より先に実行される必要があります.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // 無効状態のプラグインコード一覧を取得.
        // 無効なプラグインの一覧はEccubeExtensionで定義している.
        $plugins = $container->getParameter('eccube.plugins.disabled');

        if (empty($plugins)) {
            $container->log($this, 'disabled plugins not found.');

            return;
        }

        $definitions = $container->getDefinitions();

        foreach ($definitions as $definition) {
            $class = $definition->getClass();

            foreach ($plugins as $plugin) {
                $namespace = 'Plugin\\'.$plugin.'\\';

                if (false !== \strpos($class, $namespace)) {
                    foreach ($definition->getTags() as $tag => $attr) {
                        // PluginManagerからレポジトリを取得する場合があるため,
                        // doctrine.repository_serviceタグはスキップする.
                        if ($tag === 'doctrine.repository_service') {
                            continue;
                        }
                        $definition->clearTag($tag);
                    }
                }
            }
        }
    }
}
