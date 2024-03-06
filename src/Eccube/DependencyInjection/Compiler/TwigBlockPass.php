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

use Eccube\Common\EccubeTwigBlock;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigBlockPass implements CompilerPassInterface
{
    public const TWIG_BLOCK_TAG = 'eccube.twig_block';

    public function process(ContainerBuilder $container)
    {
        $ids = $container->findTaggedServiceIds(self::TWIG_BLOCK_TAG);
        $templates = $container->getParameter('eccube_twig_block_templates');

        foreach ($ids as $id => $tags) {
            $def = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($def->getClass());
            if (!is_subclass_of($class, EccubeTwigBlock::class)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, EccubeTwigBlock::class));
            }

            /** @var $class EccubeTwigBlock */
            $blocks = $class::getTwigBlock();
            foreach ($blocks as $block) {
                $templates[] = $block;
            }
        }

        $container->setParameter('eccube_twig_block_templates', $templates);
    }
}
