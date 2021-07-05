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

use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * サービスタグの自動設定を行う
 *
 * 以下のタグは自動設定が行われないため, 自動設定対象になるように処理する
 *
 * - doctrine.event_subscriber
 *
 * PluginPassで無効なプラグインのタグは解除されるため, PluginPassより先行して実行する必要がある
 */
class AutoConfigurationTagPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $definition) {
            $this->configureDoctrineEventSubscriberTag($definition);
        }
    }

    protected function configureDoctrineEventSubscriberTag(Definition $definition)
    {
        $class = $definition->getClass();
        if (!is_subclass_of($class, EventSubscriber::class)) {
            return;
        }

        if ($definition->hasTag('doctrine.event_subscriber')) {
            return;
        }

        $definition->addTag('doctrine.event_subscriber');
    }
}
