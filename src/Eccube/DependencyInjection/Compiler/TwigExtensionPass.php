<?php

namespace Eccube\DependencyInjection\Compiler;


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
                [new Reference('Eccube\Twig\Extension\IgnoreRoutingNotFoundExtension')]
            );
        }
    }
}
