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

use Eccube\Common\EccubeNav;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NavCompilerPass implements CompilerPassInterface
{
    const NAV_TAG = 'eccube.nav';

    public function process(ContainerBuilder $container)
    {
        $ids = $container->findTaggedServiceIds(self::NAV_TAG);
        $nav = $container->getParameter('eccube_nav');

        foreach ($ids as $id => $tags) {
            $def = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($def->getClass());
            if (!is_subclass_of($class, EccubeNav::class)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, EccubeNav::class));
            }

            /** @var $class EccubeNav */
            $addNav = $class::getNav();
            $nav = array_replace_recursive($nav, $addNav);
        }

        $container->setParameter('eccube_nav', $nav);
    }
}
