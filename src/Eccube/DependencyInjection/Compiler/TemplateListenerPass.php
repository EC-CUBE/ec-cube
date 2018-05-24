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
use Symfony\Component\DependencyInjection\Reference;

class TemplateListenerPass implements CompilerPassInterface
{
    /**
     * `@Template`は, Sensio\Bundle\FrameworkExtraBundle\EventListener\TemplateListenerによって処理される
     * TemplateListenerが保持しているTwigEnvironmentをテンプレートフックポイントを実装したEccube\Twig\Environmentに差し替える
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $twig = new Reference('Eccube\Twig\Environment');
        $df = $container->getDefinition('sensio_framework_extra.view.listener');
        $df->replaceArgument(1, $twig);
    }
}
