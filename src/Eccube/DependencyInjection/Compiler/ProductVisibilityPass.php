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
use Symfony\Component\DependencyInjection\Reference;

class ProductVisibilityPass implements CompilerPassInterface
{
    const TAG = 'eccube.product.visibility';

    public function process(ContainerBuilder $container)
    {
        $visibilitiesDef = $container->getDefinition('eccube.product.visibilities');
        $ids = $container->findTaggedServiceIds(self::TAG);
        foreach ($ids as $id => $tag) {
            $visibilitiesDef->addMethodCall('append', [new Reference($id)]);
        }
    }
}
