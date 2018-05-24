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

use Eccube\Twig\Extension\IgnoreRoutingNotFoundExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TwigExtensionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // 本番時はtwigのurl(), path()を差し替える.
        if (!$container->getParameter('kernel.debug')) {
            $definition = $container->getDefinition('twig');
            $definition->addMethodCall(
                'addExtension',
                [new Reference(IgnoreRoutingNotFoundExtension::class)]
            );
        }
    }
}
