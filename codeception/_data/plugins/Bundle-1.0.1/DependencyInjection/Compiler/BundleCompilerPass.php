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

namespace Plugin\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BundleCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $plugins = $container->getParameter('eccube.plugins.enabled');
        if (!in_array('Bundle', $plugins)) {
            if ($container->hasDefinition('League\Bundle\OAuth2ServerBundle\EventListener\AddClientDefaultScopesListener')) {
                $def = $container->getDefinition('League\Bundle\OAuth2ServerBundle\EventListener\AddClientDefaultScopesListener');
                $def->clearTags();
            }
        }
    }
}
