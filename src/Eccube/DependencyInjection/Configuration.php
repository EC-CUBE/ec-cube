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

namespace Eccube\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('eccube');
        $rootNode = $treeBuilder->getRootNode();

        $this->addRateLimiterSection($rootNode);

        return $treeBuilder;
    }

    public function addRateLimiterSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('rate_limiter')
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
                                    ->arrayNode('type')
                                        ->beforeNormalization()
                                            ->ifString()
                                            ->then(fn (string $v) => [$v])
                                        ->end()
                                        ->beforeNormalization()
                                            ->ifArray()
                                            ->then(fn (array $v) => \array_map(fn ($method) => \strtolower($method), $v))
                                        ->end()
                                    ->enumPrototype()->values(['ip', 'customer', 'user'])->end()
                                        ->defaultValue([])
                                    ->end()
                                    ->arrayNode('method')
                                        ->beforeNormalization()
                                            ->ifString()
                                            ->then(fn (string $v) => [$v])
                                        ->end()
                                        ->beforeNormalization()
                                            ->ifArray()
                                            ->then(fn (array $v) => \array_map(fn ($method) => \strtoupper($method), $v))
                                        ->end()
                                    ->enumPrototype()->values(['GET', 'POST', 'PUT', 'DELETE'])->end()
                                        ->defaultValue(['POST'])
                                    ->end()
                                    ->arrayNode('params')
                                        ->scalarPrototype()->end()
                                    ->end()
                                ->end() // children
                            ->end() // arrayPrototype
                        ->end() // arrayNode('limiters')
                    ->end() // children
                ->end() // arrayNode('rate_limiter')
            ->end() // children
        ;
    }
}
