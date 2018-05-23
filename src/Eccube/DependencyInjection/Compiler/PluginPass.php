<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PluginPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $plugins = $container->getParameter('eccube.plugins.disabled');

        if (empty($plugins)) {
            $container->log($this, 'disabled plugins not found.');

            return;
        }

        $definitions = $container->getDefinitions();

        foreach ($definitions as $definition) {
            $class = $definition->getClass();

            foreach ($plugins as $plugin) {
                $namespace = 'Plugin\\'.$plugin['code'];

                if (false !== \strpos($class, $namespace)) {
                    $definition->clearTags();
                }
            }
        }
    }
}
