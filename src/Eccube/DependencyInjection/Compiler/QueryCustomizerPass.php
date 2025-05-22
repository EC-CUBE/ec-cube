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

use Eccube\Doctrine\Query\Queries;
use Eccube\Doctrine\Query\QueryCustomizer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class QueryCustomizerPass implements CompilerPassInterface
{
    public const QUERY_CUSTOMIZER_TAG = 'eccube.query_customizer';

    public function process(ContainerBuilder $container)
    {
        $queries = $container->getDefinition(Queries::class);
        $ids = $container->findTaggedServiceIds(self::QUERY_CUSTOMIZER_TAG);

        foreach ($ids as $id => $tags) {
            $def = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($def->getClass());
            if (!is_subclass_of($class, QueryCustomizer::class)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, QueryCustomizer::class));
            }

            $queries->addMethodCall('addCustomizer', [new Reference($id)]);
        }
    }
}
