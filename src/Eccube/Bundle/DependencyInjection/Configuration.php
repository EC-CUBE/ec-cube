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

namespace Eccube\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('eccube_rate_limiter');
        $treeBuilder->getRootNode()
            ->beforeNormalization()
                ->ifTrue(fn ($v) => \is_array($v) && !isset($v['limiters']) && !isset($v['limiter']))
                ->then(fn (array $v) => ['limiters' => $v])
            ->end()
            ->children()
                ->arrayNode('limiters')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('route')
                                ->defaultNull()
                            ->end()
                            ->integerNode('limit')
                                ->isRequired()
                            ->end()
                            ->scalarNode('interval')
                                ->isRequired()
                            ->end()
                            ->enumNode('type')
                                ->values(['ip', 'customer'])
                                ->defaultNull()
                            ->end()
                            ->arrayNode('method')
                                ->beforeNormalization()
                                    ->ifTrue(fn ($v) => \is_array($v))
                                    ->then(fn (array $v) => \array_map(fn ($method) => \strtoupper($method), $v))
                                ->end()
                                ->enumPrototype()->values(['GET', 'POST', 'PUT', 'DELETE'])->end()
                                ->defaultValue(['POST'])
                            ->end()
                            ->scalarNode('mode')
                                ->defaultNull()
                            ->end()
                        ->end() // children
                    ->end() // arrayPrototype
                ->end() // arrayNode('limiters')
            ->end() // children
        ->end() // getRootNode
        ;

        return $treeBuilder;
    }
}
